<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('account.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt([...$credentials, 'is_active' => true], $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'))->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('account.index');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => strtolower($request->input('email')),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'addresses' => [],
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Account created successfully! Welcome to NovaMart.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logged out successfully.');
    }

    /**
     * Google / Gmail One-Tap & OAuth Handler
     */
    public function googleRedirect()
    {
        $enabled = \App\Models\Setting::get('google_login_enabled');
        if ($enabled !== '1') {
            return redirect()->route('login')->with('error', 'Google Sign-In is currently disabled in store configuration.');
        }

        $clientId = \App\Models\Setting::get('google_client_id');
        $redirectUri = \App\Models\Setting::get('google_redirect_uri') ?: route('auth.google.callback');

        // If mock client id or empty, use simulated authentication (local only)
        if (empty($clientId) || str_contains($clientId, 'mockclientid')) {
            if (app()->environment('local')) {
                return redirect()->route('auth.google.simulate');
            }
            return redirect()->route('login')->with('error', 'Google Sign-In is not configured.');
        }

        $params = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'prompt' => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $params);
    }

    public function googleCallback(Request $request)
    {
        $enabled = \App\Models\Setting::get('google_login_enabled');
        if ($enabled !== '1') {
            return redirect()->route('login')->with('error', 'Google Sign-In is disabled.');
        }

        $code = $request->input('code');
        $clientId = \App\Models\Setting::get('google_client_id');
        $clientSecret = \App\Models\Setting::get('google_client_secret');
        $redirectUri = \App\Models\Setting::get('google_redirect_uri') ?: route('auth.google.callback');

        if ($code && !empty($clientId) && !empty($clientSecret) && !str_contains($clientId, 'mockclientid')) {
            try {
                $response = \Illuminate\Support\Facades\Http::asForm()->post('https://oauth2.googleapis.com/token', [
                    'code' => $code,
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri' => $redirectUri,
                    'grant_type' => 'authorization_code',
                ]);

                if ($response->successful()) {
                    $tokenData = $response->json();
                    $idToken = $tokenData['id_token'] ?? null;
                    $accessToken = $tokenData['access_token'] ?? null;

                    $userProfile = \Illuminate\Support\Facades\Http::withToken($accessToken)
                        ->get('https://www.googleapis.com/oauth2/v3/userinfo')
                        ->json();

                    $email = $userProfile['email'] ?? null;
                    $name = $userProfile['name'] ?? 'Google User';
                    $googleId = $userProfile['sub'] ?? ('g_' . Str::random(12));
                    $avatar = $userProfile['picture'] ?? null;

                    if ($email) {
                        $user = User::where('email', $email)->orWhere('google_id', $googleId)->first();
                        if (!$user) {
                            $user = User::create([
                                'name' => $name,
                                'email' => $email,
                                'password' => Hash::make(Str::random(24)),
                                'google_id' => $googleId,
                                'avatar' => $avatar,
                                'role' => 'customer',
                                'is_active' => true,
                                'email_verified_at' => now(),
                                'addresses' => [],
                            ]);
                        } else {
                            $user->update([
                                'google_id' => $googleId,
                                'avatar' => $avatar ?: $user->avatar,
                            ]);
                        }

                        if (!$user->is_active) {
                            return redirect()->route('login')->with('error', 'Your account has been disabled. Please contact support.');
                        }

                        Auth::login($user);
                        return redirect()->intended(route('home'))->with('success', 'Logged in successfully via Google!');
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Google OAuth callback error: ' . $e->getMessage());
            }
        }

        // Fallback to simulated sign in if live exchange failed or test mode (local only)
        if (app()->environment('local')) {
            return redirect()->route('auth.google.simulate');
        }

        return redirect()->route('login')->with('error', 'Google Sign-In failed. Please try again.');
    }

    public function googleSimulate(Request $request)
    {
        // Passwordless login by email: never reachable outside local development
        abort_unless(app()->environment('local'), 404);

        $enabled = \App\Models\Setting::get('google_login_enabled');
        if ($enabled !== '1') {
            return redirect()->route('login')->with('error', 'Google Sign-In is disabled.');
        }

        $email = $request->input('email', 'demo.user@gmail.com');
        $name = $request->input('name', 'Google User');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(16)),
                'google_id' => 'g_' . Str::random(12),
                'avatar' => 'https://lh3.googleusercontent.com/a/default-user=s96-c',
                'role' => 'customer',
                'is_active' => true,
                'email_verified_at' => now(),
                'addresses' => [],
            ]);
        }

        if (!$user->is_active) {
            return redirect()->route('login')->with('error', 'Your account has been disabled. Please contact support.');
        }

        Auth::login($user);

        return redirect()->intended(route('home'))->with('success', 'Signed in successfully via Google / Gmail account (' . $email . ')!');
    }
}

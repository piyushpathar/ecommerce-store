<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please sign in to access the control panel.');
        }

        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access: Admin privileges required.');
        }

        return $next($request);
    }
}

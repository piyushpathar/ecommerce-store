@extends('layouts.app')

@section('title', 'Sign In | NovaMart')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-8 border border-slate-200 dark:border-white/10 shadow-pop space-y-6">

        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-brand-600 flex items-center justify-center text-white font-mono font-black text-xl shadow-glow mx-auto">NM</div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Welcome Back</h1>
            <p class="text-xs text-slate-500">Sign in to your NovaMart account</p>
        </div>

        @if(\App\Models\Setting::get('google_login_enabled') === '1')
        <!-- 1-Click Gmail Login Button -->
        <a href="{{ route('auth.google') }}" class="w-full py-3 px-4 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 text-xs font-bold text-slate-800 dark:text-white flex items-center justify-center gap-3 transition-colors shadow-xs">
            <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.15z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27A7.19 7.19 0 0 1 4.9 12c0-.79.14-1.57.38-2.27V6.58H1.25A11.97 11.97 0 0 0 0 12c0 1.92.45 3.74 1.25 5.42l4.03-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/></svg>
            <span>Continue with Google / Gmail</span>
        </a>

        <div class="relative flex py-1 items-center">
            <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
            <span class="flex-shrink mx-4 text-slate-400 text-[11px] uppercase font-mono">or email sign in</span>
            <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
        </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4 text-xs">
            @csrf

            @if($errors->any())
            <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-300 dark:border-rose-800 text-rose-600 text-xs">
                {{ $errors->first() }}
            </div>
            @endif

            <div>
                <label class="font-bold text-slate-700 dark:text-slate-300">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full mt-1 p-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl font-mono text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="font-bold text-slate-700 dark:text-slate-300">Password</label>
                <input type="password" name="password" required class="w-full mt-1 p-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="text-brand-600 rounded">
                    <span class="text-slate-600 dark:text-slate-400">Remember me</span>
                </label>
                <span class="text-slate-400">Default: Password@123</span>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-glow transition-all">
                Sign In
            </button>
        </form>

        <div class="text-center text-xs text-slate-500 border-t border-slate-100 dark:border-white/10 pt-4">
            Don't have an account yet?
            <a href="{{ route('register') }}" class="text-brand-600 dark:text-brand-400 font-bold hover:underline ml-1">Create one here</a>
        </div>

        <!-- Quick Demo Sign-ins Pill Box -->
        @env('local')
        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 text-[11px] text-slate-500 space-y-1">
            <span class="font-bold uppercase tracking-wider text-slate-400 block text-[10px]">Test Accounts:</span>
            <div class="flex justify-between">
                <span>Admin Login:</span>
                <span class="font-mono text-brand-600">admin@novamart.com</span>
            </div>
            <div class="flex justify-between">
                <span>Customer Login:</span>
                <span class="font-mono text-brand-600">demo@novamart.com</span>
            </div>
        </div>
        @endenv

    </div>
</div>
@endsection

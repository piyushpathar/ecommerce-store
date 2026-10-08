@extends('layouts.app')

@section('title', 'Create Account | NovaMart')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-8 border border-slate-200 dark:border-white/10 shadow-pop space-y-6">

        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-brand-600 flex items-center justify-center text-white font-mono font-black text-xl shadow-glow mx-auto">NM</div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Create Account</h1>
            <p class="text-xs text-slate-500">Join NovaMart for 1-day delivery and order tracking</p>
        </div>

        @if(\App\Models\Setting::get('google_login_enabled') === '1')
        <a href="{{ route('auth.google') }}" class="w-full py-3 px-4 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 text-xs font-bold text-slate-800 dark:text-white flex items-center justify-center gap-3 transition-colors shadow-xs">
            <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.15z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27A7.19 7.19 0 0 1 4.9 12c0-.79.14-1.57.38-2.27V6.58H1.25A11.97 11.97 0 0 0 0 12c0 1.92.45 3.74 1.25 5.42l4.03-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/></svg>
            <span>Sign Up with Google / Gmail</span>
        </a>

        <div class="relative flex py-1 items-center">
            <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
            <span class="flex-shrink mx-4 text-slate-400 text-[11px] uppercase font-mono">or email registration</span>
            <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4 text-xs">
            @csrf

            @if($errors->any())
            <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-300 dark:border-rose-800 text-rose-600 text-xs">
                {{ $errors->first() }}
            </div>
            @endif

            <div>
                <label class="font-bold text-slate-700 dark:text-slate-300">Your Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full mt-1 p-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="font-bold text-slate-700 dark:text-slate-300">Email Address *</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full mt-1 p-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl font-mono text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="font-bold text-slate-700 dark:text-slate-300">Mobile Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full mt-1 p-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl font-mono text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="font-bold text-slate-700 dark:text-slate-300">Password * (min 8 characters)</label>
                <input type="password" name="password" required class="w-full mt-1 p-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="font-bold text-slate-700 dark:text-slate-300">Confirm Password *</label>
                <input type="password" name="password_confirmation" required class="w-full mt-1 p-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <button type="submit" class="w-full py-3.5 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-glow transition-all">
                Create Account
            </button>
        </form>

        <div class="text-center text-xs text-slate-500 border-t border-slate-100 dark:border-white/10 pt-4">
            Already have an account?
            <a href="{{ route('login') }}" class="text-brand-600 dark:text-brand-400 font-bold hover:underline ml-1">Sign in here</a>
        </div>

    </div>
</div>
@endsection

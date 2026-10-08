<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Control Panel | NovaMart')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-[#070d1a] text-[#f1f5f9] min-h-screen font-sans antialiased flex flex-col md:flex-row transition-colors">

    <!-- Admin Sidebar -->
    <aside class="w-full md:w-64 bg-[#0c1117] border-r border-white/10 flex flex-col shrink-0">
        <!-- Logo -->
        <div class="p-6 border-b border-white/10 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-teal-400 flex items-center justify-center text-white font-mono font-bold text-lg shadow-glow">
                    NM
                </div>
                <div>
                    <span class="font-extrabold text-base tracking-tight text-white flex items-center gap-1.5">
                        NOVA<span class="text-brand-400">MART</span>
                    </span>
                    <span class="text-[10px] text-brand-400 font-mono font-bold tracking-wider uppercase block">CONTROL PANEL</span>
                </div>
            </a>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-6 space-y-1 text-sm font-medium">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-brand-500/15 text-brand-400 font-semibold border border-brand-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                <span>Dashboard Overview</span>
            </a>

            <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Catalog & Commerce</div>

            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-brand-500/15 text-brand-400 font-semibold border border-brand-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="box" class="w-4 h-4"></i>
                <span>Products Catalog</span>
            </a>

            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-brand-500/15 text-brand-400 font-semibold border border-brand-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="folder-tree" class="w-4 h-4"></i>
                <span>Categories</span>
            </a>

            <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-brand-500/15 text-brand-400 font-semibold border border-brand-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                    <span>Orders & Fulfillment</span>
                </div>
                @php $pendingCount = \App\Models\Order::where('status', 'pending')->count(); @endphp
                @if($pendingCount > 0)
                <span class="bg-amber-500/20 text-amber-400 text-[10px] font-mono px-2 py-0.5 rounded-full font-bold">{{ $pendingCount }}</span>
                @endif
            </a>

            <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Promotions & Marketing</div>

            <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.coupons.*') ? 'bg-brand-500/15 text-brand-400 font-semibold border border-brand-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="tag" class="w-4 h-4"></i>
                <span>Coupons & Promos</span>
            </a>

            <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">System & Users</div>

            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-brand-500/15 text-brand-400 font-semibold border border-brand-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="users" class="w-4 h-4"></i>
                <span>Users & Customers</span>
            </a>

            <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">CMS & Site Design</div>

            <a href="{{ route('admin.pages.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.pages.*') ? 'bg-brand-500/15 text-brand-400 font-semibold border border-brand-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span>Store Pages & Policies</span>
            </a>

            <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-brand-500/15 text-brand-400 font-semibold border border-brand-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="palette" class="w-4 h-4"></i>
                <span>Site Customizer & Visuals</span>
            </a>
        </nav>

        <!-- Bottom Actions -->
        <div class="p-4 border-t border-white/10 space-y-2">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-brand-400 hover:bg-white/5 transition-colors">
                <span class="flex items-center gap-2">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    <span>Open Live Storefront</span>
                </span>
                <span class="text-[10px] font-mono">&rarr;</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-rose-400 hover:bg-rose-500/10 transition-colors">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    <span>Admin Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Admin Header Bar -->
        <header class="h-16 border-b border-white/10 bg-[#0c1117] px-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h1 class="text-base font-bold text-white tracking-tight">@yield('page_title', 'Control Panel')</h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-800/40">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>MySQL 8.x Engine Active</span>
                </span>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-xs font-mono text-slate-400 hidden sm:inline">{{ date('D, d M Y') }}</span>
                <div class="flex items-center gap-2 pl-3 border-l border-white/10">
                    <div class="w-7 h-7 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-xs ring-1 ring-white/20">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="text-xs font-medium text-slate-200 hidden sm:inline">{{ Auth::user()->name ?? 'Admin' }}</span>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mx-6 mt-6 p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/40 text-emerald-200 flex items-center gap-3 text-sm">
            <i data-lucide="check-circle" class="w-5 h-5 text-brand-400 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="mx-6 mt-6 p-4 rounded-xl bg-rose-950/40 border border-rose-500/40 text-rose-200 flex items-center gap-3 text-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-400 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <!-- Main Body -->
        <main class="flex-1 p-6 sm:p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>

@extends('layouts.admin')

@section('page_title', 'Analytics & Control Dashboard')

@section('content')
<div class="space-y-8">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Revenue -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-medium">Total Gross Revenue</span>
                <i data-lucide="indian-rupee" class="w-4 h-4 text-brand-400"></i>
            </div>
            <div class="text-3xl font-black font-mono text-white tracking-tight">
                ₹{{ number_format($totalRevenue, 2) }}
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-xs text-brand-400 font-semibold font-mono">
                <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                <span>+24.8% vs last week</span>
            </div>
        </div>

        <!-- Orders -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-medium">Total Orders</span>
                <i data-lucide="shopping-bag" class="w-4 h-4 text-sky-400"></i>
            </div>
            <div class="text-3xl font-black font-mono text-white tracking-tight">
                {{ $totalOrders }}
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-400 font-mono">
                <span>Across physical & signals</span>
            </div>
        </div>

        <!-- Average Order Value -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-medium">Average Order Value</span>
                <i data-lucide="calculator" class="w-4 h-4 text-amber-400"></i>
            </div>
            <div class="text-3xl font-black font-mono text-white tracking-tight">
                ₹{{ number_format($avgOrderValue, 2) }}
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-400 font-mono">
                <span>Healthy basket size</span>
            </div>
        </div>

        <!-- Customers -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-medium">Registered Customers</span>
                <i data-lucide="users" class="w-4 h-4 text-emerald-400"></i>
            </div>
            <div class="text-3xl font-black font-mono text-white tracking-tight">
                {{ $totalCustomers }}
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-xs text-brand-400 font-mono">
                <span>100% Verified Users</span>
            </div>
        </div>
    </div>

    <!-- Inventory Alerts & Top Products -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Recent Orders (8 cols) -->
        <div class="lg:col-span-8 p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 text-brand-400"></i>
                    <span>Recent Customer Orders</span>
                </h3>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-brand-400 hover:underline">View all orders &rarr;</a>
            </div>

            @if($recentOrders->isEmpty())
            <div class="py-8 text-center text-xs text-slate-500">No customer orders placed yet.</div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-500 border-b border-white/5 uppercase font-mono text-[10px]">
                            <th class="pb-2.5">Order ID</th>
                            <th class="pb-2.5">Customer</th>
                            <th class="pb-2.5">Amount</th>
                            <th class="pb-2.5">Status</th>
                            <th class="pb-2.5">Date</th>
                            <th class="pb-2.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 font-mono">
                        @foreach($recentOrders as $ro)
                        <tr>
                            <td class="py-3 font-bold text-white">{{ $ro->order_number }}</td>
                            <td class="py-3 font-sans text-slate-300">{{ $ro->customer_name }}</td>
                            <td class="py-3 font-bold text-brand-400">{{ $ro->formatted_total }}</td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $ro->status_badge_class }}">
                                    {{ $ro->status }}
                                </span>
                            </td>
                            <td class="py-3 text-slate-400 text-[11px]">{{ $ro->created_at->format('d M, h:i A') }}</td>
                            <td class="py-3 text-right font-sans">
                                <a href="{{ route('admin.orders.show', $ro->_id) }}" class="px-2.5 py-1 rounded-lg bg-white/5 hover:bg-brand-600 text-slate-200 hover:text-white text-[11px] font-semibold transition-colors">
                                    Manage
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Low Stock Alerts & System Overview (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Low Stock Warnings -->
            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400 flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    <span>Low Inventory Alerts</span>
                </h3>

                <div class="space-y-2.5 text-xs">
                    @forelse($lowStockProducts as $lsp)
                    <div class="flex items-center justify-between p-2 rounded-xl bg-white/5">
                        <div class="flex-1 min-w-0 pr-2">
                            <h4 class="text-xs font-semibold text-slate-200 truncate">{{ $lsp->title }}</h4>
                            <span class="text-[10px] text-slate-500 font-mono">SKU: {{ $lsp->sku }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono bg-rose-500/20 text-rose-400">
                            {{ $lsp->stock }} left
                        </span>
                    </div>
                    @empty
                    <p class="text-xs text-slate-500">All hardware inventory healthy.</p>
                    @endforelse
                </div>
            </div>

            <!-- Fast Quick Links -->
            <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Quick Store Actions</h3>
                <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                    <a href="{{ route('admin.products.create') }}" class="p-3 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white text-center flex flex-col items-center gap-1.5 transition-all">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Add Product</span>
                    </a>
                    <a href="{{ route('admin.coupons.index') }}" class="p-3 rounded-2xl bg-white/5 hover:bg-white/10 text-slate-200 text-center flex flex-col items-center gap-1.5 transition-all">
                        <i data-lucide="tag" class="w-4 h-4 text-brand-400"></i>
                        <span>New Promo</span>
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

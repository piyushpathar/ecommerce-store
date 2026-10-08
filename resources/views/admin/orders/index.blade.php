@extends('layouts.admin')

@section('page_title', 'Orders & Fulfillment')

@section('content')
<div class="space-y-6">

    <!-- Header & Filter Tabs -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Customer Orders</h2>
            <p class="text-xs text-slate-400">Track order processing, dispatch couriers, and manage customer shipments</p>
        </div>

        <span class="text-xs font-mono font-bold text-brand-400 bg-brand-950/60 px-3 py-1.5 rounded-xl border border-brand-800/40">
            Total Orders: {{ $counts['all'] }}
        </span>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 border-b border-white/10 overflow-x-auto pb-1 text-xs font-bold">
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2.5 rounded-t-xl transition-all whitespace-nowrap {{ !$status ? 'bg-white/10 text-white border-b-2 border-brand-500' : 'text-slate-400 hover:text-white' }}">
            All ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="px-4 py-2.5 rounded-t-xl transition-all whitespace-nowrap {{ $status === 'pending' ? 'bg-white/10 text-white border-b-2 border-brand-500' : 'text-slate-400 hover:text-white' }}">
            Pending ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" class="px-4 py-2.5 rounded-t-xl transition-all whitespace-nowrap {{ $status === 'confirmed' ? 'bg-white/10 text-white border-b-2 border-brand-500' : 'text-slate-400 hover:text-white' }}">
            Confirmed ({{ $counts['confirmed'] }})
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" class="px-4 py-2.5 rounded-t-xl transition-all whitespace-nowrap {{ $status === 'shipped' ? 'bg-white/10 text-white border-b-2 border-brand-500' : 'text-slate-400 hover:text-white' }}">
            Shipped ({{ $counts['shipped'] }})
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="px-4 py-2.5 rounded-t-xl transition-all whitespace-nowrap {{ $status === 'delivered' ? 'bg-white/10 text-white border-b-2 border-brand-500' : 'text-slate-400 hover:text-white' }}">
            Delivered ({{ $counts['delivered'] }})
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="px-4 py-2.5 rounded-t-xl transition-all whitespace-nowrap {{ $status === 'cancelled' ? 'bg-white/10 text-white border-b-2 border-brand-500' : 'text-slate-400 hover:text-white' }}">
            Cancelled ({{ $counts['cancelled'] }})
        </a>
    </div>

    <!-- Search input -->
    <div class="p-4 rounded-2xl bg-[#0c1117] border border-white/10 flex items-center justify-between text-xs">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex-1 max-w-md flex gap-2">
            @if($status)
            <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by order ID, customer name, email..." class="flex-1 px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            <button type="submit" class="px-4 py-2 bg-white/10 hover:bg-white/15 text-white font-bold rounded-xl">Search</button>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-white/10 uppercase font-mono text-[10px] bg-white/[0.02]">
                        <th class="p-4">Order ID</th>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Items</th>
                        <th class="p-4">Total Amount</th>
                        <th class="p-4">Payment</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Placed Date</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($orders as $o)
                    <tr class="hover:bg-white/[0.01] transition-colors">
                        <td class="p-4 font-mono font-bold text-white">
                            <a href="{{ route('admin.orders.show', $o->_id) }}" class="hover:text-brand-400">{{ $o->order_number }}</a>
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-slate-200">{{ $o->customer_name }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $o->customer_email }}</div>
                        </td>
                        <td class="p-4 font-mono">{{ count($o->items ?? []) }} item(s)</td>
                        <td class="p-4 font-mono font-bold text-brand-400">{{ $o->formatted_total }}</td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase font-mono {{ $o->payment_status === 'paid' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">
                                {{ $o->payment_method }} &bull; {{ $o->payment_status }}
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase font-mono border {{ $o->status_badge_class }}">
                                {{ $o->status }}
                            </span>
                        </td>
                        <td class="p-4 text-slate-400 font-mono text-[11px]">{{ $o->created_at->format('d M, h:i A') }}</td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.orders.show', $o->_id) }}" class="px-3 py-1.5 rounded-xl bg-white/5 hover:bg-brand-600 text-slate-200 hover:text-white font-bold transition-colors inline-block">
                                View & Fulfill
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-slate-500">No orders found matching status filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-white/5">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection

@extends('layouts.admin')

@section('page_title', 'Promotions & Coupons')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white">Promotional Coupons</h2>
            <p class="text-xs text-slate-400">Create discount codes, set usage limits, and run seasonal campaigns</p>
        </div>

        <button type="button" @click="showModal = true" class="px-5 py-2.5 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs flex items-center gap-2 shadow-glow transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add New Coupon</span>
        </button>
    </div>

    <!-- Coupons Table -->
    <div class="rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-white/10 uppercase font-mono text-[10px] bg-white/[0.02]">
                        <th class="p-4">Coupon Code</th>
                        <th class="p-4">Discount</th>
                        <th class="p-4">Min Spend</th>
                        <th class="p-4">Used / Limit</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 font-mono">
                    @foreach($coupons as $cp)
                    <tr class="hover:bg-white/[0.01] transition-colors">
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-lg bg-brand-500/10 text-brand-400 font-bold border border-brand-500/20">
                                {{ $cp->code }}
                            </span>
                        </td>
                        <td class="p-4 text-white font-bold">
                            {{ $cp->discount_type === 'percentage' ? $cp->discount_value . '%' : '₹' . number_format($cp->discount_value, 2) }}
                        </td>
                        <td class="p-4 text-slate-400">
                            {{ $cp->min_spend ? '₹' . number_format($cp->min_spend, 2) : 'No min' }}
                        </td>
                        <td class="p-4 text-slate-300">
                            {{ $cp->used_count }} / {{ $cp->usage_limit ?: 'Unlimited' }}
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $cp->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-500/20 text-slate-400' }}">
                                {{ $cp->is_active ? 'Active' : 'Expired' }}
                            </span>
                        </td>
                        <td class="p-4 text-right font-sans">
                            <form action="{{ route('admin.coupons.destroy', $cp->_id) }}" method="POST" onsubmit="return confirm('Delete this coupon?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-rose-400 hover:bg-rose-500/10 rounded-lg">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal to Add Coupon -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-[#0c1117] rounded-3xl p-6 sm:p-8 max-w-lg w-full border border-white/10 shadow-pop space-y-4" @click.outside="showModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                <h3 class="text-base font-bold text-white">Create New Coupon Code</h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="font-bold text-slate-300">Coupon Promo Code *</label>
                    <input type="text" name="code" required placeholder="e.g. FESTIVAL20" class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white font-mono uppercase">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-300">Discount Type *</label>
                        <select name="discount_type" class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₹)</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-bold text-slate-300">Discount Value *</label>
                        <input type="number" step="0.01" name="discount_value" required placeholder="10 or 500" class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-300">Min Order Spend (₹)</label>
                        <input type="number" step="0.01" name="min_spend" placeholder="e.g. 999" class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300">Max Discount Limit (₹)</label>
                        <input type="number" step="0.01" name="max_discount" placeholder="e.g. 1500" class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-300">Total Usage Limit</label>
                    <input type="number" name="usage_limit" placeholder="e.g. 500" class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-white/10">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold">Save Coupon</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

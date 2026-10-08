@extends('layouts.admin')

@section('page_title', 'Pricing Plans & Subscriptions')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white">NovaMart VIP & Membership Plans</h2>
            <p class="text-xs text-slate-400">Configure free trial passes, monthly VIP, and annual prime membership tiers</p>
        </div>

        <button type="button" @click="showModal = true" class="px-5 py-2.5 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs flex items-center gap-2 shadow-glow transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add New Plan</span>
        </button>
    </div>

    <!-- Plans Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($plans as $plan)
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase font-mono text-brand-400">{{ $plan->badge ?: 'PLAN' }}</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $plan->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-500/20 text-slate-400' }}">
                        {{ $plan->is_active ? 'Active' : 'Disabled' }}
                    </span>
                </div>

                <h3 class="text-base font-bold text-white">{{ $plan->name }}</h3>
                <div class="mt-2 text-3xl font-black font-mono text-white">
                    {{ $plan->formatted_price }}
                    <span class="text-xs text-slate-400 font-sans font-normal">/ {{ $plan->interval }}</span>
                </div>
                <p class="text-[11px] text-slate-500 font-mono mt-0.5">Duration: {{ $plan->duration_days }} day(s)</p>

                <div class="mt-4 pt-4 border-t border-white/5 space-y-1.5 text-xs text-slate-300">
                    @foreach($plan->features ?? [] as $f)
                    <div class="flex items-start gap-2">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-brand-400 mt-0.5 shrink-0"></i>
                        <span class="text-[11px] line-clamp-1">{{ $f }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-white/5 flex justify-end">
                <form action="{{ route('admin.plans.destroy', $plan->_id) }}" method="POST" onsubmit="return confirm('Delete this plan?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-rose-400 hover:underline">Delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal to Add Plan -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-[#0c1117] rounded-3xl p-6 sm:p-8 max-w-lg w-full border border-white/10 shadow-pop space-y-4" @click.outside="showModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                <h3 class="text-base font-bold text-white">Create New Subscription Plan</h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('admin.plans.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="font-bold text-slate-300">Plan Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Weekly VIP Scalper" class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white">
                </div>

                <div class="grid grid-cols-2 gap-3 font-mono">
                    <div>
                        <label class="font-bold text-slate-300">Price (₹) *</label>
                        <input type="number" name="price" required placeholder="199" class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300">Duration (Days) *</label>
                        <input type="number" name="duration_days" value="5" required class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-300">Billing Interval *</label>
                        <select name="interval" class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white">
                            <option value="daily">Daily</option>
                            <option value="weekly" selected>Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="annual">Annual</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-bold text-slate-300">Badge Text (Optional)</label>
                        <input type="text" name="badge" placeholder="e.g. Popular, Scalper" class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-300">Features List (One per line) *</label>
                    <textarea name="features" rows="4" required placeholder="1 Daily call for today&#10;Live PCR Dashboard&#10;Telegram alert on trigger" class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white font-mono text-xs"></textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-300">Sort Order</label>
                    <input type="number" name="sort_order" value="5" class="w-full mt-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_trial" value="1" class="text-brand-600 rounded">
                        <span class="text-slate-300 font-medium">Trial Plan</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_popular" value="1" class="text-brand-600 rounded">
                        <span class="text-slate-300 font-medium">Popular Highlight</span>
                    </label>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-white/10">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold">Create Plan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

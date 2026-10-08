@extends('layouts.app')

@section('title', 'My Account | NovaMart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
     x-data="{ activeTab: '{{ $tab }}' }">

    <!-- Account Welcome Banner -->
    <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-white/10 shadow-soft mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-600 to-teal-400 text-white font-extrabold text-2xl flex items-center justify-center font-mono shadow-glow">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ $user->name }}</h1>
                <p class="text-xs text-slate-500 font-mono">{{ $user->email }} &bull; Customer Account</p>
            </div>
        </div>

        @if($user->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-xl bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/60 dark:hover:bg-brand-900/60 text-brand-700 dark:text-brand-300 font-bold text-xs border border-brand-200 dark:border-brand-800 flex items-center gap-2">
            <i data-lucide="shield-check" class="w-4 h-4"></i>
            <span>Open Admin Control Panel</span>
        </a>
        @endif
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-white/10 mb-8 overflow-x-auto pb-1 text-xs font-bold">
        <button @click="activeTab = 'orders'"
                :class="{ 'text-brand-600 dark:text-brand-400 border-brand-500 border-b-2 pb-3': activeTab === 'orders', 'text-slate-500 hover:text-slate-900 dark:hover:text-white pb-3': activeTab !== 'orders' }"
                class="px-4 transition-all flex items-center gap-2 whitespace-nowrap">
            <i data-lucide="package" class="w-4 h-4"></i>
            <span>My Orders ({{ $orders->count() }})</span>
        </button>

        <button @click="activeTab = 'addresses'"
                :class="{ 'text-brand-600 dark:text-brand-400 border-brand-500 border-b-2 pb-3': activeTab === 'addresses', 'text-slate-500 hover:text-slate-900 dark:hover:text-white pb-3': activeTab !== 'addresses' }"
                class="px-4 transition-all flex items-center gap-2 whitespace-nowrap">
            <i data-lucide="map-pin" class="w-4 h-4"></i>
            <span>Saved Addresses ({{ count($user->addresses ?? []) }})</span>
        </button>

        <button @click="activeTab = 'profile'"
                :class="{ 'text-brand-600 dark:text-brand-400 border-brand-500 border-b-2 pb-3': activeTab === 'profile', 'text-slate-500 hover:text-slate-900 dark:hover:text-white pb-3': activeTab !== 'profile' }"
                class="px-4 transition-all flex items-center gap-2 whitespace-nowrap">
            <i data-lucide="user" class="w-4 h-4"></i>
            <span>Profile Settings</span>
        </button>
    </div>

    <!-- TAB 1: My Orders -->
    <div x-show="activeTab === 'orders'" class="space-y-4">
        @if($orders->isEmpty())
        <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-12 text-center border border-slate-200 dark:border-white/10 shadow-soft space-y-3">
            <i data-lucide="package" class="w-12 h-12 text-slate-400 mx-auto"></i>
            <h3 class="text-base font-bold text-slate-800 dark:text-white">No orders placed yet</h3>
            <p class="text-xs text-slate-500">Discover flagship smartphones, creator laptops, audio gear, and lifestyle products.</p>
            <a href="{{ route('shop.index') }}" class="inline-block mt-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white text-xs font-bold">Start Shopping</a>
        </div>
        @else
        <div class="space-y-4">
            @foreach($orders as $ord)
            <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-soft flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <!-- Info -->
                <div class="space-y-2 flex-1">
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-sm text-slate-900 dark:text-white">Order #{{ $ord->order_number }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase font-mono border {{ $ord->status_badge_class }}">
                            {{ ucfirst(str_replace('_', ' ', $ord->status)) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3 text-xs text-slate-500 font-mono">
                        <span>{{ $ord->created_at->format('d M Y') }}</span>
                        <span>&bull;</span>
                        <span>Total: <strong class="text-brand-600 dark:text-brand-400 font-bold">{{ $ord->formatted_total }}</strong></span>
                        <span>&bull;</span>
                        <span>{{ count($ord->items ?? []) }} item(s)</span>
                    </div>

                    <!-- Items mini thumb -->
                    <div class="flex items-center gap-2 pt-2">
                        @foreach(array_slice($ord->items ?? [], 0, 3) as $it)
                        <img src="{{ $it['image'] }}" class="w-10 h-10 rounded-lg object-cover bg-slate-100 dark:bg-white/5 border border-slate-200/40" title="{{ $it['title'] }}" alt="">
                        @endforeach
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('orders.show', $ord->order_number) }}" class="px-4 py-2 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-brand-600 dark:hover:bg-brand-400 font-bold text-xs transition-colors flex items-center gap-1.5">
                        <i data-lucide="truck" class="w-3.5 h-3.5"></i>
                        <span>Track Order & Invoice</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- TAB 2: Saved Addresses -->
    <div x-show="activeTab === 'addresses'" x-cloak class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($user->addresses ?? [] as $addr)
            <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-soft flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-sm text-slate-900 dark:text-white">{{ $addr['name'] }}</span>
                        <span class="text-[10px] font-mono uppercase font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300">
                            {{ $addr['address_type'] ?? 'home' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        {{ $addr['address_line1'] }}, {{ $addr['address_line2'] ?? '' }}<br>
                        {{ $addr['city'] }}, {{ $addr['state'] }} - <span class="font-mono font-bold">{{ $addr['pincode'] }}</span>
                    </p>
                    <p class="text-xs text-slate-400 font-mono mt-2">Phone: {{ $addr['phone'] }}</p>
                </div>

                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-white/5 flex justify-end">
                    <form action="{{ route('account.address.delete', $addr['id']) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-rose-600 font-semibold hover:underline flex items-center gap-1">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            <span>Delete Address</span>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Add Address Form -->
        <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-white/10 shadow-soft">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Add Another Delivery Address</h3>
            <form action="{{ route('account.address.add') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Full Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Rahul Sharma" class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500/30">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Mobile Phone *</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-slate-200 dark:border-white/10 bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300 font-mono text-xs font-semibold">+91</span>
                            <input type="tel" name="phone" required placeholder="98765 43210" maxlength="10" class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-r-xl text-xs font-mono text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500/30">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Flat / House No. / Building *</label>
                    <input type="text" name="address_line1" required placeholder="e.g. Flat 502, Tower 4, Orchid Heights" class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500/30">
                </div>

                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Street / Area / Sector</label>
                    <input type="text" name="address_line2" placeholder="e.g. SG Highway, Bodakdev / Link Road" class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500/30">
                </div>

                <!-- State & City Custom Searchable Comboboxes + Pincode -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <x-india-state-city />

                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Pincode *</label>
                        <input type="text" name="pincode" maxlength="6" required placeholder="e.g. 380015" class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-mono text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500/30">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Landmark (Optional)</label>
                        <input type="text" name="landmark" placeholder="e.g. Near Iscon Cross Road / Opposite Mall" class="w-full px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500/30">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Address Type</label>
                        <div class="grid grid-cols-3 gap-2 pt-0.5" x-data="{ addrType: 'home' }">
                            <label class="cursor-pointer">
                                <input type="radio" name="address_type" value="home" x-model="addrType" class="sr-only">
                                <div class="px-2 py-2 rounded-xl border text-center transition-all text-xs font-semibold"
                                     :class="addrType === 'home' ? 'bg-brand-50 dark:bg-brand-950/60 border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5'">
                                    🏠 Home
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="address_type" value="work" x-model="addrType" class="sr-only">
                                <div class="px-2 py-2 rounded-xl border text-center transition-all text-xs font-semibold"
                                     :class="addrType === 'work' ? 'bg-brand-50 dark:bg-brand-950/60 border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5'">
                                    🏢 Work
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="address_type" value="other" x-model="addrType" class="sr-only">
                                <div class="px-2 py-2 rounded-xl border text-center transition-all text-xs font-semibold"
                                     :class="addrType === 'other' ? 'bg-brand-50 dark:bg-brand-950/60 border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5'">
                                    📍 Other
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-emerald-600 hover:from-brand-600 hover:to-emerald-700 text-white font-bold text-xs shadow-glow transition-all hover:scale-[1.02] active:scale-[0.98] inline-flex items-center gap-2">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Save Delivery Address</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 3: Profile Settings -->
    <div x-show="activeTab === 'profile'" x-cloak class="max-w-xl">
        <div class="bg-white dark:bg-[#0f1723] rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-white/10 shadow-soft">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Update Profile Information</h3>
            <form action="{{ route('account.profile.update') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300">Full Name</label>
                    <input type="text" name="name" value="{{ $user->name }}" required class="w-full mt-1 p-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl">
                </div>

                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300">Email Address (Read-only)</label>
                    <input type="email" value="{{ $user->email }}" disabled class="w-full mt-1 p-2.5 bg-slate-100 dark:bg-white/10 border border-slate-200 dark:border-white/10 rounded-xl text-slate-400 font-mono">
                </div>

                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300">Phone Number</label>
                    <input type="text" name="phone" value="{{ $user->phone }}" class="w-full mt-1 p-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl font-mono">
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-white/5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">New Password (leave blank to keep unchanged)</label>
                    <input type="password" name="password" class="w-full mt-1 p-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl">
                </div>

                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="w-full mt-1 p-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl">
                </div>

                <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold transition-colors">
                    Update Profile
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

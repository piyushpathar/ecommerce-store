@extends('layouts.admin')

@section('page_title', 'Products Management')

@section('content')
<div class="space-y-6">

    <!-- Action Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Product Catalog</h2>
            <p class="text-xs text-slate-400">Manage electronics, smartphones, laptops, and marketplace items</p>
        </div>

        <a href="{{ route('admin.products.create') }}" class="px-5 py-2.5 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs flex items-center gap-2 shadow-glow transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add New Product</span>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="p-4 rounded-2xl bg-[#0c1117] border border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex-1 flex gap-3 w-full sm:w-auto">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by title, SKU, or brand..." class="flex-1 px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            <button type="submit" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/15 text-white font-bold">Search</button>
        </form>

        <span class="text-slate-400 font-mono">{{ $products->total() }} total product(s)</span>
    </div>

    <!-- Products Table -->
    <div class="rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-white/10 uppercase font-mono text-[10px] bg-white/[0.02]">
                        <th class="p-4">Product Details</th>
                        <th class="p-4">Category</th>
                        <th class="p-4">Price</th>
                        <th class="p-4">Stock</th>
                        <th class="p-4">Rating</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($products as $p)
                    <tr class="hover:bg-white/[0.01] transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $p->thumbnail }}" alt="" class="w-12 h-12 rounded-xl object-cover bg-black/20 shrink-0">
                                <div>
                                    <h4 class="font-bold text-white text-xs line-clamp-1 max-w-xs">{{ $p->title }}</h4>
                                    <div class="flex items-center gap-2 text-[11px] text-slate-400 font-mono mt-0.5">
                                        <span>{{ $p->brand }}</span>
                                        <span>&bull;</span>
                                        <span>SKU: {{ $p->sku }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-slate-300">{{ $p->category_name }}</td>
                        <td class="p-4 font-mono font-bold text-brand-400">
                            {{ $p->formatted_price }}
                        </td>
                        <td class="p-4 font-mono">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $p->stock <= 10 ? 'bg-amber-500/20 text-amber-400' : 'bg-emerald-500/20 text-emerald-400' }}">
                                {{ $p->stock }} units
                            </span>
                        </td>
                        <td class="p-4 font-mono text-amber-400 font-bold">
                            ★ {{ $p->rating_avg }} ({{ $p->rating_count }})
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.products.edit', $p->_id) }}" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white transition-colors" title="Edit">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $p->_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 transition-colors" title="Delete">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-white/5">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection

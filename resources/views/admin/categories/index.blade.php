@extends('layouts.admin')

@section('page_title', 'Category Taxonomy')

@section('content')
<div class="space-y-6" x-data="{ showModal: false, editMode: false, currentCategory: {} }">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white">Store Categories</h2>
            <p class="text-xs text-slate-400">Manage catalog hierarchy and storefront navigation</p>
        </div>

        <button type="button" @click="editMode = false; showModal = true" class="px-5 py-2.5 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs flex items-center gap-2 shadow-glow transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add New Category</span>
        </button>
    </div>

    <div class="rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-white/10 uppercase font-mono text-[10px] bg-white/[0.02]">
                        <th class="p-4">Category Details</th>
                        <th class="p-4">Slug</th>
                        <th class="p-4">Products</th>
                        <th class="p-4">Sort Order</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($categories as $cat)
                    <tr class="hover:bg-white/[0.01] transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-brand-500/10 text-brand-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="{{ $cat->icon ?: 'folder' }}" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-white text-xs">{{ $cat->name }}</h4>
                                    <p class="text-[11px] text-slate-400 line-clamp-1">{{ $cat->description }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 font-mono text-slate-400">{{ $cat->slug }}</td>
                        <td class="p-4 font-mono font-bold text-brand-400">{{ $cat->products_count }} items</td>
                        <td class="p-4 font-mono text-slate-300">#{{ $cat->sort_order }}</td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $cat->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-500/20 text-slate-400' }}">
                                {{ $cat->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('admin.categories.toggle', $cat->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 rounded bg-white/5 hover:bg-white/10 text-[10px] text-slate-300 transition-colors">
                                        {{ $cat->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Delete this category?');">
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
    </div>

    <!-- Modal to Add Category -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-[#0c1117] rounded-3xl p-6 sm:p-8 max-w-lg w-full border border-white/10 shadow-pop space-y-4" @click.outside="showModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                <h3 class="text-base font-bold text-white">Create New Category</h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="font-bold text-slate-300">Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Mechanical Keyboards" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">
                </div>

                <div>
                    <label class="font-bold text-slate-300">Description *</label>
                    <textarea name="description" rows="2" required placeholder="Short summary for SEO and category page header..." class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-300">Lucide Icon Name *</label>
                        <input type="text" name="icon" required placeholder="e.g. keyboard, monitor, cpu" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                    </div>
                    <div>
                        <label class="font-bold text-slate-300">Sort Order *</label>
                        <input type="number" name="sort_order" value="1" required class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-300">Banner / Header Image URL *</label>
                    <input type="url" name="image" required placeholder="https://images.unsplash.com/..." class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_featured" value="1" checked class="text-brand-600 rounded">
                    <span class="text-slate-300 font-medium">Show in Homepage Category Grid</span>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-white/10">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold">Create Category</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

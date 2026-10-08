@extends('layouts.admin')

@section('page_title', 'Edit Product #' . $product->sku)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white">Edit Product: {{ $product->title }}</h2>
            <p class="text-xs text-slate-400 font-mono">SKU: {{ $product->sku }}</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs text-slate-400 hover:text-white">&larr; Back to Products</a>
    </div>

    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" class="space-y-6 text-xs">
        @csrf
        @method('PUT')

        <!-- General Info Card -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Basic Information</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="font-bold text-slate-300">Product Title *</label>
                    <input type="text" name="title" required value="{{ old('title', $product->title) }}" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">
                </div>

                <div>
                    <label class="font-bold text-slate-300">Category *</label>
                    <select name="category_id" required class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-bold text-slate-300">Brand Name *</label>
                    <input type="text" name="brand" required value="{{ old('brand', $product->brand) }}" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">
                </div>

                <div>
                    <label class="font-bold text-slate-300">SKU / Model</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                </div>

                <div>
                    <label class="font-bold text-slate-300">Stock Units *</label>
                    <input type="number" name="stock" required value="{{ old('stock', $product->stock) }}" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                </div>
            </div>
        </div>

        <!-- Pricing Card -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Pricing (INR)</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 font-mono">
                <div>
                    <label class="font-bold text-slate-300">Selling Deal Price (₹) *</label>
                    <input type="number" step="0.01" name="price" required value="{{ old('price', $product->price) }}" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">
                </div>

                <div>
                    <label class="font-bold text-slate-300">Compare MRP Price (₹)</label>
                    <input type="number" step="0.01" name="compare_price" value="{{ old('compare_price', $product->compare_price) }}" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">
                </div>
            </div>
        </div>

        <!-- Media & Descriptions -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Media & Descriptions</h3>

            <div class="space-y-4">
                <div>
                    <label class="font-bold text-slate-300">Primary Thumbnail Image URL *</label>
                    <input type="url" name="thumbnail" required value="{{ old('thumbnail', $product->thumbnail) }}" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                </div>

                <div>
                    <label class="font-bold text-slate-300">Additional Gallery Images (Comma-separated)</label>
                    <input type="text" name="images" value="{{ old('images', implode(', ', $product->images ?? [])) }}" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                </div>

                <div>
                    <label class="font-bold text-slate-300">Short Summary *</label>
                    <textarea name="short_description" rows="2" required class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">{{ old('short_description', $product->short_description) }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-300">Full Description *</label>
                    <textarea name="description" rows="5" required class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">{{ old('description', $product->description) }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-300">Search Tags</label>
                    <input type="text" name="tags" value="{{ old('tags', implode(', ', $product->tags ?? [])) }}" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">
                </div>
            </div>
        </div>

        <!-- Badges & Status -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-3">
            <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Badges & Visibility</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="text-brand-600 rounded">
                    <span class="text-slate-300 font-medium">Featured</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_bestseller" value="1" {{ $product->is_bestseller ? 'checked' : '' }} class="text-brand-600 rounded">
                    <span class="text-slate-300 font-medium">Best Seller</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_nova_choice" value="1" {{ $product->is_nova_choice ? 'checked' : '' }} class="text-brand-600 rounded">
                    <span class="text-slate-300 font-medium">Nova's Choice</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="text-brand-600 rounded">
                    <span class="text-slate-300 font-medium">Active (Visible)</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-3 rounded-2xl bg-white/5 hover:bg-white/10 text-slate-300 font-bold">Cancel</a>
            <button type="submit" class="px-6 py-3 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white font-bold shadow-glow">
                Save Product Changes
            </button>
        </div>
    </form>

</div>
@endsection

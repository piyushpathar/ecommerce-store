@extends('layouts.admin')

@section('page_title', 'Create New Product')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white">Add New Product to Catalog</h2>
            <p class="text-xs text-slate-400">Specify pricing, specifications, and media</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs text-slate-400 hover:text-white">&larr; Back to Products</a>
    </div>

    <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-6 text-xs"
          x-data="{
              specs: [{ key: '', value: '' }],
              addSpec() {
                  this.specs.push({ key: '', value: '' });
              },
              removeSpec(index) {
                  this.specs.splice(index, 1);
              }
          }">
        @csrf

        @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-950/40 border border-rose-500/40 text-rose-300">
            {{ $errors->first() }}
        </div>
        @endif

        <!-- General Info Card -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Basic Information</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="font-bold text-slate-300">Product Title *</label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Dell UltraSharp 40 WUHD Monitor" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">
                </div>

                <div>
                    <label class="font-bold text-slate-300">Category *</label>
                    <select name="category_id" required class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-bold text-slate-300">Brand Name *</label>
                    <input type="text" name="brand" required value="{{ old('brand') }}" placeholder="e.g. Dell, Keychron, Sony" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">
                </div>

                <div>
                    <label class="font-bold text-slate-300">SKU / Model Number</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" placeholder="e.g. NP-MON-4001" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                </div>

                <div>
                    <label class="font-bold text-slate-300">Initial Stock Units *</label>
                    <input type="number" name="stock" required value="{{ old('stock', 25) }}" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                </div>
            </div>
        </div>

        <!-- Pricing Card -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Pricing (INR)</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 font-mono">
                <div>
                    <label class="font-bold text-slate-300">Selling Deal Price (₹) *</label>
                    <input type="number" step="0.01" name="price" required value="{{ old('price') }}" placeholder="24999" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">
                </div>

                <div>
                    <label class="font-bold text-slate-300">Compare MRP Price (₹)</label>
                    <input type="number" step="0.01" name="compare_price" value="{{ old('compare_price') }}" placeholder="29999" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">
                </div>
            </div>
        </div>

        <!-- Media & Descriptions -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Media & Descriptions</h3>

            <div class="space-y-4">
                <div>
                    <label class="font-bold text-slate-300">Primary Thumbnail Image URL *</label>
                    <input type="url" name="thumbnail" required value="{{ old('thumbnail') }}" placeholder="https://images.unsplash.com/..." class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                </div>

                <div>
                    <label class="font-bold text-slate-300">Additional Gallery Images (Comma-separated URLs)</label>
                    <input type="text" name="images" value="{{ old('images') }}" placeholder="https://..., https://..." class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono">
                </div>

                <div>
                    <label class="font-bold text-slate-300">Short Summary / Highlights *</label>
                    <textarea name="short_description" rows="2" required placeholder="Quick 1-2 sentence overview for cards and product page..." class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">{{ old('short_description') }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-300">Full In-Depth Description *</label>
                    <textarea name="description" rows="5" required placeholder="Detailed specifications, features, build materials..." class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-300">Search Tags (Comma-separated)</label>
                    <input type="text" name="tags" value="{{ old('tags') }}" placeholder="smartphone, 5g, 128gb, titanium, wireless" class="w-full mt-1 p-2.5 bg-white/5 border border-white/10 rounded-xl text-white">
                </div>
            </div>
        </div>

        <!-- Technical Specifications Table Builder -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <h3 class="text-sm font-bold text-white">Technical Specifications</h3>
                <button type="button" @click="addSpec()" class="px-3 py-1 rounded-lg bg-brand-600 text-white font-bold text-xs flex items-center gap-1">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Add Row</span>
                </button>
            </div>

            <div class="space-y-2">
                <template x-for="(spec, idx) in specs" :key="idx">
                    <div class="flex gap-2">
                        <input type="text" name="spec_keys[]" x-model="spec.key" placeholder="Feature (e.g. Refresh Rate)" class="flex-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white">
                        <input type="text" name="spec_values[]" x-model="spec.value" placeholder="Value (e.g. 120 Hz Variable)" class="flex-1 p-2 bg-white/5 border border-white/10 rounded-xl text-white">
                        <button type="button" @click="removeSpec(idx)" class="p-2 text-rose-400 hover:bg-rose-500/10 rounded-xl">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Badges & Promotion -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-3">
            <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Badges & Promotion</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="text-brand-600 rounded">
                    <span class="text-slate-300 font-medium">Featured on Home</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_bestseller" value="1" class="text-brand-600 rounded">
                    <span class="text-slate-300 font-medium">Best Seller Badge</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_nova_choice" value="1" class="text-brand-600 rounded">
                    <span class="text-slate-300 font-medium">Nova's Choice Badge</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-3 rounded-2xl bg-white/5 hover:bg-white/10 text-slate-300 font-bold">Cancel</a>
            <button type="submit" class="px-6 py-3 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white font-bold shadow-glow">
                Publish Product
            </button>
        </div>
    </form>

</div>
@endsection

@extends('layouts.admin')

@section('title', 'Edit Page: ' . $page->title . ' | Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    tab: 'editor',
    content: @js($page->content),
    insertTag(tagOpen, tagClose = '') {
        const textarea = this.$refs.contentArea;
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selected = this.content.substring(start, end);
        const replacement = tagOpen + (selected || 'text here') + tagClose;
        this.content = this.content.substring(0, start) + replacement + this.content.substring(end);
        this.$nextTick(() => {
            textarea.focus();
            textarea.setSelectionRange(start + tagOpen.length, start + replacement.length - tagClose.length);
        });
    },
    insertBox() {
        this.insertTag('<div class="p-4 rounded-2xl bg-brand-500/10 border border-brand-500/20 text-brand-400">\n  ', '\n</div>');
    }
}">

    <!-- Breadcrumb & Title -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
                <a href="{{ route('admin.pages.index') }}" class="hover:text-brand-400">Pages</a>
                <span>/</span>
                <span class="text-white">Edit: {{ $page->title }}</span>
            </div>
            <h2 class="text-xl font-bold text-white">Edit Custom Store Page</h2>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 font-bold text-xs flex items-center gap-1.5">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>View Live</span>
            </a>
            <a href="{{ route('admin.pages.index') }}" class="px-3.5 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 font-bold text-xs flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back</span>
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.pages.update', $page->_id) }}" class="space-y-6 text-xs">
        @csrf
        @method('PUT')

        <!-- Main Content Card -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Basic Information</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-bold text-slate-300 block mb-1">Page Title *</label>
                    <input type="text" name="title" value="{{ $page->title }}" required class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs placeholder-slate-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
                <div>
                    <label class="font-bold text-slate-300 block mb-1">URL Slug *</label>
                    <div class="flex items-center">
                        <span class="px-2.5 py-2.5 rounded-l-xl bg-white/10 text-slate-400 font-mono text-[11px] border border-r-0 border-white/10">/page/</span>
                        <input type="text" name="slug" value="{{ $page->slug }}" required class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-r-xl text-white text-xs font-mono placeholder-slate-500 focus:ring-2 focus:ring-brand-500/30">
                    </div>
                </div>
            </div>

            <!-- Content Area with Formatting Toolbar & Visual Preview Mode -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="font-bold text-slate-300">Page Content (HTML / Rich Text) *</label>
                    
                    <!-- Editor vs Preview Switcher -->
                    <div class="flex items-center gap-1 bg-white/5 p-1 rounded-xl border border-white/10">
                        <button type="button" @click="tab = 'editor'" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all" :class="tab === 'editor' ? 'bg-brand-500 text-white font-bold' : 'text-slate-400 hover:text-white'">
                            Code Editor
                        </button>
                        <button type="button" @click="tab = 'preview'" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all" :class="tab === 'preview' ? 'bg-brand-500 text-white font-bold' : 'text-slate-400 hover:text-white'">
                            Visual Preview
                        </button>
                    </div>
                </div>

                <!-- Formatting Toolbar -->
                <div x-show="tab === 'editor'" class="flex items-center gap-1.5 flex-wrap p-2 bg-white/[0.03] border border-b-0 border-white/10 rounded-t-xl text-xs">
                    <button type="button" @click="insertTag('<h2>', '</h2>')" class="px-2 py-1 bg-white/5 hover:bg-white/10 rounded text-slate-300 font-bold" title="Heading 2">H2</button>
                    <button type="button" @click="insertTag('<h3>', '</h3>')" class="px-2 py-1 bg-white/5 hover:bg-white/10 rounded text-slate-300 font-bold" title="Heading 3">H3</button>
                    <button type="button" @click="insertTag('<strong>', '</strong>')" class="px-2 py-1 bg-white/5 hover:bg-white/10 rounded text-slate-300 font-bold" title="Bold">B</button>
                    <button type="button" @click="insertTag('<em>', '</em>')" class="px-2 py-1 bg-white/5 hover:bg-white/10 rounded text-slate-300 italic" title="Italic">I</button>
                    <button type="button" @click="insertTag('<p>', '</p>')" class="px-2 py-1 bg-white/5 hover:bg-white/10 rounded text-slate-300" title="Paragraph">&lt;p&gt;</button>
                    <button type="button" @click="insertTag('<ul>\n  <li>', '</li>\n</ul>')" class="px-2 py-1 bg-white/5 hover:bg-white/10 rounded text-slate-300" title="Bullet List">List</button>
                    <button type="button" @click="insertBox()" class="px-2 py-1 bg-white/5 hover:bg-white/10 rounded text-brand-400 font-semibold" title="Highlight Card">Highlight Box</button>
                </div>

                <!-- Textarea Editor -->
                <textarea x-show="tab === 'editor'" 
                          x-ref="contentArea" 
                          x-model="content" 
                          name="content" 
                          rows="14" 
                          required 
                          class="w-full p-4 bg-white/5 border border-white/10 rounded-b-xl text-white font-mono text-xs focus:ring-2 focus:ring-brand-500/30 focus:outline-none leading-relaxed"></textarea>

                <!-- Live Visual Preview Container -->
                <div x-show="tab === 'preview'" class="p-6 bg-[#080d16] border border-white/10 rounded-xl min-h-[300px] text-slate-200 prose prose-sm dark:prose-invert max-w-none">
                    <div x-html="content"></div>
                </div>
            </div>
        </div>

        <!-- SEO Metadata Card -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Search Engine Optimization (SEO)</h3>

            <div class="space-y-3">
                <div>
                    <label class="font-bold text-slate-300 block mb-1">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ $page->meta_title }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs placeholder-slate-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
                <div>
                    <label class="font-bold text-slate-300 block mb-1">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-xs placeholder-slate-500 focus:ring-2 focus:ring-brand-500/30">{{ $page->meta_description }}</textarea>
                </div>
            </div>
        </div>

        <!-- Visibility & Placement Settings -->
        <div class="p-6 rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/10 pb-3">Navigation & Placement Settings</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-3 rounded-2xl bg-white/5 border border-white/5 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-white block">Publish Page</span>
                        <span class="text-[10px] text-slate-400">Make live to public</span>
                    </div>
                    <input type="checkbox" name="is_published" value="1" {{ $page->is_published ? 'checked' : '' }} class="text-brand-600 rounded w-4 h-4">
                </div>

                <div class="p-3 rounded-2xl bg-white/5 border border-white/5 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-white block">Header Navbar</span>
                        <span class="text-[10px] text-slate-400">Include in top menu</span>
                    </div>
                    <input type="checkbox" name="show_in_header" value="1" {{ $page->show_in_header ? 'checked' : '' }} class="text-brand-600 rounded w-4 h-4">
                </div>

                <div class="p-3 rounded-2xl bg-white/5 border border-white/5 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-white block">Footer Links</span>
                        <span class="text-[10px] text-slate-400">Include in site footer</span>
                    </div>
                    <input type="checkbox" name="show_in_footer" value="1" {{ $page->show_in_footer ? 'checked' : '' }} class="text-brand-600 rounded w-4 h-4">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="font-bold text-slate-300 block mb-1">Footer Column Category</label>
                    <select name="footer_column" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs focus:ring-2 focus:ring-brand-500/30">
                        <option value="company" {{ $page->footer_column === 'company' ? 'selected' : '' }}>Company Info</option>
                        <option value="help" {{ $page->footer_column === 'help' ? 'selected' : '' }}>Customer Care & Help</option>
                        <option value="legal" {{ $page->footer_column === 'legal' ? 'selected' : '' }}>Legal & Policies</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-300 block mb-1">Sort Order Position</label>
                    <input type="number" name="sort_order" value="{{ $page->sort_order }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs font-mono">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.pages.index') }}" class="px-5 py-2.5 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 font-bold">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-emerald-600 hover:from-brand-600 hover:to-emerald-700 text-white font-bold shadow-glow transition-all">
                Update Page
            </button>
        </div>
    </form>
</div>
@endsection

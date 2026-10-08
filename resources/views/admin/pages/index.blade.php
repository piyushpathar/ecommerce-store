@extends('layouts.admin')

@section('title', 'Manage Store Pages | Admin')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i data-lucide="file-text" class="w-5 h-5 text-brand-400"></i>
                <span>Store Pages & Policy Manager</span>
            </h2>
            <p class="text-xs text-slate-400">Create and visually edit custom store pages, policies, FAQs, and company info</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-emerald-600 hover:from-brand-600 hover:to-emerald-700 text-white font-bold text-xs shadow-glow flex items-center gap-2 self-start sm:self-auto">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Create New Page</span>
        </a>
    </div>

    <!-- Pages Table -->
    <div class="bg-[#0c1117] rounded-3xl border border-white/10 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-white/10 bg-white/[0.02] text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3.5 px-4">Page Title & Slug</th>
                        <th class="py-3.5 px-4">Navigation Visibility</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Sort Order</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($pages as $p)
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-white text-sm">{{ $p->title }}</div>
                            <div class="font-mono text-[11px] text-brand-400 mt-0.5 flex items-center gap-1">
                                <span>/page/{{ $p->slug }}</span>
                                <a href="{{ route('pages.show', $p->slug) }}" target="_blank" class="text-slate-500 hover:text-white">
                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                </a>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @if($p->show_in_header)
                                <span class="px-2 py-0.5 rounded-md bg-sky-500/10 text-sky-400 border border-sky-500/20 text-[10px] font-bold">
                                    Header
                                </span>
                                @endif
                                @if($p->show_in_footer)
                                <span class="px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-[10px] font-bold">
                                    Footer ({{ ucfirst($p->footer_column ?? 'General') }})
                                </span>
                                @endif
                                @if(!$p->show_in_header && !$p->show_in_footer)
                                <span class="text-slate-500 text-[11px]">Direct Link Only</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <form action="{{ route('admin.pages.toggle', $p->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold transition-all cursor-pointer {{ $p->is_published ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/25' : 'bg-slate-700/40 text-slate-400 border border-slate-600/30 hover:bg-slate-700/60' }}">
                                    {{ $p->is_published ? '● Published' : '○ Draft' }}
                                </button>
                            </form>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-400">
                            {{ $p->sort_order }}
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('pages.show', $p->slug) }}" target="_blank" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-colors" title="View Public Page">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.pages.edit', $p->id) }}" class="p-1.5 rounded-lg text-brand-400 hover:bg-brand-500/15 transition-colors" title="Edit Page Content">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.pages.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this page?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/15 transition-colors" title="Delete Page">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            <i data-lucide="file-question" class="w-8 h-8 mx-auto text-slate-500 mb-2"></i>
                            <p class="text-sm">No custom pages found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

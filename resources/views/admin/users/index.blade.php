@extends('layouts.admin')

@section('page_title', 'User Accounts & Roles')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white">Registered Users</h2>
            <p class="text-xs text-slate-400">View customer profiles and manage administrative roles</p>
        </div>

        <span class="text-xs font-mono font-bold text-brand-400 bg-brand-950/60 px-3 py-1.5 rounded-xl border border-brand-800/40">
            Total Users: {{ $users->total() }}
        </span>
    </div>

    <!-- Search Form -->
    <div class="p-4 rounded-2xl bg-[#0c1117] border border-white/10 flex items-center justify-between text-xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex-1 max-w-md flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name, email..." class="flex-1 px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            <button type="submit" class="px-4 py-2 bg-white/10 hover:bg-white/15 text-white font-bold rounded-xl">Search</button>
        </form>
    </div>

    <!-- Users Table -->
    <div class="rounded-3xl bg-[#0c1117] border border-white/10 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-white/10 uppercase font-mono text-[10px] bg-white/[0.02]">
                        <th class="p-4">User</th>
                        <th class="p-4">Email Address</th>
                        <th class="p-4">Phone</th>
                        <th class="p-4">Role</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Registered Date</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($users as $u)
                    <tr class="hover:bg-white/[0.01] transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-600 text-white font-bold flex items-center justify-center text-xs">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <span class="font-bold text-white">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td class="p-4 font-mono text-slate-300">{{ $u->email }}</td>
                        <td class="p-4 font-mono text-slate-400">{{ $u->phone ?: 'N/A' }}</td>
                        <td class="p-4 font-mono">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $u->role === 'admin' ? 'bg-brand-500/20 text-brand-400 border border-brand-500/30' : 'bg-slate-500/20 text-slate-400' }}">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $u->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                {{ $u->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </td>
                        <td class="p-4 text-slate-400 font-mono text-[11px]">{{ $u->created_at->format('d M Y') }}</td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('admin.users.toggle.role', $u->_id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 rounded bg-white/5 hover:bg-white/10 text-[10px] text-slate-300 transition-colors">
                                        {{ $u->role === 'admin' ? 'Revoke Admin' : 'Make Admin' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.toggle.status', $u->_id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 rounded {{ $u->is_active ? 'bg-rose-500/10 text-rose-400 hover:bg-rose-500/20' : 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20' }} text-[10px] transition-colors">
                                        {{ $u->is_active ? 'Disable' : 'Enable' }}
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
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection

@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Users</h1>
            <p class="mt-1 text-sm text-slate-400">{{ $users->total() }} registered account{{ $users->total() === 1 ? '' : 's' }}.</p>
        </div>
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-2">
            <input type="search" name="search" value="{{ $search }}" placeholder="Name or email…" aria-label="Search users"
                class="rounded-xl border border-white/10 bg-abyss-900/70 px-3.5 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">
            <x-ui.button type="submit" variant="secondary" size="sm">Search</x-ui.button>
            @if ($search !== '')
                <a href="{{ route('admin.users.index') }}" class="self-center px-2 text-sm text-slate-400 hover:text-white">Clear</a>
            @endif
        </form>
    </div>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-white/5">
        <table class="w-full text-left text-sm">
            <thead class="bg-abyss-900/80 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Name</th>
                    <th class="px-4 py-3 font-medium">Email</th>
                    <th class="px-4 py-3 font-medium">Role</th>
                    <th class="px-4 py-3 font-medium">Games</th>
                    <th class="px-4 py-3 font-medium">Spins</th>
                    <th class="px-4 py-3 font-medium">Steam</th>
                    <th class="px-4 py-3 text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($users as $user)
                    <tr class="bg-abyss-900/40 transition hover:bg-abyss-850/60">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="grid h-8 w-8 flex-none place-items-center rounded-full bg-abyss-700 text-xs font-bold text-slate-200">
                                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                </span>
                                <span class="text-slate-100">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-400">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <x-ui.badge :tone="$user->isAdmin() ? 'amber' : 'slate'">{{ $user->role }}</x-ui.badge>
                        </td>
                        <td class="px-4 py-3 text-slate-300">{{ $user->user_games_count }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ $user->spin_history_count }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $user->steamAccount?->display_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            @if ($user->steamAccount)
                                <form method="POST" action="{{ route('admin.users.steam.destroy', $user) }}"
                                    x-data @submit.prevent="if (confirm('Unlink this user\'s Steam account? They will be able to link a different one.')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-300 hover:text-red-200">Unlink Steam</button>
                                </form>
                            @else
                                <span class="text-xs text-slate-600">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-slate-500">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection
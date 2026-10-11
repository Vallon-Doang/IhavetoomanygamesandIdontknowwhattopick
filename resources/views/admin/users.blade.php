@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-white">Users</h1>
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-2">
            <input type="search" name="search" value="{{ $search }}" placeholder="Name or email…"
                class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-amber-500 focus:outline-none">
            <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 text-sm text-white hover:bg-slate-600">Search</button>
            @if ($search !== '')
                <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:border-slate-500">Clear</a>
            @endif
        </form>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-slate-800">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-900 text-slate-400">
                <tr>
                    <th class="px-4 py-3 font-medium">Name</th>
                    <th class="px-4 py-3 font-medium">Email</th>
                    <th class="px-4 py-3 font-medium">Role</th>
                    <th class="px-4 py-3 font-medium">Games</th>
                    <th class="px-4 py-3 font-medium">Spins</th>
                    <th class="px-4 py-3 font-medium">Steam</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse ($users as $user)
                    <tr class="bg-slate-900/40">
                        <td class="px-4 py-3 text-slate-100">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded px-2 py-0.5 text-xs font-medium {{ $user->isAdmin() ? 'bg-amber-900/50 text-amber-300' : 'bg-slate-700/60 text-slate-300' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-300">{{ $user->user_games_count }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ $user->spin_history_count }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $user->steamAccount?->display_name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-slate-500">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection
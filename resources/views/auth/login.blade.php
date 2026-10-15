@extends('layouts.auth')

@section('title', 'Log in')

@section('content')
    <h1 class="text-2xl font-bold tracking-tight text-white">Welcome back</h1>
    <p class="mt-1 text-sm text-slate-400">Log in to continue where you left off.</p>

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5 rounded-2xl border border-white/5 bg-abyss-900/60 p-6 sm:p-7">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-300">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                class="mt-1.5 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-slate-100 placeholder:text-slate-500 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                class="mt-1.5 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-slate-100 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-400">
            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-white/20 bg-abyss-950 text-accent-500 focus:ring-accent-500/40">
            Remember me
        </label>

        <x-ui.button type="submit" class="w-full">Log in</x-ui.button>

        <p class="text-center text-sm text-slate-400">
            No account?
            <a href="{{ route('register') }}" class="font-medium text-accent-400 hover:text-accent-300">Register</a>
        </p>
    </form>
@endsection
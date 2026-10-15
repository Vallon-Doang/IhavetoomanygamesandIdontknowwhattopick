@extends('layouts.auth')

@section('title', 'Register')

@section('content')
    <h1 class="text-2xl font-bold tracking-tight text-white">Create your account</h1>
    <p class="mt-1 text-sm text-slate-400">Registration creates a member account. Admin access is assigned separately.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5 rounded-2xl border border-white/5 bg-abyss-900/60 p-6 sm:p-7">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-slate-300">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="mt-1.5 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-slate-100 placeholder:text-slate-500 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-300">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                class="mt-1.5 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-slate-100 placeholder:text-slate-500 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password"
                class="mt-1.5 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-slate-100 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-300">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                class="mt-1.5 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-slate-100 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">
        </div>

        <x-ui.button type="submit" class="w-full">Register</x-ui.button>

        <p class="text-center text-sm text-slate-400">
            Already registered?
            <a href="{{ route('login') }}" class="font-medium text-accent-400 hover:text-accent-300">Log in</a>
        </p>
    </form>
@endsection
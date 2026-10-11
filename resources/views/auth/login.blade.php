@extends('layouts.app')

@section('title', 'Log in')

@section('content')
    <div class="mx-auto max-w-md">
        <h1 class="text-2xl font-bold text-white">Log in</h1>
        <p class="mt-1 text-sm text-slate-400">Welcome back. Pick up where you left off.</p>

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5 rounded-xl border border-slate-800 bg-slate-900/50 p-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-300">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100 focus:border-indigo-500 focus:outline-none">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
                <input id="password" name="password" type="password" required
                    class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100 focus:border-indigo-500 focus:outline-none">
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-400">
                <input type="checkbox" name="remember" value="1" class="rounded border-slate-700 bg-slate-950">
                Remember me
            </label>

            <button type="submit" class="w-full rounded-lg bg-indigo-500 px-4 py-2.5 font-semibold text-white hover:bg-indigo-400">
                Log in
            </button>

            <p class="text-center text-sm text-slate-400">
                No account?
                <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300">Register</a>
            </p>
        </form>
    </div>
@endsection
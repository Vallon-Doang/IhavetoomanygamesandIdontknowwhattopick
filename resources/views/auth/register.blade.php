@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="mx-auto max-w-md">
        <h1 class="text-2xl font-bold text-white">Create your account</h1>
        <p class="mt-1 text-sm text-slate-400">Registration creates a member account. Admin access is assigned separately.</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5 rounded-xl border border-slate-800 bg-slate-900/50 p-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-300">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                    class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100 focus:border-indigo-500 focus:outline-none">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-300">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100 focus:border-indigo-500 focus:outline-none">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
                <input id="password" name="password" type="password" required
                    class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100 focus:border-indigo-500 focus:outline-none">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-300">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100 focus:border-indigo-500 focus:outline-none">
            </div>

            <button type="submit" class="w-full rounded-lg bg-indigo-500 px-4 py-2.5 font-semibold text-white hover:bg-indigo-400">
                Register
            </button>

            <p class="text-center text-sm text-slate-400">
                Already registered?
                <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300">Log in</a>
            </p>
        </form>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
    <section class="rounded-2xl border border-slate-800 bg-gradient-to-br from-slate-900 to-slate-950 px-6 py-12 text-center sm:px-12 sm:py-16">
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl">
            Can't decide what to play?
        </h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-300">
            GameSpin keeps track of the games you own, filters out the ones you're not in the mood for,
            and lets a roulette wheel pick your next game — fairly.
        </p>

        @guest
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('register') }}" class="rounded-lg bg-indigo-500 px-6 py-3 font-semibold text-white shadow hover:bg-indigo-400">
                    Create an account
                </a>
                <a href="{{ route('login') }}" class="rounded-lg border border-slate-700 px-6 py-3 font-semibold text-slate-200 hover:border-slate-500">
                    Log in
                </a>
            </div>
        @else
            <div class="mt-8">
                <a href="{{ route('dashboard') }}" class="rounded-lg bg-indigo-500 px-6 py-3 font-semibold text-white hover:bg-indigo-400">
                    Go to my dashboard
                </a>
            </div>
        @endguest
    </section>

    <section class="mt-12 grid gap-6 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-6">
            <div class="text-2xl font-bold text-indigo-400">1</div>
            <h2 class="mt-2 font-semibold text-white">Build your library</h2>
            <p class="mt-1 text-sm text-slate-400">Start with seeded demo games, then link Steam to import your own collection.</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-6">
            <div class="text-2xl font-bold text-indigo-400">2</div>
            <h2 class="mt-2 font-semibold text-white">Filter what matters</h2>
            <p class="mt-1 text-sm text-slate-400">Search by title, set playtime limits, and exclude games you don't want to see.</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-6">
            <div class="text-2xl font-bold text-indigo-400">3</div>
            <h2 class="mt-2 font-semibold text-white">Spin and play</h2>
            <p class="mt-1 text-sm text-slate-400">The roulette picks one eligible game at random and records it in your history.</p>
        </div>
    </section>

    <p class="mt-10 text-center text-xs text-slate-500">
        Steam features are optional. The roulette works with seeded demo data even without Steam access.
    </p>
@endsection
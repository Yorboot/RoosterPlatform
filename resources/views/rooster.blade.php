@extends('layouts.app')
@section('title', 'Welkom')
@section('content')
<div class="grid gap-8 lg:grid-cols-2">
    <section class="rounded-2xl border border-slate-800 bg-slate-900 p-8">
        <p class="text-sm uppercase tracking-widest text-cyan-400">Rooster Platform</p>
        <h1 class="mt-3 text-4xl font-bold">Plan diensten. Werk samen.</h1>
        <p class="mt-4 text-slate-300">Beheer diensten en taken als planner of schrijf je in als medewerker.</p>
    </section>
    <div class="grid gap-6 md:grid-cols-2">
        <form method="POST" action="{{ route('login') }}" class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
            @csrf <input type="hidden" name="_html" value="1"><h2 class="text-xl font-semibold">Inloggen</h2>
            <label class="mt-5 block">E-mail<input type="email" name="email" required class="mt-2 w-full rounded bg-slate-800 px-3 py-2"></label>
            <label class="mt-4 block">Wachtwoord<input type="password" name="password" required class="mt-2 w-full rounded bg-slate-800 px-3 py-2"></label>
            <button class="mt-5 w-full rounded bg-cyan-500 px-4 py-2 font-semibold text-slate-950">Inloggen</button>
        </form>
        <form method="POST" action="{{ route('register') }}" class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
            @csrf <input type="hidden" name="_html" value="1"><h2 class="text-xl font-semibold">Account aanmaken</h2>
            <label class="mt-5 block">Naam<input name="name" required class="mt-2 w-full rounded bg-slate-800 px-3 py-2"></label>
            <label class="mt-4 block">E-mail<input type="email" name="email" required class="mt-2 w-full rounded bg-slate-800 px-3 py-2"></label>
            <label class="mt-4 block">Telefoonnummer<input name="phone" required class="mt-2 w-full rounded bg-slate-800 px-3 py-2"></label>
            <label class="mt-4 block">Wachtwoord<input type="password" name="password" required class="mt-2 w-full rounded bg-slate-800 px-3 py-2"></label>
            <label class="mt-4 block">Herhaal wachtwoord<input type="password" name="password_confirmation" required class="mt-2 w-full rounded bg-slate-800 px-3 py-2"></label>
            <button class="mt-5 w-full rounded bg-slate-700 px-4 py-2 font-semibold">Registreren</button>
        </form>
    </div>
</div>
@endsection

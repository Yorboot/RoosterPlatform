@extends('layouts.app')
@section('title', $user->name)
@section('content')
<h1 class="text-3xl font-bold">{{ $user->name }}</h1>
<div class="mt-6 rounded-xl border border-slate-800 bg-slate-900 p-6">
    <dl class="grid gap-4 md:grid-cols-2">
        <div><dt class="text-sm text-slate-400">E-mail</dt><dd>{{ $user->email }}</dd></div>
        <div><dt class="text-sm text-slate-400">Telefoon</dt><dd>{{ $user->phone }}</dd></div>
    </dl>
</div>
<h2 class="mt-8 text-2xl font-semibold">Diensten</h2>
<div class="mt-4 grid gap-3">
    @forelse ($user->shifts as $shift)
        <a href="{{ route('shifts.show', $shift) }}" class="rounded-xl border border-slate-800 bg-slate-900 p-4">
            {{ $shift->scheduled_date->format('d-m-Y') }} · {{ $shift->title }}
        </a>
    @empty
        <p class="text-slate-400">Deze medewerker heeft nog geen diensten.</p>
    @endforelse
</div>
@endsection

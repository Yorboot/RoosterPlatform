@extends('layouts.app')
@section('title', 'Diensten')
@section('content')
<div class="flex flex-wrap items-end justify-between gap-4">
    <div><p class="text-sm uppercase tracking-widest text-cyan-400">Planning</p><h1 class="text-3xl font-bold">Beschikbare diensten</h1></div>
    @if (auth()->user()->role === 'planner') <a href="{{ route('shifts.create') }}" class="rounded bg-cyan-500 px-4 py-2 font-semibold text-slate-950">Dienst aanmaken</a> @endif
</div>
<form class="mt-8 grid gap-4 rounded-xl border border-slate-800 bg-slate-900 p-5 md:grid-cols-4">
    <input name="title" value="{{ request('title') }}" placeholder="Titel" class="rounded bg-slate-800 px-3 py-2">
    <input name="department" value="{{ request('department') }}" placeholder="Afdeling" class="rounded bg-slate-800 px-3 py-2">
    <select name="status" class="rounded bg-slate-800 px-3 py-2"><option value="">Elke status</option>@foreach(['draft' => 'Concept', 'published' => 'Gepubliceerd', 'completed' => 'Afgerond'] as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select>
    <button class="rounded bg-slate-700 px-4 py-2 font-semibold hover:bg-slate-600">Zoeken</button>
</form>
<div class="mt-8 grid gap-4 md:grid-cols-2">
@forelse ($shifts as $shift)
    <article class="rounded-xl border border-slate-800 bg-slate-900 p-5">
        <div class="flex items-start justify-between gap-4"><div><h2 class="text-xl font-semibold">{{ $shift->title }}</h2><p class="text-cyan-300">{{ $shift->department }}</p></div><span class="rounded-full bg-slate-800 px-3 py-1 text-xs">{{ $shift->is_completed ? 'Afgerond' : ['draft' => 'Concept', 'published' => 'Gepubliceerd', 'completed' => 'Afgerond'][$shift->status] }}</span></div>
        <p class="mt-4 text-slate-300">{{ $shift->description }}</p>
        <p class="mt-4 text-sm text-slate-400">{{ $shift->scheduled_date->format('d-m-Y') }} · {{ substr($shift->begin_time, 0, 5) }} - {{ substr($shift->end_time, 0, 5) }} · {{ $shift->users_count }}/{{ $shift->required_staff }} medewerkers</p>
        <div class="mt-5 flex flex-wrap gap-3"><a href="{{ route('shifts.show', $shift) }}" class="rounded bg-slate-700 px-3 py-2 text-sm">Details</a>
        @if ($shift->is_fully_staffed)<span class="rounded bg-emerald-700 px-3 py-2 text-sm">Volgeboekt</span>@elseif ($shift->has_staff_shortage)<span class="rounded bg-amber-700 px-3 py-2 text-sm" title="Nog niet alle benodigde medewerkers zijn ingeschreven">!</span>@endif
        @if (auth()->user()->role === 'planner') <form method="POST" action="{{ route('shifts.destroy', $shift) }}">@csrf @method('DELETE')<button class="rounded bg-rose-600 px-3 py-2 text-sm">Verwijderen</button></form>@endif
        @if (auth()->user()->role === 'medewerker')
            @if ($shift->users->contains(auth()->id())) <form method="POST" action="{{ route('shifts.unenroll', $shift) }}">@csrf @method('DELETE')<button class="rounded bg-rose-600 px-3 py-2 text-sm">Uitschrijven</button></form>
            @else <form method="POST" action="{{ route('shifts.enroll', $shift) }}">@csrf<button class="rounded bg-cyan-500 px-3 py-2 text-sm font-semibold text-slate-950">Inschrijven</button></form> @endif
        @endif</div>
    </article>
@empty <p class="text-slate-400">Geen diensten gevonden.</p> @endforelse
</div>
@endsection

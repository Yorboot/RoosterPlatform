@extends('layouts.app')
@section('title', 'Mijn profiel')
@section('content')
<h1 class="text-3xl font-bold">Mijn komende diensten</h1>
<div class="mt-5 grid gap-3">@forelse($upcoming as $shift)<a href="{{ route('shifts.show', $shift) }}" class="rounded-xl border border-slate-800 bg-slate-900 p-4">{{ $shift->scheduled_date->format('d-m-Y') }} · {{ substr($shift->begin_time, 0, 5) }} - {{ substr($shift->end_time, 0, 5) }} · {{ $shift->title }}</a>@empty<p class="text-slate-400">Geen komende diensten.</p>@endforelse</div>
<h2 class="mt-10 text-2xl font-bold">Afgelopen diensten</h2>
<div class="mt-5 grid gap-3">@forelse($past as $shift)<a href="{{ route('shifts.show', $shift) }}" class="rounded-xl border border-slate-800 bg-slate-900 p-4 text-slate-400">{{ $shift->scheduled_date->format('d-m-Y') }} · {{ $shift->title }}</a>@empty<p class="text-slate-400">Geen afgelopen diensten.</p>@endforelse</div>
@endsection

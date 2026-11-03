@extends('layouts.app')
@section('title', $heading)
@section('content')
<h1 class="text-3xl font-bold">{{ $heading }}</h1>
<form method="POST" action="{{ $shift->exists ? route('shifts.update', $shift) : route('shifts.store') }}" class="mt-8 grid gap-5 rounded-xl border border-slate-800 bg-slate-900 p-6 md:grid-cols-2">
    @csrf @if($shift->exists) @method('PUT') @endif
    <label>Titel<input name="title" required value="{{ old('title', $shift->title) }}" class="mt-2 w-full rounded bg-slate-800 px-3 py-2"></label>
    <label>Afdeling<input name="department" required value="{{ old('department', $shift->department) }}" class="mt-2 w-full rounded bg-slate-800 px-3 py-2"></label>
    <label class="md:col-span-2">Beschrijving<textarea name="description" class="mt-2 w-full rounded bg-slate-800 px-3 py-2">{{ old('description', $shift->description) }}</textarea></label>
    <label>Benodigde medewerkers<input type="number" min="1" name="required_staff" required value="{{ old('required_staff', $shift->required_staff) }}" class="mt-2 w-full rounded bg-slate-800 px-3 py-2"></label>
    <label>Geplande datum<input type="date" name="scheduled_date" required value="{{ old('scheduled_date', optional($shift->scheduled_date)->format('Y-m-d')) }}" class="mt-2 w-full rounded bg-slate-800 px-3 py-2"></label>
    <label>Begintijd<input type="time" name="begin_time" required value="{{ old('begin_time', substr($shift->begin_time ?? '', 0, 5)) }}" class="mt-2 w-full rounded bg-slate-800 px-3 py-2"></label>
    <label>Eindtijd<input type="time" name="end_time" required value="{{ old('end_time', substr($shift->end_time ?? '', 0, 5)) }}" class="mt-2 w-full rounded bg-slate-800 px-3 py-2"></label>
    <label>Status<select name="status" class="mt-2 w-full rounded bg-slate-800 px-3 py-2">@foreach(['draft' => 'Concept', 'published' => 'Gepubliceerd', 'completed' => 'Afgerond'] as $value => $label)<option value="{{ $value }}" @selected(old('status', $shift->status ?: 'draft') === $value)>{{ $label }}</option>@endforeach</select></label>
    <button class="rounded bg-cyan-500 px-4 py-3 font-semibold text-slate-950 md:col-span-2">Opslaan</button>
</form>
@endsection

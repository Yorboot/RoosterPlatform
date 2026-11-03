@extends('layouts.app')
@section('title', $task->exists ? 'Taak wijzigen' : 'Taak toevoegen')
@section('content')
<h1 class="text-3xl font-bold">{{ $task->exists ? 'Taak wijzigen' : 'Taak toevoegen' }}</h1>
<form method="POST" action="{{ $task->exists ? route('tasks.update', [$shift, $task]) : route('tasks.store', $shift) }}" class="mt-8 grid gap-5 rounded-xl border border-slate-800 bg-slate-900 p-6">
@csrf @if($task->exists) @method('PATCH') @endif
<label>Titel<input name="title" required value="{{ old('title', $task->title) }}" class="mt-2 w-full rounded bg-slate-800 px-3 py-2"></label>
<label>Beschrijving<textarea name="description" class="mt-2 w-full rounded bg-slate-800 px-3 py-2">{{ old('description', $task->description) }}</textarea></label>
<label>Status<select name="status" class="mt-2 w-full rounded bg-slate-800 px-3 py-2">@foreach(['pending' => 'Openstaand', 'busy' => 'Bezig', 'done' => 'Klaar'] as $value => $label)<option value="{{ $value }}" @selected(old('status', $task->status ?: 'pending') === $value)>{{ $label }}</option>@endforeach</select></label>
<button class="rounded bg-cyan-500 px-4 py-3 font-semibold text-slate-950">Opslaan</button>
</form>
@endsection

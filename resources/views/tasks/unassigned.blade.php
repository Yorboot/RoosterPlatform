@extends('layouts.app')
@section('title', 'Losse taken')
@section('content')
<div>
    <p class="text-sm uppercase tracking-widest text-cyan-400">Beheer</p>
    <h1 class="text-3xl font-bold">Taken zonder dienst</h1>
    <p class="mt-2 text-slate-400">Deze taken zijn bewaard nadat hun dienst is verwijderd. Koppel ze aan een nieuwe dienst.</p>
</div>
<div class="mt-8 grid gap-4">
    @forelse ($tasks as $task)
        <article class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <h2 class="text-xl font-semibold">{{ $task->title }}</h2>
            <p class="mt-2 text-slate-400">{{ $task->description }}</p>
            <form method="POST" action="{{ route('tasks.assign', $task) }}" class="mt-4 flex flex-wrap gap-3">
                @csrf
                @method('PATCH')
                <select name="shift_id" class="min-w-64 rounded bg-slate-800 px-3 py-2" required>
                    <option value="">Kies een dienst</option>
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}">{{ $shift->title }} — {{ $shift->scheduled_date->format('d-m-Y') }}</option>
                    @endforeach
                </select>
                <button class="rounded bg-cyan-500 px-4 py-2 font-semibold text-slate-950">Aan dienst koppelen</button>
            </form>
        </article>
    @empty
        <p class="text-slate-400">Er zijn geen losse taken.</p>
    @endforelse
</div>
@endsection

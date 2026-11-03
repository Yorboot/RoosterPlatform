<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Rooster Platform')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-shell min-h-screen text-slate-100">
<header class="border-b border-slate-800 bg-slate-900">
    <nav class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <a href="{{ route('shifts.index') }}" class="text-xl font-bold text-cyan-400">Rooster Platform</a>
        <div class="flex items-center gap-4 text-sm">
            @auth
                <a href="{{ route('shifts.index') }}" class="hover:text-cyan-300">Diensten</a>
                <a href="{{ route('profile') }}" class="hover:text-cyan-300">Mijn profiel</a>
                @if (auth()->user()->role === 'planner')
                    <a href="{{ route('shifts.create') }}" class="hover:text-cyan-300">Nieuwe dienst</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf <input type="hidden" name="_html" value="1">
                    <button class="rounded bg-slate-700 px-3 py-2 hover:bg-slate-600">Uitloggen</button>
                </form>
            @else
                <a href="{{ route('home') }}" class="hover:text-cyan-300">Inloggen</a>
            @endauth
        </div>
    </nav>
</header>
<main class="mx-auto max-w-6xl px-6 py-8">
    @if (session('success'))
        <div class="mb-6 rounded border border-emerald-500/40 bg-emerald-500/10 p-4 text-emerald-200">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-6 rounded border border-red-500/40 bg-red-500/10 p-4 text-red-200">
            <ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @yield('content')
</main>
</body>
</html>

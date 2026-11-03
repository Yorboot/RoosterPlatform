<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    public function profile(): View
    {
        $user = auth()->user();
        $user->load(['shifts' => fn ($query) => $query->orderBy('scheduled_date')->orderBy('begin_time')]);
        $upcoming = $user->shifts->filter(fn ($shift) => $shift->scheduled_date->copy()->setTimeFromTimeString($shift->end_time)->isFuture());
        $past = $user->shifts->diff($upcoming);

        return view('profile', compact('upcoming', 'past'));
    }

    public function show(User $user): View
    {
        $user->load(['shifts' => fn ($query) => $query->orderBy('scheduled_date')->orderBy('begin_time')]);

        return view('users.show', compact('user'));
    }
}

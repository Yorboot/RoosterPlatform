<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftUserController extends Controller
{
    public function store(Request $request, Shift $shift): RedirectResponse
    {
        abort_unless(auth()->check() && auth()->user()->role === 'medewerker', 403);
        $user = $request->user();

        $error = DB::transaction(function () use ($shift, $user): ?string {
            $lockedShift = Shift::query()->lockForUpdate()->findOrFail($shift->id);
            $start = $lockedShift->startDateTime();

            if (now()->gte($start->copy()->subDay())) {
                return 'Inschrijven kan tot 24 uur voor de begintijd.';
            }
            if ($lockedShift->users()->whereKey($user->id)->exists()) {
                return 'Je bent al ingeschreven voor deze dienst.';
            }
            if ($lockedShift->users()->count() >= $lockedShift->required_staff) {
                return 'Deze dienst zit al vol.';
            }

            $end = $lockedShift->endDateTime();
            $hasOverlap = Shift::whereHas('users', fn ($query) => $query->whereKey($user->id))
                ->get()
                ->contains(function (Shift $enrolledShift) use ($start, $end): bool {
                    $enrolledStart = $enrolledShift->startDateTime();
                    $enrolledEnd = $enrolledShift->endDateTime();

                    return $enrolledStart->lt($end) && $enrolledEnd->gt($start);
                });

            if ($hasOverlap) {
                return 'Je bent al ingeschreven voor een overlappende dienst.';
            }

            $lockedShift->users()->attach($user->id);

            return null;
        });

        if ($error !== null) {
            return back()->withErrors(['shift' => $error]);
        }

        return back()->with('success', 'Je bent ingeschreven.');
    }

    public function destroy(Request $request, Shift $shift): RedirectResponse
    {
        $shiftStart = $shift->startDateTime();
        abort_unless($request->user()->role === 'medewerker', 403);

        if (now()->gte($shiftStart->copy()->subDay())) {
            return back()->withErrors(['shift' => 'Uitschrijven kan tot 24 uur voor de begintijd.']);
        }

        $shift->users()->detach($request->user()->id);

        return back()->with('success', 'Je bent uitgeschreven.');
    }
}

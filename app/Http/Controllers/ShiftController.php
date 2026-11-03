<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function index(Request $request): View
    {
        $query = Shift::query()->withCount('users')->orderBy('scheduled_date')->orderBy('begin_time');

        foreach (['title', 'department', 'status'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, 'like', '%'.$request->string($filter).'%');
            }
        }

        return view('shifts.index', ['shifts' => $query->get()]);
    }

    public function create(): View
    {
        $this->ensurePlanner();

        return view('shifts.form', ['shift' => new Shift, 'heading' => 'Dienst aanmaken']);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensurePlanner();
        $shift = Shift::create($this->validated($request));

        return redirect()->route('shifts.show', $shift)->with('success', 'Dienst aangemaakt.');
    }

    public function show(Shift $shift): View
    {
        $shift->load(['users', 'tasks']);

        return view('shifts.show', compact('shift'));
    }

    public function edit(Shift $shift): View
    {
        $this->ensurePlanner();

        return view('shifts.form', ['shift' => $shift, 'heading' => 'Dienst wijzigen']);
    }

    public function update(Request $request, Shift $shift): RedirectResponse
    {
        $this->ensurePlanner();
        $data = $this->validated($request);

        if ($data['required_staff'] < $shift->users()->count()) {
            return back()->withInput()->withErrors([
                'required_staff' => 'Het aantal benodigde medewerkers mag niet lager zijn dan het aantal ingeschreven medewerkers.',
            ]);
        }

        if ($this->createsEnrolledUserOverlap($shift, $data)) {
            return back()->withInput()->withErrors([
                'scheduled_date' => 'Deze wijziging veroorzaakt een overlappende dienst voor een ingeschreven medewerker.',
            ]);
        }

        $shift->update($data);

        return redirect()->route('shifts.show', $shift)->with('success', 'Dienst gewijzigd.');
    }

    public function destroy(Shift $shift): RedirectResponse
    {
        $this->ensurePlanner();
        $hadEnrollments = $shift->users()->exists();
        $shift->delete();

        $response = redirect()->route('shifts.index')->with('success', 'Dienst verwijderd. De inschrijvingen zijn verwijderd en de taken zijn beschikbaar als losse taken.');

        if ($hadEnrollments) {
            $response->with('warning', 'Let op: de verwijderde dienst had inschrijvingen. Controleer of andere diensten voldoende medewerkers hebben.');
        }

        return $response;
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'department' => ['required', 'string', 'max:255'],
            'required_staff' => ['required', 'integer', 'min:1'],
            'scheduled_date' => ['required', 'date'],
            'begin_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'status' => ['required', 'in:draft,published,completed'],
        ], [
            'department.required' => 'Een afdeling is verplicht.',
        ]);

        return $data;
    }

    private function ensurePlanner(): void
    {
        abort_unless(auth()->user()?->role === 'planner', 403);
    }

    private function createsEnrolledUserOverlap(Shift $shift, array $data): bool
    {
        $start = Carbon::parse($data['scheduled_date'])->setTimeFromTimeString($data['begin_time']);
        $end = Carbon::parse($data['scheduled_date'])->setTimeFromTimeString($data['end_time']);
        $userIds = $shift->users()->pluck('users.id');

        return Shift::whereKeyNot($shift->id)
            ->whereHas('users', fn ($query) => $query->whereIn('users.id', $userIds))
            ->get()
            ->contains(function (Shift $otherShift) use ($start, $end): bool {
                $otherStart = $otherShift->startDateTime();
                $otherEnd = $otherShift->endDateTime();

                return $otherStart->lt($end) && $otherEnd->gt($start);
            });
    }
}

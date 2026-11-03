<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShifTaskController extends Controller
{
    public function create(Shift $shift): View
    {
        return view('tasks.form', ['shift' => $shift, 'task' => new ShiftTask]);
    }

    public function unassigned(): View
    {
        $tasks = ShiftTask::query()->whereNull('shift_id')->latest()->get();
        $shifts = Shift::query()->orderBy('scheduled_date')->orderBy('begin_time')->get();

        return view('tasks.unassigned', compact('tasks', 'shifts'));
    }

    public function store(Request $request, Shift $shift): RedirectResponse
    {
        $shift->tasks()->create($this->validated($request));

        return redirect()->route('shifts.show', $shift)->with('success', 'Taak toegevoegd.');
    }

    public function edit(Shift $shift, ShiftTask $task): View|RedirectResponse
    {
        if ($task->shift_id !== $shift->id) {
            return redirect()->route('shifts.show', $shift)->withErrors([
                'task' => 'Deze taak hoort niet bij de geselecteerde dienst.',
            ]);
        }

        return view('tasks.form', compact('shift', 'task'));
    }

    public function update(Request $request, Shift $shift, ShiftTask $task): RedirectResponse
    {
        if ($task->shift_id !== $shift->id) {
            return redirect()->route('shifts.show', $shift)->withErrors([
                'task' => 'Deze taak hoort niet bij de geselecteerde dienst.',
            ]);
        }
        $task->update($this->validated($request));

        return redirect()->route('shifts.show', $shift)->with('success', 'Taak gewijzigd.');
    }

    public function updateStatus(Request $request, ShiftTask $task): RedirectResponse
    {
        $request->validate(['status' => ['required', 'in:pending,busy,done']]);
        if ($request->user()->role !== 'medewerker') {
            return back()->withErrors([
                'authorization' => 'Alleen medewerkers kunnen taakstatussen wijzigen.',
            ]);
        }
        if (! $task->shift->users()->whereKey($request->user()->id)->exists()) {
            return back()->withErrors([
                'task' => 'Je kunt alleen taken wijzigen van een dienst waarvoor je bent ingeschreven.',
            ]);
        }
        if (! $task->shift->isActive()) {
            return back()->withErrors([
                'task' => 'Een taak kan alleen tijdens de dienst worden gewijzigd.',
            ]);
        }
        $task->update(['status' => $request->string('status')]);

        return back()->with('success', 'Taakstatus bijgewerkt.');
    }

    public function assign(Request $request, ShiftTask $task): RedirectResponse
    {
        $data = $request->validate([
            'shift_id' => ['required', 'integer', 'exists:shifts,id'],
        ], [
            'shift_id.required' => 'Kies een dienst.',
            'shift_id.exists' => 'De gekozen dienst bestaat niet.',
        ]);

        if ($task->shift_id !== null) {
            return back()->withErrors([
                'task' => 'Deze taak is al aan een dienst gekoppeld.',
            ]);
        }

        $task->update(['shift_id' => $data['shift_id']]);

        return back()->with('success', 'Taak aan de dienst gekoppeld.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,busy,done'],
        ]);
    }
}

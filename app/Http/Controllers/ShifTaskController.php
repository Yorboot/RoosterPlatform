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
        $task->update(['status' => $request->string('status')]);

        return back()->with('success', 'Taakstatus bijgewerkt.');
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

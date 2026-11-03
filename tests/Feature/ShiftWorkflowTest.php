<?php

use App\Models\Shift;
use App\Models\ShiftTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('planner cannot create a shift without a department', function () {
    $planner = User::factory()->create(['role' => 'planner']);

    $response = $this->actingAs($planner)->post(route('shifts.store'), [
        'title' => 'Avonddienst',
        'description' => 'Balie',
        'department' => '',
        'required_staff' => 1,
        'scheduled_date' => now()->addDays(2)->toDateString(),
        'begin_time' => '17:00',
        'end_time' => '09:00',
        'status' => 'published',
    ]);

    $response->assertSessionHasErrors(['department']);
});

test('a planner can create an overnight shift', function () {
    $planner = User::factory()->create(['role' => 'planner']);

    $response = $this->actingAs($planner)->post(route('shifts.store'), [
        'title' => 'Nachtdienst',
        'description' => 'Nachtbalie',
        'department' => 'Beveiliging',
        'required_staff' => 1,
        'scheduled_date' => now()->addDay()->toDateString(),
        'begin_time' => '22:00',
        'end_time' => '02:00',
        'status' => 'published',
    ]);

    $response->assertRedirect();
    expect(Shift::latest('id')->first()->endDateTime()->toDateString())
        ->toBe(now()->addDays(2)->toDateString());
});

test('a medewerker cannot enroll in overlapping shifts', function () {
    $employee = User::factory()->create(['role' => 'medewerker']);
    $first = Shift::factory()->create([
        'scheduled_date' => now()->addDays(2)->toDateString(),
        'begin_time' => '09:00',
        'end_time' => '13:00',
        'required_staff' => 2,
    ]);
    $second = Shift::factory()->create([
        'scheduled_date' => $first->scheduled_date,
        'begin_time' => '12:00',
        'end_time' => '16:00',
    ]);

    $this->actingAs($employee)->post(route('shifts.enroll', $first))->assertSessionHasNoErrors();
    expect($employee->fresh()->shifts)->toHaveCount(1);
    $this->actingAs($employee)->post(route('shifts.enroll', $second))->assertSessionHasErrors('shift');

    expect($employee->fresh()->shifts)->toHaveCount(1);
});

test('a medewerker cannot enroll after a shift has started or when it is full', function () {
    $employee = User::factory()->create(['role' => 'medewerker']);
    $started = Shift::factory()->create([
        'scheduled_date' => now()->toDateString(),
        'begin_time' => now()->subHour()->format('H:i'),
        'end_time' => now()->addHour()->format('H:i'),
    ]);
    $full = Shift::factory()->create(['required_staff' => 1]);
    $otherEmployee = User::factory()->create(['role' => 'medewerker']);
    $full->users()->attach($otherEmployee);

    $this->actingAs($employee)->post(route('shifts.enroll', $started))
        ->assertSessionHasErrors('shift');
    $this->actingAs($employee)->post(route('shifts.enroll', $full))
        ->assertSessionHasErrors('shift');
});

test('a planner cannot reduce capacity below current enrollments or create an overlap', function () {
    $planner = User::factory()->create(['role' => 'planner']);
    $employee = User::factory()->create(['role' => 'medewerker']);
    $shift = Shift::factory()->create(['required_staff' => 2]);
    $otherShift = Shift::factory()->create([
        'scheduled_date' => $shift->scheduled_date,
        'begin_time' => '12:00',
        'end_time' => '16:00',
    ]);
    $shift->users()->attach($employee);
    $otherShift->users()->attach($employee);

    $this->actingAs($planner)->put(route('shifts.update', $shift), [
        'title' => $shift->title,
        'description' => $shift->description,
        'department' => $shift->department,
        'required_staff' => 0,
        'scheduled_date' => $shift->scheduled_date->toDateString(),
        'begin_time' => '09:00',
        'end_time' => '17:00',
        'status' => 'published',
    ])->assertSessionHasErrors('required_staff');

    $this->actingAs($planner)->put(route('shifts.update', $shift), [
        'title' => $shift->title,
        'description' => $shift->description,
        'department' => $shift->department,
        'required_staff' => 2,
        'scheduled_date' => $shift->scheduled_date->toDateString(),
        'begin_time' => '13:00',
        'end_time' => '17:00',
        'status' => 'published',
    ])->assertSessionHasErrors('scheduled_date');
});

test('a medewerker receives a message instead of an authorization error when managing a task', function () {
    $employee = User::factory()->create(['role' => 'medewerker']);
    $shift = Shift::factory()->create();
    $task = ShiftTask::factory()->create(['shift_id' => $shift->id]);

    $response = $this->actingAs($employee)->get(route('tasks.edit', [$shift, $task]));

    $response->assertRedirect();
    $response->assertSessionHasErrors('authorization');
});

test('a medewerker cannot change a task outside an enrolled shift', function () {
    $employee = User::factory()->create(['role' => 'medewerker']);
    $shift = Shift::factory()->create();
    $task = ShiftTask::factory()->create(['shift_id' => $shift->id]);

    $response = $this->actingAs($employee)->patch(route('tasks.status', $task), [
        'status' => 'done',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors('task');
    expect($task->fresh()->status)->toBe('pending');
});

test('a planner can reassign a task from a deleted shift', function () {
    $planner = User::factory()->create(['role' => 'planner']);
    $shift = Shift::factory()->create();
    $replacementShift = Shift::factory()->create();
    $task = ShiftTask::factory()->create(['shift_id' => $shift->id]);

    $this->actingAs($planner)->delete(route('shifts.destroy', $shift));
    expect($task->fresh()->shift_id)->toBeNull();

    $response = $this->actingAs($planner)->patch(route('tasks.assign', $task), [
        'shift_id' => $replacementShift->id,
    ]);

    $response->assertRedirect();
    expect($task->fresh()->shift_id)->toBe($replacementShift->id);
});

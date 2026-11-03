<?php

use App\Http\Controllers\ShifTaskController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShiftUserController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('rooster');
})->name('home');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function (): void {
    Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::post('/shifts', [ShiftController::class, 'store'])
        ->middleware('planner')
        ->name('shifts.store');
    Route::get('/shifts/create', [ShiftController::class, 'create'])
        ->middleware('planner')
        ->name('shifts.create');
    Route::get('/shifts/{shift}', [ShiftController::class, 'show'])->whereNumber('shift')->name('shifts.show');
    Route::get('/shifts/{shift}/edit', [ShiftController::class, 'edit'])
        ->middleware('planner')
        ->whereNumber('shift')
        ->name('shifts.edit');
    Route::put('/shifts/{shift}', [ShiftController::class, 'update'])
        ->middleware('planner')
        ->whereNumber('shift')
        ->name('shifts.update');
    Route::post('/shifts/{shift}/enroll', [ShiftUserController::class, 'store'])
        ->whereNumber('shift')
        ->name('shifts.enroll');
    Route::delete('/shifts/{shift}/enroll', [ShiftUserController::class, 'destroy'])
        ->whereNumber('shift')
        ->name('shifts.unenroll');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/users/{user}', [UserController::class, 'show'])
        ->middleware('planner')
        ->whereNumber('user')
        ->name('users.show');
    Route::get('/shifts/{shift}/tasks/create', [ShifTaskController::class, 'create'])
        ->middleware('planner')
        ->whereNumber('shift')
        ->name('tasks.create');
    Route::post('/shifts/{shift}/tasks', [ShifTaskController::class, 'store'])
        ->middleware('planner')
        ->whereNumber('shift')
        ->name('tasks.store');
    Route::get('/shifts/{shift}/tasks/{task}/edit', [ShifTaskController::class, 'edit'])
        ->middleware('planner')
        ->whereNumber(['shift', 'task'])
        ->name('tasks.edit');
    Route::patch('/shifts/{shift}/tasks/{task}', [ShifTaskController::class, 'update'])
        ->middleware('planner')
        ->whereNumber(['shift', 'task'])
        ->name('tasks.update');
    Route::patch('/tasks/{task}/status', [ShifTaskController::class, 'updateStatus'])
        ->whereNumber('task')
        ->name('tasks.status');
});

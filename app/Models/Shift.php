<?php

namespace App\Models;

use Database\Factories\ShiftFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'department', 'required_staff', 'scheduled_date', 'begin_time', 'end_time', 'status'])]
class Shift extends Model
{
    /** @use HasFactory<ShiftFactory> */
    use HasFactory;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(ShiftUser::class)->withTimestamps();
    }

    public function shiftUsers(): HasMany
    {
        return $this->hasMany(ShiftUser::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ShiftTask::class);
    }

    protected function casts(): array
    {
        return [
            'required_staff' => 'integer',
            'scheduled_date' => 'date',
        ];
    }

    protected function isCompleted(): Attribute
    {
        return Attribute::get(fn (): bool => $this->scheduled_date
            ->copy()
            ->setTimeFromTimeString($this->end_time)
            ->isPast());
    }
}

<?php

namespace App\Models;

use Database\Factories\ShiftFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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
        return Attribute::get(fn (): bool => $this->endDateTime()->isPast());
    }

    protected function isFullyStaffed(): Attribute
    {
        return Attribute::get(fn (): bool => $this->users()->count() >= $this->required_staff);
    }

    protected function hasStaffShortage(): Attribute
    {
        return Attribute::get(fn (): bool => $this->users()->count() < $this->required_staff);
    }

    public function startDateTime(): Carbon
    {
        return $this->scheduled_date->copy()->setTimeFromTimeString($this->begin_time);
    }

    public function endDateTime(): Carbon
    {
        $end = $this->scheduled_date->copy()->setTimeFromTimeString($this->end_time);

        if ($end->lessThanOrEqualTo($this->startDateTime())) {
            $end->addDay();
        }

        return $end;
    }

    public function isActive(): bool
    {
        return now()->betweenIncluded($this->startDateTime(), $this->endDateTime()->subSecond());
    }
}

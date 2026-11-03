<?php

namespace App\Models;

use Database\Factories\ShiftUserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable(['shift_id', 'user_id'])]
class ShiftUser extends Pivot
{
    /** @use HasFactory<ShiftUserFactory> */
    use HasFactory;

    protected $table = 'shift_user';

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

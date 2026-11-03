<?php

namespace App\Models;

use Database\Factories\ShiftTaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['shift_id', 'title', 'description', 'status'])]
class ShiftTask extends Model
{
    /** @use HasFactory<ShiftTaskFactory> */
    use HasFactory;

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
}

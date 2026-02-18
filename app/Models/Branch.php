<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',      // 'MAIN' or 'EXTENSION'
        'code',      // e.g., 'CEIT', 'SILANG'
        'name',      // e.g., 'College of Engineering...', 'Silang Campus'
        'address',
        'updated_by',
    ];

    /**
     * Accessor to display "MAIN - CEIT" or "EXTENSION - SILANG"
     */
    public function getDisplayTitleAttribute(): string
    {
        return $this->type.' - '.$this->code;
    }

    /**
     * Get the user who last updated the branch.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

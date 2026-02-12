<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacultyProfile extends Model
{
    /** @use HasFactory<\Database\Factories\FacultyProfileFactory> */
    use HasFactory;

    protected $table = 'faculty_profiles';

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'branch',
        'department',
        'academic_rank',
        'email',
        'contactno',
        'address',
        'sex',
        'birthday',
        'updated_by'
    ];

    /**
     * Get the user that owns the FacultyProfileFactory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

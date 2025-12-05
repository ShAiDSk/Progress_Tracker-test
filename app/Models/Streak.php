<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Streak extends Model
{
    use HasFactory;

    // Add fillable or guarded properties as needed for your database structure
    protected $fillable = [
        'user_id', 
        'current_streak', 
        'longest_streak', 
        'last_check_in',
    ];

    /**
     * Get the user that owns the streak.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Streak;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone', // Add this to users table migration
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all goals for the user.
     */
    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    /**
     * Get the user's streak record.
     */
    public function streak(): HasOne
    {
        return $this->hasOne(Streak::class);
    }

    /**
     * Get active goals only
     */
    public function activeGoals()
    {
        return $this->hasMany(Goal::class)
            ->where('status', 'active');   // ✅ ONLY active, NOT archived
    }


    /**
     * Get completed goals only
     */
    public function completedGoals()
    {
        return $this->hasMany(Goal::class)
            ->where('status', 'completed');
    }

    /**
     * Get statistics for the user
     */
    public function getStatistics(): array
    {
        return [
            'total_goals' => $this->goals()
                ->where('status', '!=', 'archived')
                ->count(),

            'active_goals' => $this->activeGoals()->count(),

            'completed_goals' => $this->completedGoals()->count(),

            'completion_rate' => $this->getCompletionRate(),

            'current_streak' => $this->streak?->current_streak ?? 0,

            'longest_streak' => $this->streak?->longest_streak ?? 0,
        ];
    }


    /**
     * Calculate completion rate percentage
     */
    public function getCompletionRate(): float
    {
        $total = $this->goals()
            ->where('status', '!=', 'archived')
            ->count();

        if ($total === 0) {
            return 0;
        }

        $completed = $this->completedGoals()->count();

        return round(($completed / $total) * 100, 2);
    }


    /**
     * Get goals by category
     */
    public function getGoalsByCategory(): array
    {
        return $this->goals()
            ->selectRaw('category, COUNT(*) as count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
    }

    /**
     * Check if user has reached their goal limit
     */
    public function hasReachedGoalLimit(): bool
    {
        $maxGoals = config('app.max_goals_per_user', 100);
        return $this->goals()->count() >= $maxGoals;
    }
}

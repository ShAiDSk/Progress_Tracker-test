<?php

namespace App\Services;

use App\Models\User;
use App\Models\Streak;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StreakService
{
    /**
     * Update user's streak based on goal completion
     * Handles timezone, grace periods, and consecutive day logic
     */
    public function updateStreak(User $user): void
    {
        $streak = $user->streak ?? $this->createInitialStreak($user);
        
        $userTimezone = $user->timezone ?? config('app.timezone');
        $today = Carbon::now($userTimezone)->startOfDay();
        $lastActivity = $streak->last_activity_date 
            ? Carbon::parse($streak->last_activity_date)->startOfDay() 
            : null;

        // First time completing a goal
        if (!$lastActivity) {
            $this->initializeStreak($streak, $today);
            return;
        }

        $daysSinceLastActivity = $today->diffInDays($lastActivity);

        // Already completed something today
        if ($daysSinceLastActivity === 0) {
            return;
        }

        // Consecutive day - increment streak
        if ($daysSinceLastActivity === 1) {
            $this->incrementStreak($streak, $today);
            return;
        }

        // Streak broken - reset to 1
        $this->resetStreak($streak, $today);
    }

    /**
     * Create initial streak record for user
     */
    private function createInitialStreak(User $user): Streak
    {
        return Streak::create([
            'user_id' => $user->id,
            'current_streak' => 0,
            'longest_streak' => 0,
            'last_activity_date' => null,
        ]);
    }

    /**
     * Initialize streak on first completion
     */
    private function initializeStreak(Streak $streak, Carbon $date): void
    {
        $streak->update([
            'current_streak' => 1,
            'longest_streak' => 1,
            'last_activity_date' => $date,
        ]);

        Log::info("Streak initialized for user {$streak->user_id}");
    }

    /**
     * Increment streak for consecutive day
     */
    private function incrementStreak(Streak $streak, Carbon $date): void
    {
        $newStreak = $streak->current_streak + 1;
        
        $streak->update([
            'current_streak' => $newStreak,
            'longest_streak' => max($newStreak, $streak->longest_streak),
            'last_activity_date' => $date,
        ]);

        Log::info("Streak incremented to {$newStreak} for user {$streak->user_id}");
    }

    /**
     * Reset streak when broken
     */
    private function resetStreak(Streak $streak, Carbon $date): void
    {
        $oldStreak = $streak->current_streak;
        
        $streak->update([
            'current_streak' => 1,
            'last_activity_date' => $date,
        ]);

        Log::warning("Streak reset from {$oldStreak} to 1 for user {$streak->user_id}");
    }

    /**
     * Check if user is at risk of losing streak (hasn't completed anything today)
     */
    public function isStreakAtRisk(User $user): bool
    {
        if (!$user->streak || $user->streak->current_streak === 0) {
            return false;
        }

        $userTimezone = $user->timezone ?? config('app.timezone');
        $today = Carbon::now($userTimezone)->startOfDay();
        $lastActivity = $user->streak->last_activity_date
            ? Carbon::parse($user->streak->last_activity_date)->startOfDay()
            : null;

        if (!$lastActivity) {
            return false;
        }

        // Streak at risk if last activity was yesterday and nothing done today
        return $today->diffInDays($lastActivity) === 1;
    }

    /**
     * Get streak statistics for user
     */
    public function getStreakStats(User $user): array
    {
        $streak = $user->streak;

        if (!$streak) {
            return [
                'current' => 0,
                'longest' => 0,
                'at_risk' => false,
                'days_until_record' => 0,
            ];
        }

        $daysUntilRecord = max(0, $streak->longest_streak - $streak->current_streak);

        return [
            'current' => $streak->current_streak,
            'longest' => $streak->longest_streak,
            'at_risk' => $this->isStreakAtRisk($user),
            'days_until_record' => $daysUntilRecord,
            'is_record' => $streak->current_streak === $streak->longest_streak && $streak->current_streak > 0,
        ];
    }

    /**
     * Manually reset a user's streak (admin function)
     */
    public function manualReset(User $user): void
    {
        if ($streak = $user->streak) {
            $streak->update([
                'current_streak' => 0,
                'last_activity_date' => null,
            ]);

            Log::warning("Manual streak reset for user {$user->id}");
        }
    }
}
<?php

namespace App\Policies;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoalPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any goals.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the goal.
     */
    public function view(User $user, Goal $goal): bool
    {
        return $user->id === $goal->user_id;
    }

    /**
     * Determine if the user can create goals.
     */
    public function create(User $user): bool
    {
        // Optional: Limit number of goals per user
        $maxGoals = config('app.max_goals_per_user', 100);
        $currentGoalCount = $user->goals()->count();

        return $currentGoalCount < $maxGoals;
    }

    /**
     * Determine if the user can update the goal.
     */
    public function update(User $user, Goal $goal): bool
    {
        return $user->id === $goal->user_id;
    }

    /**
     * Determine if the user can delete the goal.
     */
    public function delete(User $user, Goal $goal): bool
    {
        return $user->id === $goal->user_id;
    }

    /**
     * Determine if the user can restore the goal.
     */
    public function restore(User $user, Goal $goal): bool
    {
        return $user->id === $goal->user_id;
    }

    /**
     * Determine if the user can permanently delete the goal.
     */
    public function forceDelete(User $user, Goal $goal): bool
    {
        return $user->id === $goal->user_id;
    }
}
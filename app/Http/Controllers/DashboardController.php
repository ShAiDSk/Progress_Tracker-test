<?php

namespace App\Http\Controllers;

use App\Services\StreakService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected StreakService $streakService;

    public function __construct(StreakService $streakService)
    {
        $this->streakService = $streakService;
    }

    /**
     * Display the dashboard with user statistics and overview
     */
    public function index()
    {
        $user = auth()->user();

        $recentGoals = $user->goals()
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get();

        $recentCompletions = $user->goals()
            ->where('status', 'completed')
            ->latest('completed_at')
            ->take(5)
            ->get();

        $overdueGoals = $user->goals()
            ->where('status', 'active')
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->get();

        $highPriorityGoals = $user->goals()
            ->where('status', 'active')
            ->whereIn('priority', ['high', 'critical'])
            ->get();

        $statistics = [
            'total_goals' => $user->goals()->count(),
            'active_goals' => $user->goals()->where('status', 'active')->count(),
            'completed_goals' => $user->goals()->where('status', 'completed')->count(),
        ];

        return view('dashboard', compact(
            'recentGoals',
            'recentCompletions',
            'overdueGoals',
            'highPriorityGoals',
            'statistics'
        ));
    }


    /**
     * Calculate overall progress summary
     */
    private function calculateProgressSummary($user): array
    {
        $activeGoals = $user->activeGoals()->get();

        if ($activeGoals->isEmpty()) {
            return [
                'total_progress' => 0,
                'average_progress' => 0,
                'goals_near_completion' => 0,
            ];
        }

        $totalProgress = $activeGoals->sum('progress_percentage');
        $averageProgress = round($totalProgress / $activeGoals->count(), 2);

        // Goals that are 80% or more complete
        $goalsNearCompletion = $activeGoals->filter(function ($goal) {
            return $goal->progress_percentage >= 80;
        })->count();

        return [
            'total_progress' => $totalProgress,
            'average_progress' => $averageProgress,
            'goals_near_completion' => $goalsNearCompletion,
        ];
    }
}

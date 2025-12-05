<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Services\StreakService;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    protected StreakService $streakService;

    public function __construct(StreakService $streakService)
    {
        $this->streakService = $streakService;
    }

    /**
     * Display a listing of the user's goals.
     */
    public function index(Request $request)
    {
        $query = auth()->user()->goals()->where('status', '!=', 'archived');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        $goals = $query->orderBy($sortBy, $sortOrder)->paginate(15);

        return view('goals.index', [
            'goals' => $goals,
            'filters' => $request->only(['status', 'category', 'priority', 'sort', 'order']),
        ]);
    }

    /**
     * Show the form for creating a new goal.
     */
    public function create()
    {
        return view('goals.create');
    }

    /**
     * Store a newly created goal in storage.
     */
    public function store(StoreGoalRequest $request)
    {
        $data = $request->validated();

        // ✅ REQUIRED by your DB schema
        $data['user_id'] = auth()->id();

        // ✅ Safe defaults (your DB also supports these)
        $data['current_value'] = 0;
        $data['status'] = 'active';
        $data['unit'] = $data['unit'] ?? 'count';

        $goal = Goal::create($data);

        return redirect()
            ->route('goals.index')
            ->with('success', 'Goal created successfully!');
    }



    /**
     * Display the specified goal.
     */
    public function show(Goal $goal)
    {


        return view('goals.show', [
            'goal' => $goal,
        ]);
    }

    /**
     * Show the form for editing the specified goal.
     */
    public function edit(Goal $goal)
    {

        return view('goals.edit', [
            'goal' => $goal,
        ]);
    }

    /**
     * Update the specified goal in storage.
     */
    public function update(UpdateGoalRequest $request, Goal $goal)
    {
        $wasActive = $goal->status === 'active';
        $goal->update($request->validated());

        // If goal just became completed, update streak
        if (!$wasActive && $goal->status === 'completed') {
            $this->streakService->updateStreak(auth()->user());
        }

        return redirect()
            ->route('goals.show', $goal)
            ->with('success', 'Goal updated successfully!');
    }

    /**
     * Remove the specified goal from storage (soft delete).
     */
    public function destroy(Goal $goal)
    {


        $goal->delete();

        return redirect()
            ->route('goals.index')
            ->with('success', 'Goal deleted successfully.');
    }

    /**
     * Increment goal progress by a specific amount
     */
    public function increment(Request $request, Goal $goal)
    {
        $request->validate([
            'current_amount' => 'required|numeric|min:0.01',
        ]);

        $oldValue = $goal->current_value;
        $goal->incrementProgress($request->current_amount);

        // Check if this completion should update streak
        if ($goal->is_completed && $oldValue < $goal->target_value) {
            $this->streakService->updateStreak(auth()->user());
        }

        return back()->with('success', 'Progress updated!');
    }

    /**
     * Mark goal as complete
     */
    public function complete(Goal $goal)
    {


        if ($goal->markAsComplete()) {
            $this->streakService->updateStreak(auth()->user());

            return back()->with('success', '🎉 Goal completed! Streak updated.');
        }

        return back()->with('error', 'Failed to complete goal.');
    }

    /**
     * Reopen a completed goal
     */
    public function reopen(Goal $goal)
    {


        if ($goal->reopen()) {
            return back()->with('success', 'Goal reopened.');
        }

        return back()->with('error', 'Goal is not completed.');
    }

    /**
     * Archive a goal
     */
    public function archive(Goal $goal)
    {

        $goal->archive();

        return redirect()
            ->route('goals.index')
            ->with('success', 'Goal archived.');
    }

    /**
     * Batch complete multiple goals
     */
    public function batchComplete(Request $request)
    {
        $request->validate([
            'goal_ids' => 'required|array',
            'goal_ids.*' => 'exists:goals,id',
        ]);

        $goals = Goal::whereIn('id', $request->goal_ids)
            ->where('user_id', auth()->id())
            ->get();

        $completedCount = 0;
        foreach ($goals as $goal) {
            if ($goal->markAsComplete()) {
                $completedCount++;
            }
        }

        if ($completedCount > 0) {
            $this->streakService->updateStreak(auth()->user());
        }

        return back()->with('success', "{$completedCount} goal(s) completed!");
    }
}

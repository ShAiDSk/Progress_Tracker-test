<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $goals = auth()->user()->goals ?? collect();
        return view('goals.index', compact('goals'));
    }

    public function create()
    {
        return view('goals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
            'title' => 'required',
            'description' => 'nullable',
            'target_amount' => 'nullable|numeric',
            'deadline' => 'nullable|date',
            ]
        );

        $validated['user_id'] = auth()->id(); // SUPER IMPORTANT
        $validated['current_amount'] = 0;

        Goal::create($validated);

        return redirect()->route('goals.index')->with('success', 'Goal created!');
    }

    public function markDone(Goal $goal)
    {
        $this->authorizeAction($goal);

        if ($goal->target_amount) {
            $goal->current_amount = $goal->target_amount;
        } else {
            // if no target, mark done by leaving current_amount as-is or set to 1
            $goal->current_amount = max($goal->current_amount, 1);
        }

        $goal->save();

        return redirect()->route('goals.index')->with('success', 'Goal marked as done.');
    }

    /**
     * Reopen a completed goal (reset progress)
     */
    public function reopen(Goal $goal)
    {
        $this->authorizeAction($goal);

        $goal->current_amount = 0;
        $goal->save();

        return redirect()->route('goals.index')->with('success', 'Goal reopened.');
    }

    /**
     * Update progress (set current_amount)
     */
    public function updateProgress(Request $request, Goal $goal)
    {
        $this->authorizeAction($goal);

        $validated = $request->validate(
            [
            'current_amount' => 'required|numeric|min:0',
            ]
        );

        $new = $validated['current_amount'];

        // clamp to target_amount if present
        if ($goal->target_amount !== null && $goal->target_amount > 0) {
            $new = min($new, $goal->target_amount);
        }

        $goal->current_amount = $new;
        $goal->save();

        return redirect()->route('goals.index')->with('success', 'Progress updated.');
    }

    /**
     * Helper to ensure the authenticated user owns the goal.
     * Throws 403 if not.
     */
    protected function authorizeAction(Goal $goal)
    {
        if (Auth::id() !== $goal->user_id) {
            abort(403);
        }
    }
}

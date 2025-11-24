<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::where('user_id', auth()->id())
            ->where('hidden', false)
            ->get();

        $hiddenGoals = Goal::where('user_id', auth()->id())
            ->where('hidden', true)
            ->get();

        return view('goals.index', compact('goals', 'hiddenGoals'));
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

    public function updateProgress(Request $request, Goal $goal)
    {
        $request->validate([
            'current_amount' => 'required|numeric|min:0'
        ]);

        $goal->update([
            'current_amount' => $request->current_amount
        ]);

        return back()->with('success', 'Progress updated');
    }

    public function markDone(Goal $goal)
    {
        if ($goal->target_amount) {
            $goal->update(['current_amount' => $goal->target_amount]);
        }
        return back()->with('success', 'Goal marked as completed');
    }

    public function reopen(Goal $goal)
    {
        $goal->update(['current_amount' => 0]);
        return back()->with('success', 'Goal reopened');
    }

    public function hide(Goal $goal)
    {

        $goal->hidden = true;
        $goal->save();

        return back()->with('success', 'Goal hidden');
    }

    public function unhide(Goal $goal)
    {

        $goal->hidden = false;
        $goal->save();

        return back()->with('success', 'Goal restored');
    }

    public function destroy(Goal $goal)
    {
        $goal->delete();
        return back()->with('success', 'Goal removed');
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

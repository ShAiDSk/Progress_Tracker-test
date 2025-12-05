<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGoalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user owns the goal
        return $this->route('goal')->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $goal = $this->route('goal');

        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'target_value' => [
                'sometimes',
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
                // Ensure target is not less than current value
                function ($attribute, $value, $fail) use ($goal) {
                    $currentValue = $this->input('current_value', $goal->current_value);
                    if ($value < $currentValue) {
                        $fail('Target cannot be less than current progress.');
                    }
                },
            ],
            'current_value' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
                // Ensure current doesn't exceed target
                function ($attribute, $value, $fail) use ($goal) {
                    $targetValue = $this->input('target_value', $goal->target_value);
                    if ($value > $targetValue) {
                        $fail('Progress cannot exceed your target.');
                    }
                },
            ],
            'unit' => [
                'sometimes',
                'required',
                'string',
                'max:50',
            ],
            'category' => [
                'nullable',
                'string',
                'max:100',
                'in:fitness,learning,productivity,health,finance,personal,career,hobby,other',
            ],
            'priority' => [
                'nullable',
                'string',
                'in:low,medium,high,critical',
            ],
            'status' => [
                'sometimes',
                'required',
                'string',
                'in:active,completed,archived,paused',
            ],
            'deadline' => [
                'nullable',
                'date',
            ],
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Goal title cannot be empty.',
            'title.min' => 'Goal title must be at least 3 characters.',
            'target_value.min' => 'Target must be greater than 0.',
            'current_value.min' => 'Progress cannot be negative.',
            'status.in' => 'Invalid status. Use: active, completed, archived, or paused.',
            'priority.in' => 'Priority must be low, medium, high, or critical.',
            'category.in' => 'Please select a valid category.',
        ];
    }

    /**
     * Handle authorization failure
     */
    protected function failedAuthorization()
    {
        abort(403, 'You do not have permission to update this goal.');
    }

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation(): void
    {
        $goal = $this->route('goal');

        // If marking as completed, set completed_at timestamp
        if ($this->status === 'completed' && $goal->status !== 'completed') {
            $this->merge([
                'completed_at' => now(),
            ]);
        }

        // If reopening a completed goal, clear completed_at
        if ($this->status === 'active' && $goal->status === 'completed') {
            $this->merge([
                'completed_at' => null,
            ]);
        }
    }
}
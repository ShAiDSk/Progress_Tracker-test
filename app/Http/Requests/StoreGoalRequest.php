<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization happens in controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => [
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
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
            ],
            'unit' => [
                'nullable',
                'string',
                'max:50',
                'in:days,hours,minutes,reps,pages,kilometers,miles,pounds,kilograms,count,custom',
            ],

            'custom_unit' => [
                'nullable',
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
            'deadline' => [
                'nullable',
                'date',
                'after:today',
            ],
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please give your goal a title.',
            'title.min' => 'Your goal title should be at least 3 characters.',
            'title.max' => 'Keep your goal title under 255 characters.',
            'target_value.required' => 'Set a target to work towards.',
            'target_value.min' => 'Target must be greater than 0.',
            'target_value.max' => 'Target value is too large.',
            'unit.required' => 'Specify what unit you\'re tracking (days, hours, reps, etc).',
            'unit.in' => 'Please select a valid unit from the list.',
            'custom_unit.required_if' => 'Please specify your custom unit.',
            'category.in' => 'Please select a valid category.',
            'priority.in' => 'Priority must be low, medium, high, or critical.',
            'deadline.after' => 'Deadline must be in the future.',
        ];
    }

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation(): void
    {
        // Set default values if not provided
        $this->merge([
            'current_value' => 0,
            'status' => 'active',
            'priority' => $this->priority ?? 'medium',
            'category' => $this->category ?? 'other',
        ]);

        // If custom unit is selected, use the custom_unit value
        if ($this->unit === 'custom' && $this->custom_unit) {
            $this->merge([
                'unit' => $this->custom_unit,
            ]);
        }
    }

    /**
     * Get validated data with user_id
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Add user_id to validated data
        $validated['user_id'] = auth()->id();

        return $validated;
    }
}

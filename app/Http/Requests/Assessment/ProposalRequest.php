<?php

namespace App\Http\Requests\Assessment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProposalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->type === 'assessor';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'scheduled_at' => ['nullable', 'date'],
            'assessment_scheduled_id' => ['nullable', 'exists:assessment_schedules,id'],
            'feedback' => [
                'nullable',
                'string',
                'max:1000',
                function ($attribute, $value, $fail) {
                    // If rejecting (no scheduled_at and no assessment_scheduled_id), feedback is required
                    if (empty($this->scheduled_at) && empty($this->assessment_scheduled_id) && empty($value)) {
                        $fail('Feedback is required when rejecting an assessment proposal.');
                    }
                },
            ],
        ];
    }
}

<?php

namespace App\Http\Requests\Assessment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EvaluateRequest extends FormRequest
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
            'tasks' => ['required', 'array'],
            'result' => ['required', 'boolean'],
            'comment' => ['nullable', 'string'],
            'started_at' => ['required', 'date'],

            'assessee_name' => ['required', 'string'],
            'assessee_signature' => ['nullable', 'string'],
            'assessee_signature_date' => ['nullable', 'date'],
            'assessor_name' => ['required', 'string'],
            'assessor_signature' => ['nullable', 'string'],
            'assessor_signature_date' => ['nullable', 'date'],
            'supervisor_name' => ['nullable', 'string'],
            'supervisor_signature' => ['nullable', 'string'],
            'supervisor_signature_date' => ['nullable', 'date'],
            'data_recorder_name' => ['nullable', 'string'],
            'data_recorder_signature' => ['nullable', 'string'],
            'data_recorder_signature_date' => ['nullable', 'date'],
        ];
    }
}

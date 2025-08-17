<?php

namespace App\Http\Requests\Module;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->type === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'duration_estimation' => ['nullable', 'numeric', 'min:0'],
            'minimum_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'questions_count' => ['required', 'numeric', 'min:0'],
            'body' => ['nullable', 'string'],
            'equipment_required' => ['nullable', 'string'],
            'procedure' => ['nullable', 'string'],
            'reference' => ['nullable', 'string'],
            'performance' => ['nullable', 'string'],
        ];
    }
}

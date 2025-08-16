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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'duration_estimation' => ['nullable', 'numeric', 'min:0'],
            'minimum_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'questions_count' => ['required', 'numeric', 'min:0'],
        ];
    }
}

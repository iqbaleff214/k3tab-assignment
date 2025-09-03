<?php

namespace App\Http\Requests\PerformanceGuide;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'module_id' => ['nullable', 'exists:modules,id'],
            'skill_number' => ['required', 'string', 'unique:performance_guides,skill_number'],
            'title' => ['required', 'string'],
            'performance_task' => ['required'],
            'tasks' => ['required'],
        ];
    }
}

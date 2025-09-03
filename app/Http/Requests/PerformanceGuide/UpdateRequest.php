<?php

namespace App\Http\Requests\PerformanceGuide;

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
            'module_id' => ['nullable', 'exists:modules,id'],
            'title' => ['required', 'string'],
            'performance_task' => ['required'],
            'tasks' => ['required'],
        ];
    }
}

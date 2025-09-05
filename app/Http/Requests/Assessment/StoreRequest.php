<?php

namespace App\Http\Requests\Assessment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->type === 'assessee';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'assessor_id' => ['required', 'exists:users,id'],
            'skill_number' => ['required', 'exists:performance_guides,skill_number'],
            'available' => ['required', 'array', 'min:1'],
        ];
    }
}

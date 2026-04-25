<?php

namespace App\Http\Requests\Module\Question;

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
        $type = $this->input('type');
        $needsChoices = $type === 'multiple_choice' || $type === 'fill_in_the_blank';
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:multiple_choice,essay,fill_in_the_blank'],
            'question' => ['nullable', 'string'],
            'question_image' => ['nullable', 'file', 'image', 'max:10240'],
            'choices' => [$needsChoices ? 'required' : 'nullable', 'array'],
            'choices.*' => ['nullable', 'string'],
            'choices_images' => ['nullable', 'array'],
            'choices_images.*' => ['nullable'],
            'correct_answer_index' => [$type === 'multiple_choice' ? 'required' : 'nullable', 'numeric', 'min:0'],
        ];
    }
}

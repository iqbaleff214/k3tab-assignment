<?php

namespace App\Http\Requests\Module\Question;

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
        $isEssay = $this->input('type') === 'essay';
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:multiple_choice,essay'],
            'question' => ['nullable', 'string'],
            'question_image' => ['nullable', 'file', 'image', 'max:10240'],
            'choices' => [$isEssay ? 'nullable' : 'required', 'array'],
            'choices.*' => ['nullable', 'string'],
            'choices_images' => ['nullable', 'array'],
            'choices_images.*' => ['nullable', 'file', 'image', 'max:10240'],
            'correct_answer_index' => [$isEssay ? 'nullable' : 'required', 'numeric', 'min:0'],
        ];
    }
}

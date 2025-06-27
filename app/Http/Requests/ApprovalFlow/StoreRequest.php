<?php

namespace App\Http\Requests\ApprovalFlow;

use App\Enum\Permission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Permission::AddApprovalFlow->isAllowed();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'subject' => 'required|string',
            'parent_id' => 'nullable|exists:approval_flows,id',
            'role_id' => 'required|exists:roles,id',
        ];
    }
}

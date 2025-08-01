<?php

namespace App\Http\Requests;

use App\Rules\PermissionRule;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name.en' => 'required|string|max:30|unique_translation:roles,name,' . $this->role,
            'name.ar' => 'required|string|max:30|unique_translation:roles,name,' . $this->role,
            'permissions' => 'required|array|min:1',
            'permissions.*' => ['required', new PermissionRule()],
            'status' => 'in:0,1',
        ];
    }
}

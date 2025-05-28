<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminRequest extends FormRequest
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
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:70|unique:admins,email,' . $this->admin,
            'image' => 'nullable|image|mimes:jpeg,svg,png,jpg,gif',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:1,0',
            'password' => [Rule::requiredIf($this->is('api/dashboard/admins')), 'confirmed'],
        ];
    }
}

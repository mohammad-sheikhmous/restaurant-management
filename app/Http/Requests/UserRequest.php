<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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
            'first_name' => [$this->is('*/register') ? 'required' : 'sometimes', 'string', 'max:50'],
            'last_name' => [$this->is('*/register') ? 'required' : 'sometimes', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'unique:users,mobile,' . $this->user('user')?->id, 'max:20', 'regex:/^09[0-9]{8}$/'],
            'email' => [$this->is('*/register') ? 'required' : 'sometimes', 'email', 'max:70', 'unique:users,email'],
            'image' => ['nullable', 'image', 'mimes:jpeg,gif,svg,png,jpg'],
            'birthdate' => ['nullable', 'date', 'before:now'],
            'status' => ['in:0,1'],
            'password' => [$this->is('*/register') ? 'required' : 'sometimes', 'confirmed', Password::min(8)
                ->max(50)
                ->letters()
                ->numbers()
                ->symbols()
            ],
        ];
    }
}

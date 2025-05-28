<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use JetBrains\PhpStorm\ArrayShape;

class RegisterRequest extends FormRequest
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
        $data = [
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'mobile' => ['required', 'string', 'max:20', 'regex:/^09[0-9]{8}$/'],
            'email' => ['required', 'email', 'max:70', 'unique:users,email'],
            'image' => ['nullable', 'image', 'mimes:jpeg,gif,svg,png,jpg'],
            'password' => ['required', 'confirmed', Password::min(8)
                ->max(50)
                ->letters()
                ->numbers()
                ->symbols()
            ],
        ];
        if ($this->is('api/dashboard/users'))
            $data['status'] = ['required', 'in:0,1'];

        return $data;
    }
}

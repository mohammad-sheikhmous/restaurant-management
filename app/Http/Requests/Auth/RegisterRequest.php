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
        return [
            'first_name' => ['required', 'string', 'max:60'],
            'last_name' => ['required', 'string', 'max:60'],
            'mobile' => ['required', 'string', 'max:20', 'regex:/^09[3,4,5,7,8][0-9]{7}$/'],
            'email' => ['required', 'email', 'max:70', 'unique:users,email'],
            'image' => ['nullable', 'image', 'mimes:jpeg,gif,svg,png,jpg'],
            'password' => ['required', 'confirmed', Password::min(8)
                ->max(50)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
            ],
        ];
    }
}

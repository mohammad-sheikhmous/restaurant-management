<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAddressRequest extends FormRequest
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
            'name' => 'required|string|max:30',
            'label' => 'required|string|max:30',
            'city' => 'required|string|max:20',
            'area' => 'required|string|max:30',
            'street' => 'required|string|max:40',
            'mobile' => 'nullable|string|max:20',
            'additional_details' => 'nullable|string|max:150    ',
            'latitude' => 'required|decimal:0,7',
            'longitude' => 'required|decimal:0,7',
        ];
    }
}

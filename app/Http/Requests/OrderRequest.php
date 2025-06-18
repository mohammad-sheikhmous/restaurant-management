<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'receiving_method' => 'required|in:delivery,pick_up',
            'payment_method' => 'required|in:cash,wallet',
            'order_notes.*' => 'nullable|string|max:50',
            'address_id' => 'required_if:receiving_method,delivery',
        ];
    }
}

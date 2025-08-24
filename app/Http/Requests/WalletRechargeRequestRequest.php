<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletRechargeRequestRequest extends FormRequest
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
            'proof_image' => 'required|image|mimes:png,jpg,jpeg,svg,gif',
            'transfer_method' => 'required|in:cash,bank',
            'amount' => 'nullable|decimal:0,2|max:1000000',
            'note' => 'nullable|string|max:120',
        ];
    }
}

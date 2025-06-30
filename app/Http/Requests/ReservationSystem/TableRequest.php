<?php

namespace App\Http\Requests\ReservationSystem;

use Illuminate\Foundation\Http\FormRequest;

class TableRequest extends FormRequest
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
            'table_num' => 'required|string|unique:tables,table_num,'.$this->table,
            'seats_count' => 'required|integer|max:20',
            'activation' => 'in:active,inactive',
            'is_combinable' => 'in:0,1',
            'type_id' => 'required|exists:reservation_types,id'
        ];
    }
}

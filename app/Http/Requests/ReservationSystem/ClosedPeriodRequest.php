<?php

namespace App\Http\Requests\ReservationSystem;

use Illuminate\Foundation\Http\FormRequest;

class ClosedPeriodRequest extends FormRequest
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
            'type_id' => 'required|exists:reservation_types,id',
            'full_day' => 'required|in:0,1,true,false',
            'from_date' => 'required|date_format:Y-m-d',
            'to_date' => 'required|date_format:Y-m-d|after_or_equal:from_date',
            'from_time' => 'nullable|required_unless:to_time,null|date_format:H:i',
            'to_time' => 'nullable|required_unless:from_time,null|date_format:H:i|after:from_time',
            'reason' => 'nullable|array|size:2',
            'reason.en' => 'required_unless:reason.ar,null|max:100',
            'reason.ar' => 'required_unless:reason.en,null|max:100',
        ];
    }
}

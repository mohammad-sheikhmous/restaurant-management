<?php

namespace App\Http\Requests\ReservationSystem;

use Illuminate\Foundation\Http\FormRequest;

class BookingPolicyRequest extends FormRequest
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
            'max_revs_duration_hours' => 'required|integer|min:1|max:12',
            'max_pre_booking_days' => 'required|integer|min:1|max:255',
            'min_pre_booking_minutes' => 'required|integer|min:0',
            'revs_cancellability' => 'required|in:0,1',
            'revs_modifiability' => 'required|in:0,1',
            'auto_no_show_minutes' => 'required|integer|min:0',
            'table_combinability' => 'required|in:0,1',
            'manual_confirmation' => 'required|in:0,1',
            'min_people' => 'required|integer|min:1|max:10',
            'max_people' => 'required|integer|gt:min_people|min:4|max:300',
            'interval_minutes' => 'required|integer',
            'deposit_system' => 'required|in:0,1',
            'explanatory_notes.en' => 'required_unless:reason.ar,null',
            'explanatory_notes.ar' => 'required_unless:reason.en,null',
        ];
        if ($this->revs_cancellability) {
            $data['min_revs_cancellability_minutes'] = 'required|integer|lte:min_pre_booking_minutes|min:0';
            $data['revs_cancellability_ratio'] = 'required|integer|min:0';
        }

        if ($this->revs_modifiability) {
            $data['min_revs_modifiability_minutes'] = 'required|integer|lte:min_pre_booking_minutes|min:0';
            $data['revs_modifiability_ratio'] = 'required|integer|min:0';
        }

        if ($this->deposit_system) {
            $data['deposit_value'] = 'required|decimal:0,2|min:0.00';
            $data['num_of_person_per_deposit'] = 'required|integer|min:1';
            $data['time_per_deposit'] = 'required|integer|min:15';
            $data['deposit_customizability'] = 'required|in:0,1';

            if ($this->deposit_customizability) {
                $data['types'] = 'required|array';
                $data['types.*.deposit_value'] = 'required|decimal:0,2|min:0.00';
            }
        }

        return $data;
    }
}

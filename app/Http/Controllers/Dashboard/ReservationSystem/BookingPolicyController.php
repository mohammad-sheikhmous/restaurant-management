<?php

namespace App\Http\Controllers\Dashboard\ReservationSystem;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationSystem\BookingPolicyRequest;
use App\Models\BookingPolicy;
use App\Models\ReservationType;
use Illuminate\Support\Facades\DB;

class BookingPolicyController extends Controller
{
    public function index()
    {
        $policies = BookingPolicy::first();

        return dataJson('policies', $policies, 'Booking policies');
    }

    public function update(BookingPolicyRequest $request)
    {
        try {
            $policies = BookingPolicy::first();

            DB::beginTransaction();
            $policies->update([
                'max_revs_duration_hours' => $request->max_revs_duration_hours,
                'max_pre_booking_days' => $request->max_pre_booking_days,
                'min_pre_booking_minutes' => $request->min_pre_booking_minutes,
                'revs_cancellability' => $request->revs_cancellability,
                'min_revs_cancellability_minutes' => $request->revs_cancellability ? $request->min_revs_cancellability_minutes : null,
                'revs_cancellability_ratio' => $request->revs_cancellability ? $request->revs_cancellability_ratio : null,
                'revs_modifiability' => $request->revs_modifiability,
                'min_revs_modifiability_minutes' => $request->revs_modifiability ? $request->min_revs_modifiability_minutes : null,
                'revs_modifiability_ratio' => $request->revs_modifiability ? $request->revs_modifiability_ratio : null,
                'table_combinability' => $request->table_combinability,
                'manual_confirmation' => $request->manual_confirmation,
                'min_people' => $request->min_people,
                'max_people' => $request->max_people,
                'interval_minutes' => $request->interval_minutes,
                'auto_no_show_minutes' => $request->auto_no_show_minutes,
                'deposit_system' => $request->deposit_system,
                'deposit_value' => $request->deposit_system ? $request->deposit_value : 0,
                'num_of_person_per_deposit' => $request->deposit_system ? $request->num_of_person_per_deposit : null,
                'time_per_deposit' => $request->deposit_system ? $request->time_per_deposit : null,
                'deposit_customizability' => $request->deposit_system ? $request->deposit_customizability : null,
                'explanatory_notes' => $request->explanatory_notes,
            ]);

            if (!$request->deposit_system || !$request->deposit_customizability)
                DB::statement('update reservation_types set deposit_value = 0;');
            else {
                $types = ReservationType::all();

                foreach ($request->types as $key => $value) {
                    $type = $types->first(fn($type) => $type->id == $key);
                    if (!$type)
                        return messageJson("Invalid type with id: $key.", false, 422);

                    $updates[] = [
                        'id' => $type->id,
                        'deposit_value' => $value['deposit_value']
                    ];
                }
                batchUpdate('reservation_types', $updates, 'id');
            }
            DB::commit();

            return messageJson('Policies updated successfully.');

        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
        }
    }
}

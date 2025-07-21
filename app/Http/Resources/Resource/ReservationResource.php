<?php

namespace App\Http\Resources\Resource;

use App\Models\BookingPolicy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use function Termwind\parse;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($request->is('*/dashboard/*')) {
            $data = [
                'id' => $this->id,
                'user_data' => $this->when($request->is('*/dashboard/reservations/*'), [
                    'id' => $this->user_id,
                    // must be first that verify from the user present or not
                    'name' => $this->user ? $this->user->first_name . ' ' . $this->user->last_name : $this->user_data['name'],
                    'email' => $this->user ? $this->user->email : $this->user_data['email'],
                    'mobile' => $this->user ? $this->user->mobile : $this->user_data['mobile'],
                ]),
                'user_name' => $this->when($request->is('*/dashboard/reservations'),
                    $this->user ? $this->user->first_name . ' ' . $this->user->last_name : $this->user_data['name']),
                'revs_date' => $this->revs_date,
                'revs_time' => Carbon::parse($this->revs_time)->format('H:i'),
                'revs_duration' => Carbon::parse($this->revs_duration)->format('H:i'),
                'revs_status' => $this->status,
                'revs_type' => $this->reservationTables->first()->table_data['type'][config('app.locale')],
                'guests_count' => $this->guests_count,
                'tables' => $this->reservationTables->pluck('table_data')->pluck('table_num')->implode(', '),

            ];
            if ($request->is('*/dashboard/reservations/*')) {
                $data['tables'] =
                    $this->reservationTables->map(fn($item) => [
                        'id' => $item->table_id,
                        'table_num' => $item->table_data['table_num'],
                        'seats_count' => $item->table_data['seats_count'],
                    ]);
                $data['deposit_value'] = $this->deposit_value;
                $data['deposit_status'] = $this->deposit_status;
                $data['note'] = $this->note;
                $data['created_at'] = Carbon::parse($this->created_at)->format('Y-m-d H:i');
            }

            return $data;
        } else {
            $booking_policies = BookingPolicy::first();
            $active_direction = $request->is('api/reservations/*');
            $active_status = in_array($this->status, ['not_confirmed', 'pending', 'accepted']);
            $temp_status = in_array($this->status, ['not_confirmed', 'pending']);

            $data = [
                'id' => $this->id,
                'type' => $this->tables->first()->type->name,
                'tables_count' => $this->tables->count(),
                'revs_date' => $this->revs_date,
                'revs_time' => Carbon::parse($this->revs_time)->format('H:i'),
                'revs_duration' => Carbon::parse($this->revs_duration)->format('H:i'),
                'guests_count' => $this->guests_count,
                'status' => $this->status,
                'deposit_value' => $this->deposit_value,
                'deposit_status' => $this->when($active_direction, $this->deposit_status),
                'note' => $this->when($active_direction, $this->note),
                'created_at' => $this->when($active_direction, $this->created_at->format('Y-m-d H:i')),
                'revs_accepted_cancellability' => boolval($booking_policies->revs_cancellability),
                'cancellation_inability_hours' => $this->when($active_direction, null),
                'revs_cancellability_now' => $this->when($active_status, $temp_status || null),
                'revs_accepted_modifiability' => boolval($booking_policies->revs_modifiability),
                'modification_inability_hours' => $this->when($active_direction, null),
                'revs_modifiability_now' => $this->when($active_status, $temp_status || null),
                'auto_no_show_minutes' => $this->when($active_direction && $active_status,
                    $booking_policies->auto_no_show_minutes),
            ];

            // Check if the revs can be cancelled now when revs status is accepted.
            $created_at = Carbon::parse($this->created_at);
            if ($booking_policies->revs_cancellability) {
                $diffBetweenRevsAndCreatedMinutes = max(round($created_at->diffInUTCMinutes($this->revs_date) *
                    $booking_policies->revs_cancellability_ratio / 100, 0), $booking_policies->min_revs_cancellability_minutes);

                if ($active_direction)
                    $data['cancellation_inability_hours'] = round($diffBetweenRevsAndCreatedMinutes / 60, 0);

                if (in_array($this->status, ['accepted']))
                    $data['revs_cancellability_now'] = now()->diffInUTCMinutes($this->revs_date) > $diffBetweenRevsAndCreatedMinutes;
            }

            // Check if the revs can be modified now when revs status is accepted.
            if ($booking_policies->revs_modifiability) {
                $diffBetweenRevsAndCreatedMinutes = max(round($created_at->diffInUTCMinutes($this->revs_date) *
                    $booking_policies->revs_modifiability_ratio / 100, 0), $booking_policies->min_revs_modifiability_minutes);

                if ($active_direction)
                    $data['modification_inability_hours'] = round($diffBetweenRevsAndCreatedMinutes / 60, 0);

                if (in_array($this->status, ['accepted']))
                    $data['revs_modifiability_now'] = now()->diffInUTCMinutes($this->revs_date) > $diffBetweenRevsAndCreatedMinutes;
            }

            return $data;
        }
    }
}

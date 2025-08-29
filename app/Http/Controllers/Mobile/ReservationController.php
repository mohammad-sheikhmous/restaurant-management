<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource\ReservationResource;
use App\Models\BookingPolicy;
use App\Models\ClosedPeriod;
use App\Models\Reservation;
use App\Models\ReservationType;
use App\Models\Table;
use App\Models\WorkingShift;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReservationController extends Controller
{
    public function getAvailableDays()
    {
        $data = $this->availableDays();

        return dataJson('data', [
            'min_people' => $data['booking_policies']->min_people,
            'max_people' => $data['booking_policies']->max_people,
            'max_revs_duration_hours' => $data['booking_policies']->max_revs_duration_hours,
            'available_date' => $data['available_dates'],
        ], 'All Available Dates and Max people.');
    }

    private function availableDays()
    {
        $shifts = WorkingShift::all();

        $booking_policies = BookingPolicy::first();
        $max_pre_booking_days = $booking_policies->max_pre_booking_days;

        // Get closed dates where the type of close is whole restaurant
        $closed_dates = ClosedPeriod::whereNull('type_id')->whereFullDay(1)->get();

        // Get last date by max_pre_booking_days present in booking policies
        $end_day = now()->addDays($max_pre_booking_days);

        $dates = $this->generateDatesFromWorkingShifts($shifts->pluck('day_of_week')->toArray(), $end_day);

        $available_dates = $dates->map(function ($date) use ($closed_dates) {

            $closed_date = $closed_dates->first(function ($closed_date) use ($date) {
                return $closed_date->from_date <= $date && $closed_date->to_date >= $date;
            });
            return [
                'date' => $date,
                'is_available' => !isset($closed_date),
                'reason' => $closed_date->reason ?? null,
            ];
        });

        return [
            'booking_policies' => $booking_policies,
            'available_dates' => $available_dates
        ];
    }

    function generateDatesFromWorkingShifts(array $week_days, $end_date)
    {
        $dates = collect();
        $start = now();
        $end = Carbon::parse($end_date);

        while ($start->lte($end)) {
            if (in_array(strtolower($start->format('l')), $week_days))
                $dates->push($start->toDateString());

            $start->addDay();
        }

        return $dates;
    }

    public function getAvailableTimeSlots()
    {
        $booking_policies = BookingPolicy::first();
        $max_people = $booking_policies->max_people;
        $min_people = $booking_policies->min_people;
        $max_revs_duration_hours = Carbon::createFromTime($booking_policies->max_revs_duration_hours)->format('H:i');

        request()->validate([
            'selected_date' => 'required|date|after_or_equal:today',
            'guests_count' => "required|integer|min:$min_people|max:$max_people",
            'revs_duration' => 'required|date_format:H:i|before_or_equal:' . $max_revs_duration_hours
        ]);

        $time_slots = $this->generateTimeSlots($booking_policies, request()->selected_date,
            request()->revs_duration, request()->guests_count, request()->revs_id ?? null);

        return dataJson('time_slots', $time_slots, 'All time slots in ' . \request()->selected_date);
    }

    private function generateTimeSlots($booking_policies, $selected_date, $revs_duration, $guests_count, $revs_id = null)
    {
        $types = ReservationType::all();

        $interval = $booking_policies->interval_minutes;
        $time_slots = collect();

        $day_of_week = strtolower(Carbon::parse($selected_date)->format('l'));
        $revs_duration_minutes = Carbon::parse($revs_duration)->secondsSinceMidnight() / 60;

        $tables = Table::where('activation', 1)->get();

        $shifts = WorkingShift::whereDayOfWeek($day_of_week)->with('type')->get()
            ->each(function ($shift) use (
                $time_slots, $interval, $types, $day_of_week, $revs_duration_minutes,
                $tables, $selected_date, $guests_count, $revs_id
            ) {
                $slot = Carbon::parse($shift->opening_time);

                while ($slot->copy()->addMinutes($revs_duration_minutes)->lte($shift->closing_time)) {
                    $tables = $tables->where('type_id', $shift->type_id)
                        ->filter(function ($table) use ($shift, $slot, $revs_duration_minutes, $selected_date, $revs_id) {
                            return !Reservation::where(function ($query) {
                                return $query->whereIn('status', ['pending', 'accepted', 'active'])
                                    ->orWhere('status', 'not_confirmed')
                                    ->where('created_at', '>', now()->subMinutes(5));
                            })
                                ->whereRelation('tables', 'tables.id', $table->id)
                                ->where('revs_date', $selected_date)
                                ->where('revs_time', '<', $slot->copy()->addMinutes($revs_duration_minutes)->format('H:i'))
                                ->whereRaw("addtime(revs_time, revs_duration) > time_format('$slot', '%H:%i')")
                                ->where('id', '!=', $revs_id)
                                ->exists();
                        });
                    if (
                        !ClosedPeriod::where(function (Builder $query) use ($selected_date) {
                            // Until the slot is available it has to the selected date is not intersected with closed period.
                            return $query->where('from_date', '<=', $selected_date)
                                ->where('to_date', '>=', $selected_date);
                        })->where(function ($period) use ($slot, $revs_duration_minutes) {
                            // Until the slot is available it has to be the start of revs before the end of closing period,
                            // and the end of revs after the start of closing period.
                            return $period->where('from_time', '<', $slot->copy()->addMinutes($revs_duration_minutes)->format('H:i'))
                                ->where('to_time', '>', $slot->format('H:i'))
                                ->orWhereNull('from_time');
                        })->count()
                        &&
                        (   // check if there are single tables available for selected guests count
                            $tables->where('seats_count', '>=', $guests_count)->count()
                            ||
                            // if single tables not found the table groups will be applied
                            $tables->where('is_combinable', 1)->sum('seats_count') >= $guests_count
                        )
                    )
                        $time_slots->push([
                            'type_id' => $shift->type_id,
                            'type' => $types->where('id', $shift->type_id)->first()->name,
                            'slot' => $slot->format('H:i')
                        ]);
                    $slot->addMinutes($interval);
                }
            });
        $types->whereNotIn('id', $shifts->pluck('type_id')->toArray())->each(function ($type) use ($time_slots) {
            $time_slots->push(['type' => $type->name]);
        });

        return $time_slots->groupBy('type')->map(fn($slot) => $slot->pluck('slot')->toArray());
    }

    public function createTempRevs(Request $request)
    {
        $booking_policies = BookingPolicy::first();
        $max_people = $booking_policies->max_people;
        $min_people = $booking_policies->min_people;
        $max_revs_duration_hours = Carbon::createFromTime($booking_policies->max_revs_duration_hours)->format('H:i');

        $request->validate([
            'selected_date' => 'required|date|after_or_equal:today:before:' . now()->addDays($booking_policies->max_pre_booking_days),
            'guests_count' => "required|integer|min:$min_people|max:$max_people",
            'revs_duration' => 'required|date_format:H:i|before_or_equal:' . $max_revs_duration_hours,
            'type' => 'required|string',
            'selected_slot' => 'required|date_format:H:i',
        ]);
        try {
            $check_from_selected_slot = $this->checkFromSelectedSlot($booking_policies, $request->selected_date,
                $request->revs_duration, $request->guests_count, $request->selected_slot, $request->type);

            if ($check_from_selected_slot['status'] == false)
                return messageJson(
                    $check_from_selected_slot['message'],
                    $check_from_selected_slot['status'],
                    $check_from_selected_slot['status_code']
                );

            $table = $check_from_selected_slot['table'];
            $tables_combination = $check_from_selected_slot['tables_combination'];
            $deposit_value = $check_from_selected_slot['deposit_value'];

            $user = auth('user')->user()->load('wallet');

            if ($deposit_value > ($user->wallet->balance ?? 0)) {
                return messageJson(
                    'Sorry, your wallet balance not enough for creating this reservation.',
                    false,
                    402);
            }

            DB::beginTransaction();

            $revs = Reservation::create([
                'user_id' => $user->id,
                'user_data' => [
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'mobile' => $user->mobile
                ],
                'revs_date' => $request->selected_date,
                'revs_time' => Carbon::parse($request->selected_slot)->format('H:i'),
                'revs_duration' => $request->revs_duration,
                'guests_count' => $request->guests_count,
                'status' => 'not_confirmed',
                'deposit_value' => $deposit_value,
                'deposit_status' => 'pending',
            ]);

            $this->createIntermediates($table, $tables_combination, $revs);

            $user->wallet()->update(['balance' => $user->wallet->balance - $deposit_value]);

            $user->walletTransactions()->create([
                'user_data' => [
                    'name' => $user->name,
                    'mobile' => $user->mobile,
                    'email' => $user->email,
                ],
                'amount' => $deposit_value,
                'type' => 'debit',
                'description' => 'The deposit value paid.',
                'reservation_id' => $revs->id
            ]);

            DB::commit();

            // Number of hours of inability to cancel the revs
            $cancellation_inability_hours = round(
                now()->diffInUTCHours($request->selected_date)
                *
                $booking_policies->revs_cancellability_ratio / 100,
                0);

            // Number of hours of inability to modify the revs
            $modification_inability_hours = round(
                now()->diffInUTCHours($request->selected_date)
                *
                $booking_policies->revs_modifiability_ratio / 100,
                0);

            return dataJson(
                'data',
                [
                    'revs_id' => $revs->id,
                    'deposit_value' => $deposit_value,
                    'confirmation_time' => $booking_policies->temp_revs_conf_minutes,
                    'tables_count' => $revs->tables->count(),
                    'cancellation_inability_hours' =>
                        $booking_policies->revs_cancellability
                            ?
                            max($cancellation_inability_hours, $booking_policies->min_revs_cancellability_minutes / 60)
                            : null,
                    'modification_inability_hours' =>
                        $booking_policies->revs_modifiability
                            ?
                            max($modification_inability_hours, $booking_policies->min_revs_modifiability_minutes / 60)
                            : null,
                    'explanatory_notes' => $booking_policies->explanatory_notes
                ],
                'New Temporary Reservation created, Confirm before 5 minutes else will be cancelled.',
                true,
                201
            );
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
        }
    }

    private function checkFromSelectedSlot($booking_policies, $selected_date, $revs_duration, $guests_count, $selected_slot, $type)
    {
        $revs_duration_minutes = Carbon::parse($revs_duration)->secondsSinceMidnight() / 60;
        $selected_slot = Carbon::parse($selected_slot);
        $interval = $booking_policies->interval_minutes;

        if ($selected_slot->minute % $interval != 0)
            return [
                'message' => 'Invalid selected slot',
                'status' => false,
                'status_code' => 422
            ];

        $tables = Table::whereRelation('type', 'name->' . config('app.locale'), $type)
            ->where('activation', 1)
            ->get();

        if ($tables->isEmpty())
            return [
                'message' => 'Invalid type',
                'status' => false,
                'status_code' => 422
            ];

        $tables = $tables->filter(function ($table) use ($selected_date, $selected_slot, $revs_duration_minutes) {
            return !Reservation::where(function ($query) {
                return $query->whereIn('status', ['pending', 'accepted', 'active'])
                    ->orWhere('status', 'not_confirmed')
                    ->where('created_at', '>', now()->subMinutes(5));
            })
                ->whereRelation('tables', 'tables.id', $table->id)
                ->where('revs_date', $selected_date)
                ->where('revs_time', '<', $selected_slot->copy()->addMinutes($revs_duration_minutes)->format('H:i'))
                ->whereRaw("addtime(revs_time, revs_duration) > time_format('$selected_slot', '%H:%i')")
                ->exists();
        });

        if (
            ClosedPeriod::where(function (Builder $query) use ($selected_date) {
                return $query->where('from_date', '<=', $selected_date)
                    ->where('to_date', '>=', $selected_date);
            })->where(function ($period) use ($selected_slot, $revs_duration_minutes) {
                return $period->where('from_time', '<', $selected_slot->copy()->addMinutes($revs_duration_minutes)->format('H:i'))
                    ->where('to_time', '>', $selected_slot->format('H:i'))
                    ->orWhereNull('from_time');
            })->count()
        )
            return [
                'message' => 'The selected period overlaps with a closed period.!',
                'status' => false,
                'status_code' => 409
            ];

        // check if there are single tables available for selected guests count
        $table = $tables->where('seats_count', '>=', $guests_count)->sortBy('seats_count')->first();

        // if single tables not found the table groups will be applied
        $tables_combination = collect($this->generateCombinations($tables->where('is_combinable', 1)))
            ->where(function ($combination) use ($guests_count) {
                return $combination->sum('seats_count') >= $guests_count;
            })
            ->sortBy(function ($combination) {
                return $combination->sum('seats_count');
            })->first();

        if (!$table && !$tables_combination)
            return [
                'message' => 'Sorry, Reservations are no longer available at this time. Choose a second time.',
                'status' => false,
                'status_code' => 409
            ];

        if ($booking_policies->deposit_system) {
            if ($booking_policies->deposit_customizability)
                $deposit_value = $table ?
                    $table->type->deposit_value :
                    $tables_combination->first()->type->deposit_value;
            else
                $deposit_value = $booking_policies->deposit_value;

            $deposit_value =
                $deposit_value
                *
                round($guests_count / $booking_policies->num_of_person_per_deposit, 0)
                *
                round($revs_duration_minutes / $booking_policies->time_per_deposit, 0);
        } else
            $deposit_value = 0;

        return [
            'status' => true,
            'deposit_value' => $deposit_value,
            'table' => $table,
            'tables_combination' => $tables_combination,
        ];
    }

    private function generateCombinations(Collection $tables): array
    {
        $results = [];

        $tablesArray = $tables->values()->all();

        $count = count($tablesArray);

        // Create all binary and ternary combinations
        for ($i = 2; $i <= min($count, 4); $i++) {
            $results = array_merge($results, $this->combinations($tablesArray, $i));
        }

        // Convert each element from results to collect
        return array_map(fn($combo) => collect($combo), $results);
    }

    // Generate non-repeat combinations (nCr)
    private function combinations(array $array, $r): array
    {
        $results = [];

        $recurse = function ($start, $combo) use ($array, $r, &$results, &$recurse) {
            if (count($combo) == $r) {
                $results[] = $combo;
                return;
            }

            for ($i = $start; $i < count($array); $i++) {
                $newCombo = array_merge($combo, [$array[$i]]);
                $recurse($i + 1, $newCombo);
            }
        };

        $recurse(0, []);

        return $results;
    }

    public function confirmTempRevs(Request $request, $id)
    {
        $revs = auth('user')->user()->reservations()
            ->where('id', $id)
            ->where('status', 'not_confirmed')
            ->where('created_at', '>', now()->subMinutes())
            ->where('created_at', '>', now()->subMinutes(5))
            ->first();
        if (!$revs)
            return messageJson('Reservation not found.!', false, 404);

        $revs->update(['status' => 'pending', 'note' => $request->note]);

        return messageJson('The Reservation confirmed successfully.');
    }

    public function index()
    {
        $booking_policies = BookingPolicy::first();
        $conf_minutes = $booking_policies->temp_revs_conf_minutes;

        $reservations = auth('user')->user()->reservations()
            ->whereRaw("(
                status != 'not_confirmed'
                    or (status = 'not_confirmed' and created_at > DATE_SUB(NOW(), INTERVAL {$conf_minutes} MINUTE))
            )")
            ->with('tables.type')
            ->orderBy('id', 'desc')
            ->get();

        return dataJson(
            'reservations',
            ReservationResource::collection($reservations->map(function ($revs) use ($booking_policies) {
                $revs->booking_policies = $booking_policies;
                return $revs;
            })),
            'All reservations'
        );
    }

    public function show($id)
    {
        $booking_policies = BookingPolicy::first();
        $conf_minutes = $booking_policies->temp_revs_conf_minutes;

        $reservation = auth('user')->user()->reservations()
            ->where('id', $id)
            ->whereRaw("(
                status != 'not_confirmed'
                    or (status = 'not_confirmed' and created_at > DATE_SUB(NOW(), INTERVAL {$conf_minutes} MINUTE))
            )")
            ->with('tables.type')
            ->first();
        if (!$reservation)
            return messageJson('Reservation not found.!', false, 404);

        $reservation->qr_code = $this->generateQRCode($reservation->revs_number);

        $reservation->booking_policies = $booking_policies;

        return dataJson(
            'reservation',
            ReservationResource::make($reservation),
            'Reservation details'
        );
    }

    public function generateQRCode($revs_number): string
    {
        $qr = QrCode::size(200)->generate($revs_number);

        return base64_encode($qr);
    }

    public function edit($id)
    {
        $reservation = auth('user')->user()->reservations()
            ->whereId($id)
            ->whereIn('status', ['not_confirmed', 'pending', 'accepted'])
            ->with('tables.type')
            ->first();
        if (!$reservation)
            return messageJson('Reservation not found.!', false, 404);

        $data = $this->availableDays();
        $available_dates = $data['available_dates'];
        $booking_policies = $data['booking_policies'];

        if ($reservation->status == 'accepted' && !$booking_policies->revs_modifiability)
            return messageJson('Restaurant policy does not allow you to modify a reservation.', false, 403);

        if ($reservation->status == 'accepted') {
            $diffBetweenRevsAndCreatedMinutes = max(round(
                Carbon::parse($reservation->created_at)->diffInUTCMinutes($reservation->revs_date)
                *
                $booking_policies->revs_modifiability_ratio / 100, 0), $booking_policies->min_revs_modifiability_minutes);

            if (!now()->diffInUTCMinutes($reservation->revs_date) > $diffBetweenRevsAndCreatedMinutes)
                return messageJson('You have exceeded the specified period for modification.!', false, 403);
        }

        $time_slots = $this->generateTimeSlots($booking_policies, $reservation->revs_date, $reservation->revs_duration,
            $reservation->guests_count, $reservation->id);

        return dataJson('data', [
            'min_people' => $data['booking_policies']->min_people,
            'max_people' => $data['booking_policies']->max_people,
            'max_revs_duration_hours' => $data['booking_policies']->max_revs_duration_hours,
            'revs_data' => [
                'id' => $reservation->id,
                'revs_date' => $reservation->revs_date,
                'revs_duration' => $reservation->revs_duration,
                'guests_count' => $reservation->guests_count,
                'revs_time' => $reservation->revs_time,
                'type' => $reservation->tables->first()->type->name,
            ],
            'available_date' => $available_dates,
            'time_slots' => $time_slots,
        ], "All details for modifying reservation with Id: $id.");
    }

    public function update(Request $request, $id)
    {
        $reservation = auth('user')->user()->reservations()
            ->whereId($id)
            ->whereIn('status', ['not_confirmed', 'pending', 'accepted'])
            ->with('tables.type')
            ->first();
        if (!$reservation)
            return messageJson('Reservation not found.!', false, 404);

        $booking_policies = BookingPolicy::first();

        if ($reservation->status == 'accepted') {
            if (!$booking_policies->revs_modifiability)
                return messageJson('Restaurant policy does not allow you to modify a reservation.', false, 403);

            else {
                $diffBetweenRevsAndCreatedMinutes = max(round(
                    Carbon::parse($reservation->created_at)->diffInUTCMinutes($reservation->revs_date)
                    *
                    $booking_policies->revs_modifiability_ratio / 100, 0), $booking_policies->min_revs_modifiability_minutes);

                if (!now()->diffInUTCMinutes($reservation->revs_date) > $diffBetweenRevsAndCreatedMinutes)
                    return messageJson('You have exceeded the specified period for modification.!', false, 403);
            }
        }
        // if no changes
        if (
            $request->selected_date == $reservation->revs_date &&
            $request->guests_count == $reservation->guests_count &&
            Carbon::parse($request->revs_duration)->equalTo($reservation->revs_duration) &&
            $request->type === $reservation->tables->first()->type->name &&
            Carbon::parse($request->selected_slot)->equalTo($reservation->revs_time)
        )
            return messageJson('No changed to the reservation.', true, 200);

        $max_people = $booking_policies->max_people;
        $min_people = $booking_policies->min_people;
        $max_revs_duration_hours = Carbon::createFromTime($booking_policies->max_revs_duration_hours)->format('H:i');

        $request->validate([
            'selected_date' => 'required|date|after_or_equal:today|before:' . now()->addDays($booking_policies->max_pre_booking_days),
            'guests_count' => "required|integer|min:$min_people|max:$max_people",
            'revs_duration' => 'required|date_format:H:i|before_or_equal:' . $max_revs_duration_hours,
            'type' => 'required|string',
            'selected_slot' => 'required|date_format:H:i',
        ]);
        try {
            $check_from_selected_slot = $this->checkFromSelectedSlot($booking_policies, $request->selected_date,
                $request->revs_duration, $request->guests_count, $request->selected_slot, $request->type);

            if ($check_from_selected_slot['status'] == false)
                return messageJson($check_from_selected_slot['message'],
                    $check_from_selected_slot['status'], $check_from_selected_slot['status_code']);

            $table = $check_from_selected_slot['table'];
            $tables_combination = $check_from_selected_slot['tables_combination'];
            $deposit_value = $check_from_selected_slot['deposit_value'];

            DB::beginTransaction();
            $reservation->update([
                'revs_date' => $request->selected_date,
                'revs_time' => Carbon::parse($request->selected_slot)->format('H:i'),
                'revs_duration' => $request->revs_duration,
                'guests_count' => $request->guests_count,
                'deposit_value' => $deposit_value,
            ]);
            $reservation->tables()->detach($reservation->tables->pluck('id'));

            $this->createIntermediates($table, $tables_combination, $reservation);
            DB::commit();

            return messageJson('Reservation modified successfully.');

        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
        }
    }

    private function createIntermediates($table, $tables_combination, $revs): void
    {
        if ($table && (!isset($tables_combination) || $table->seats_count <= $tables_combination->sum('seats_count'))) {
            $revs->tables()->attach($table->id, [
                'table_data' => json_encode([
                    'table_num' => $table->table_num,
                    'seats_count' => $table->seats_count,
                    'type' => $table->type->getTranslations('name')
                ])
            ]);
        } else {
            $revs->tables()->attach($tables_combination->groupBy('id')->map(function ($tables) {
                return [
                    'table_data' => json_encode([
                        'table_num' => $tables->first()->table_num,
                        'seats_count' => $tables->first()->seats_count,
                        'type' => $tables->first()->type->getTranslations('name'),
                    ])
                ];
            })->toArray());
        }
    }

    public function cancel($id)
    {
        $reservation = auth('user')->user()->reservations()
            ->whereId($id)
            ->whereIn('status', ['not_confirmed', 'pending', 'accepted'])
            ->first();
        if (!$reservation)
            return messageJson('Reservation not found.!', false, 404);

        $booking_policies = BookingPolicy::first();

        $deposit_status = 'pending';

        if ($reservation->status == 'accepted') {
            if (!$booking_policies->revs_cancellability)
                return messageJson('Restaurant policy does not allow you to cancel a reservation.', false, 403);

            else {
                $diffBetweenRevsAndCreatedMinutes = max(round(
                    Carbon::parse($reservation->created_at)->diffInUTCMinutes($reservation->revs_date)
                    *
                    $booking_policies->revs_cancellability_ratio / 100,
                    0), $booking_policies->min_revs_cancellability_minutes);

                if (!now()->diffInUTCMinutes($reservation->revs_date) > $diffBetweenRevsAndCreatedMinutes)
                    $deposit_status = 'forfeited';
            }
        }
        $reservation->update(['status' => 'cancelled', 'deposit_status' => $deposit_status]);

        return messageJson('Your Reservation cancelled.');
    }
}

<?php

namespace App\Http\Controllers\Dashboard\ReservationSystem;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource\ReservationResource;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::latest()
            ->with(['user:id,first_name,last_name', 'reservationTables:reservation_id,table_data'])
            ->paginate(\request()->limit ?? 10);

        $page = numberToOrdinalWord(request()->page ?? 1);

        return dataJson(
            'reservations',
            (ReservationResource::collection($reservations))->response()->getData(true),
            "All Reservations for {$page} page."
        );
    }

    public function show($id)
    {
        $reservation = Reservation::with([
            'user:id,first_name,last_name,mobile,email',
            'reservationTables:reservation_id,table_id,table_data'
        ])->find($id);
        if (!$reservation)
            return messageJson('This reservation not found.!', false, 404);

        return dataJson('reservation', ReservationResource::make($reservation),
            "Reservation with id: $id returned.");
    }

    public function changeStatus($id)
    {
        request()->validate([
            'status' => 'required|string|in:pending,accepted,active,completed,cancelled,no_show,rejected',
        ]);

        $reservation = Reservation::find($id);
        if (!$reservation)
            return messageJson('This reservation not found.!', false, 404);

        $current = $reservation->status;
        $target = request()->input('status');

        $allowedTransitions = [
            'pending' => ['accepted', 'cancelled', 'rejected'],
            'accepted' => ['active', 'cancelled', 'no_show'],
            'active' => ['completed'],
            'completed' => [],
            'cancelled' => [],
            'no_show' => [],
            'rejected' => [],
        ];

        if (!in_array($target, $allowedTransitions[$current]))
            return messageJson("Invalid status transition from '$current' to '$target'.", false, 422);

        $reservation->status = $target;
//        $reservation->status_changed_at = now(); // optional tracking field
        $reservation->save();

        return messageJson("Reservation status updated successfully.");
    }

    public function destroy($id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation)
            return messageJson('This reservation not found.!', false, 404);

        $deletableStatuses = ['cancelled', 'rejected', 'completed', 'no_show'];

        if (!in_array($reservation->status, $deletableStatuses)) {
            $message = "Cannot delete reservation with status '{$reservation->status}'!. Please cancel it instead.";
            return messageJson($message, false, 403);
        }
        $reservation->delete();

        return messageJson('This reservation deleted successfully.');
    }
}

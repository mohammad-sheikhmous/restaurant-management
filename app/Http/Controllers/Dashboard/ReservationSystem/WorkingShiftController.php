<?php

namespace App\Http\Controllers\Dashboard\ReservationSystem;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationSystem\WorkingShiftRequest;
use App\Http\Resources\Resource\WorkingShiftResource;
use App\Models\WorkingShift;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkingShiftController extends Controller
{
    public function index()
    {
        $shifts = WorkingShift::with('type')->get();


        return dataJson('working_shifts', WorkingShiftResource::collection($shifts), 'All working shifts');
    }

    public function show($id)
    {
        $shift = WorkingShift::with('type')->find($id);
        if (!$shift)
            return messageJson('Shift not found', false, 404);

        return dataJson('shift', WorkingShiftResource::make($shift), "The shift with id: $id returned");
    }

    public function store(WorkingShiftRequest $request)
    {
        WorkingShift::create($request->all());

        return messageJson('New shift created.', true, 201);
    }

    public function update(WorkingShiftRequest $request, $id)
    {
        $shift = WorkingShift::find($id);
        if (!$shift)
            return messageJson('Shift not found', false, 404);

        $shift->update($request->all());

        return messageJson('The shift updated successfully.');
    }

    public function destroy($id)
    {
        $shift = WorkingShift::find($id);
        if (!$shift)
            return messageJson('Shift not found', false, 404);

        $shift->delete();

        return messageJson('The shift deleted.');
    }
}

<?php

namespace App\Http\Controllers\Dashboard\ReservationSystem;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationSystem\ClosedPeriodRequest;
use App\Http\Resources\Resource\ClosedPeriodResource;
use App\Models\ClosedPeriod;

class ClosedPeriodController extends Controller
{
    public function index()
    {
        $periods = ClosedPeriod::with('type')->paginate(request()->limit ?? 10);

        $page = numberToOrdinalWord(request()->page ?? 1);

        return dataJson('periods', (ClosedPeriodResource::collection($periods)->response()->getData(true)),
            "All periods for {$page} page.");
    }

    public function show($id)
    {
        $period = ClosedPeriod::with('type')->find($id);
        if (!$period)
            return messageJson('Period not found.', false, 404);

        return dataJson('period', ClosedPeriodResource::make($period), "The period with id: {$id} returned.");
    }

    public function store(ClosedPeriodRequest $request)
    {
        ClosedPeriod::create($request->all());

        return messageJson('New period created.', true, 201);
    }

    public function update(ClosedPeriodRequest $request, $id)
    {
        $period = ClosedPeriod::find($id);
        if (!$period)
            return messageJson('Period not found.', false, 404);

        $period->update($request->all());

        return messageJson('The period updated successfully.');
    }

    public function destroy($id)
    {
        $period = ClosedPeriod::find($id);
        if (!$period)
            return messageJson('Period not found.', false, 404);

        $period->delete();

        return messageJson('The period deleted successfully.');
    }
}

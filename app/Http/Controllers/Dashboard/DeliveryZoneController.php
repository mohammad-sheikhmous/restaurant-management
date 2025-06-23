<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryZoneRequest;
use App\Http\Resources\Resource\DeliveryZoneResource;
use App\Models\DeliveryZone;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DeliveryZoneController extends Controller
{
    public function index()
    {
        $zones = DeliveryZone::select('id', 'name', 'coordinates', 'status')
            ->withCount(['users' => function ($query) {
                return $query->select(DB::raw('count(distinct user_id)'));
            }])->get();

        return dataJson('delivery_zones', DeliveryZoneResource::collection($zones), 'All delivery zones');
    }

    public function show($id)
    {
        $zone = DeliveryZone::find($id);
        if (!$zone)
            return messageJson('Zone not found', false, 404);

        return dataJson('delivery_zone', DeliveryZoneResource::make($zone), "Delivery zone returned");
    }

    public function store(DeliveryZoneRequest $request)
    {
        $zone = DeliveryZone::create($request->only('name', 'coordinates', 'status'))
            ->mergeCasts(['coordinates' => 'array']);

        $box = getBoundingBox($zone->coordinates);

        assignAddressesToZone($zone, $box);

        Cache::forget('zones');

        return messageJson('New Zone created successfully', true, 201);
    }

    public function update(DeliveryZoneRequest $request, $id)
    {
        $zone = DeliveryZone::find($id);
        if (!$zone)
            return messageJson('Zone not found', false, 404);

        $old_zone = $zone;
        $zone->update($request->only('name', 'coordinates', 'status'));

        $box = getBoundingBox(
            $zone->mergeCasts(['coordinates' => 'array'])->coordinates,
            $old_zone->mergeCasts(['coordinates' => 'array'])->coordinates
        );
        assignAddressesToZone($zone, $box);

        Cache::forget('zones');

        return messageJson('The Zone updated successfully');
    }

    public function destroy($id)
    {
        $zone = DeliveryZone::find($id);
        if (!$zone)
            return messageJson('Zone not found', false, 404);


        $zone->delete();
        Cache::forget('zones');

        return messageJson('The Zone deleted successfully');
    }

    public function changeStatus($id)
    {
        $zone = DeliveryZone::find($id);
        if (!$zone)
            return messageJson('Zone not found', false, 404);

        $zone->update(['status' => !$zone->status]);
        Cache::forget('zones');

        return messageJson('The zone status updated');
    }
}

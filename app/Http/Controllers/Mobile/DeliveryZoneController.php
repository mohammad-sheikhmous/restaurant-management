<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource\DeliveryZoneResource;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DeliveryZoneController extends Controller
{
    public function index()
    {
        if (!Cache::has('zones'))
            Cache::remember('zones', 900, function () {
                return DeliveryZone::select('id', 'name', 'coordinates')->whereStatus(1)->get();
            });
        $zones = Cache::get('zones');

        return dataJson('delivery_zones', DeliveryZoneResource::collection($zones), 'All supported delivery areas');
    }
}

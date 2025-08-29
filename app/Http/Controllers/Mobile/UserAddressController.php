<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAddressRequest;
use App\Http\Resources\Resource\UserAddressResource;
use App\Models\DeliveryZone;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class UserAddressController extends Controller
{
    public function index()
    {
        $addresses = auth('user')->user()->addresses()
            ->select('id', 'name', 'city', 'area', 'street', 'delivery_zone_id')
            ->with('deliveryZone:id,status')
            ->latest()
            ->get();

        if ($addresses->isEmpty())
            return messageJson('There is no addresses', false, 404);

        return dataJson('addresses', UserAddressResource::collection($addresses), 'All user addresses');
    }

    public function show($id)
    {
        $address = auth('user')->user()->addresses()->with('deliveryZone:id,status')->find($id);
        if (!$address)
            return messageJson("Address not found", false, 404);

        return dataJson('address', UserAddressResource::make($address), "address with id: $id returned");
    }

    public function store(UserAddressRequest $request)
    {
        try {
            $zones = Cache::has('zones') ? Cache::get('zones') : DeliveryZone::whereStatus(1)->get();

            $lat = $request->latitude;
            $lng = $request->longitude;
            foreach ($zones as $zone) {
                $polygon = json_decode($zone->coordinates);
                $box = getBoundingBox($polygon);
                if (($box['max_lat'] >= $lat) != ($box['min_lat'] > $lat)
                    &&
                    ($box['max_lng'] >= $lng) != ($box['min_lng'] > $lng)
                ) {
                    if (isPointInPolygon(['lat' => $lat, 'lng' => $lng], $polygon)) {

                        // to store distance array from calculateDistance function
                        $setting = Setting::first();
                        $distance = $this->calculateDistance(
                            $setting->latitude, $setting->longitude,
                            $lat, $lng
                        );

                        $address = auth('user')->user()->addresses()->create([
                            ...$request->all(),
                            'duration' => $distance['duration'],
                            'distance' => $distance['distance'],
                            'delivery_zone_id' => $zone->id
                        ]);
                        return messageJson('New address created', true, 201);
                    }
                }
            }
            return messageJson(
                'Sorry, This address is located in unsupported delivery area',
                true,
                422);

        } catch (\Exception $exception) {
            return exceptionJson();
        }
    }

    private function calculateDistance($lat_from, $lon_from, $lat_to, $lon_to)
    {
        $origin = $lat_from . ',' . $lon_from;
        $destination = $lat_to . ',' . $lon_to;

        $response = Http::get("https://maps.googleapis.com/maps/api/directions/json", [
            'origin' => $origin,
            'destination' => $destination,
            'key' => 'AIzaSyD9zQQNoowad3i_Fycd6YrfbR2mfysHtnQ',
        ]);
        $data = $response->json();

        $distanceInMeters = $data['routes'][0]['legs'][0]['distance']['value'] ?? 0; // by meters
        $distanceInKm = round($distanceInMeters / 1000, 1);

        $durationInMinutes = $data['routes'][0]['legs'][0]['duration']['text'] ?? 0; // by minutes

        return [
            'distance' => $distanceInKm,
            'duration' => $durationInMinutes
        ];
    }

    public function destroy($id)
    {
        $address = auth('user')->user()->addresses()->with(['orders' => function ($query) {
            return $query->whereIn('status', ['pending', 'accepted', 'preparing', 'prepared',
                'delivering']);
        }])->find($id);
        if (!$address)
            return messageJson("Address not found", false, 404);

        if ($address->orders->isNotEmpty()) {
            $message = 'This address cannot be deleted because there is a order in processing associated with it.';
            return messageJson($message, false, 409);
        }
        $address->delete();

        return messageJson("Address deleted");
    }
}

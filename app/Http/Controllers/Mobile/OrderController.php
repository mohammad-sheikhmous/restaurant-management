<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function getDetailsForCreatingOrder()
    {

    }

    public function calculateDeliveryPrice()
    {
        $origin = "33.50,36.31";
        $destination = "33.49,36.32";

        $response = Http::get("https://maps.googleapis.com/maps/api/directions/json", [
            'origin' => $origin,
            'destination' => $destination,
            'key' => 'AIzaSyD9zQQNoowad3i_Fycd6YrfbR2mfysHtnQ',
        ]);

        $data = $response->json();

        $distanceInMeters = $data['routes'][0]['legs'][0]['distance']['value']; // بالمتر
        $distanceInKm = $distanceInMeters / 1000;

        return $distanceInKm;
    }
}

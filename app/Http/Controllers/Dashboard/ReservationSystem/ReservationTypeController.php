<?php

namespace App\Http\Controllers\Dashboard\ReservationSystem;

use App\Casts\Translated;
use App\Http\Controllers\Controller;
use App\Models\ReservationType;

class ReservationTypeController extends Controller
{
    public function index()
    {
        $types = ReservationType::all()->each(fn($type) => $type->mergeCasts(['name' => Translated::class]));

        return dataJson('types', $types, 'Reservation Types');
    }
}

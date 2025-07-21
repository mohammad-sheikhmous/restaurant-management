<?php

namespace App\Http\Controllers\Dashboard\ReservationSystem;

use App\Casts\Translated;
use App\Http\Controllers\Controller;
use App\Models\ReservationType;
use Illuminate\Http\Request;

class ReservationTypeController extends Controller
{
    public function index()
    {
        $types = ReservationType::all()->each(fn($type) => $type->mergeCasts(['name' => Translated::class]));

        return dataJson('types', $types, 'Reservation Types');
    }

    public function show($id)
    {
        $type = ReservationType::find($id);
        if (!$type)
            return messageJson('This type not found.!', false, 404);

        return dataJson('type', $type, "Type with id: $id returned.");
    }

    public function store(Request $request)
    {
        $request->validate([
            'name.en' => 'required|string|max:30',
            'name.ar' => 'required|string|max:30'
        ]);

        ReservationType::create($request->only('name'));

        return messageJson('New Type created', true, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name.en' => 'required|string|max:30',
            'name.ar' => 'required|string|max:30'
        ]);
        $type = ReservationType::find($id);
        if (!$type)
            return messageJson('This type not found.!', false, 404);

        $type->update($request->only('name'));

        return messageJson('Type updated successfully.');
    }

    public function destroy($id)
    {
        $type = ReservationType::find($id);
        if (!$type)
            return messageJson('This type not found.!', false, 404);

        $type->delete();

        return messageJson('Type deleted successfully.');
    }
}

<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return messageJson('You are logged in');
});
Route::get('test1', function () {
    return view('welcome');
});

Route::post('test2', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'name' => 'required|array',
        'coordinates' => 'required|json',
    ]);

    \App\Models\DeliveryZone::create([
        'name' => $request->name,
        'coordinates' => $request->coordinates,
    ]);

    return redirect()->back();
})->name('test2');

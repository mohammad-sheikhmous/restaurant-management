<?php

use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('exceptionJson')) {
    function exceptionJson()
    {
        return response()->json([
            'status' => false,
            'status_code' => 500,
            'message' => __('Something went wrong')
        ], 500);
    }
}


if (!function_exists('messageJson')) {
    function messageJson(string|array $messageVal, bool $status = true, int $code = 200, string $messageKey = 'message')
    {
        return response()->json([
            'status' => $status,
            'status_code' => $code,
            $messageKey => $messageVal,
        ], $code);
    }
}

if (!function_exists('dataJson')) {
    function dataJson(string $dataKey, mixed $data, string $message = "", bool $status = true, int $code = 200)
    {
        return response()->json([
            'status' => $status,
            'status_code' => $code,
            'message' => $message,
            $dataKey => $data,
        ], $code);
    }
}

if (!function_exists('storeImage')) {
    function storeImage(string $name, mixed $image, string $disk, string $path = ""): string
    {
        $image_name = $path . '/' . Str::replace(' ', '-', $name) . '-' . time() . '.' .
            $image->getClientOriginalExtension();

        Storage::disk($disk)->putFileAs('', $image, $image_name);

        return $image_name;
    }
}

if (!function_exists('deleteImage')) {
    function deleteImage(mixed $stored_img_name, string $disk): void
    {
        if (
            $stored_img_name && !Str::startsWith($stored_img_name, 'default') &&
            Storage::disk($disk)->exists($stored_img_name)
        ) {
            Storage::disk($disk)->delete($stored_img_name);
        }
    }
}

if (!function_exists('updateImage')) {
    function updateImage(string $new_name, string $stored_img_name, mixed $image, string $disk, string $path = ""): string
    {
        deleteImage($stored_img_name, $disk);

        return storeImage($new_name, $image, $disk);
    }
}

// Ray Casting method to check if a point is inside a polygon
if (!function_exists('isPointInPolygon')) {
    function isPointInPolygon(array $point, array $polygon)
    {
        $x = $point['lng'];
        $y = $point['lat'];
        $n = count($polygon);
        $inside = false;

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $x1 = $polygon[$j]['lng'];// x-coordinate for the first point of edge
            $y1 = $polygon[$j]['lat'];// y-coordinate for the first point of edge
            $x2 = $polygon[$i]['lng'];// x-coordinate for the last point of edge
            $y2 = $polygon[$i]['lat'];// y-coordinate for the last point of edge

            $intersect =
                (($y2 >= $y) != ($y1 > $y))// check that the point's y-coordinate is within the edge's y-range
                &&
                // check if the point's x-coordinate is less than or equal intersection x-coordinate
                ($x <= ($y - $y1) * ($x2 - $x1) / (($y2 - $y1) ?: 1e-10) + $x1);

            if ($intersect)
                $inside = !$inside;
        }
        return $inside;
    }
}

if (!function_exists('getBoundingBox')) {
    function getBoundingBox($polygon1, $polygon2 = [])
    {
        $lats = array_column([...$polygon1, ...$polygon2], 'lat');
        $lngs = array_column([...$polygon1, ...$polygon2], 'lng');

        return [
            'min_lat' => min($lats),
            'max_lat' => max($lats),
            'min_lng' => min($lngs),
            'max_lng' => max($lngs),
        ];
    }
}

// To assign addresses whose zones have been deleted or disabled to a new zone after creating or updating it
if (!function_exists('assignAddressesToZone')) {
    function assignAddressesToZone($zone, $bounding_box)
    {
        $addresses = UserAddress::whereNull('delivery_zone_id')
            ->orWhere(function ($query) {
                return $query->whereRelation('deliveryZone', 'status', 0);
            })
            ->whereBetween('latitude', [$bounding_box['min_lat'], $bounding_box['max_lat']])
            ->whereBetween('longitude', [$bounding_box['min_lng'], $bounding_box['max_lng']])
            ->chunk(500, function ($addresses) use ($zone) {
                $updates = [];

                foreach ($addresses as $address)
                    if (isPointInPolygon(['lat' => $address->latitude, 'lng' => $address->longitude], $zone->coordinates))
                        $updates[] = [
                            'id' => $address->id,
                            'delivery_zone_id' => $zone->id
                        ];
                batchUpdate('user_addresses', $updates, 'id');
            });
    }
}

if (!function_exists('batchUpdate')) {
    function batchUpdate($table, $data, $index)
    {
        if (empty($data)) return;

        $cases = [];
        $ids = [];

        foreach ($data as $row) {
            $id = $row[$index];
            $ids[] = $id;
            foreach ($row as $col => $val) {
                if ($col == $index) continue;
                $cases[$col][] = "WHEN {$index} = {$id} THEN " . DB::getPdo()->quote($val);
            }
        }

        $sql = "UPDATE {$table} SET ";
        foreach ($cases as $col => $whens) {
            $sql .= "{$col} = CASE " . implode(' ', $whens) . " ELSE {$col} END, ";
        }

        $sql = rtrim($sql, ', ') . " WHERE {$index} IN (" . implode(',', $ids) . ")";
        DB::statement($sql);
    }
}

function numberToOrdinalWord(int $number): string
{
    $words = [
        1 => 'first', 2 => 'second', 3 => 'third', 4 => 'fourth', 5 => 'fifth', 6 => 'sixth', 7 => 'seventh',
        8 => 'eighth', 9 => 'ninth', 10 => 'tenth', 11 => 'eleventh', 12 => 'twelfth', 13 => 'thirteenth',
        14 => 'fourteenth', 15 => 'fifteenth', 16 => 'sixteenth', 17 => 'seventeenth', 18 => 'eighteenth',
        19 => 'nineteenth', 20 => 'twentieth', 30 => 'thirtieth', 40 => 'fortieth', 50 => 'fiftieth',
        60 => 'sixtieth', 70 => 'seventieth', 80 => 'eightieth', 90 => 'ninetieth',
    ];

    if (isset($words[$number])) {
        return $words[$number];
    }

    $tens = floor($number / 10) * 10;
    $units = $number % 10;

    if ($tens > 0 && $units > 0 && isset($words[$tens]) && isset($words[$units])) {
        return str_replace('y', 'ieth', $words[$tens]) . '-' . $words[$units]; // like twenty-first
    }

    return $number . 'th'; // fallback
}


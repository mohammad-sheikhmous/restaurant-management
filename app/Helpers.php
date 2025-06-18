<?php

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
    function dataJson(string $dataKey, mixed $data, string $message = "", int $code = 200, bool $status = true)
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

<?php

if (!function_exists('exceptionJson')) {
    function exceptionJson()
    {
        return response()->json([
            'status' => false,
            'status code' => 500,
            'message' => __('messages.Something Went Wrong...!, Try again Later.')
        ], 500);
    }
}


if (!function_exists('messageJson')) {
    function messageJson(string|array $messageVal, bool $status = true, int $code = 200, string $messageKey = 'message')
    {
        return response()->json([
            'status' => $status,
            'status code' => $code,
            $messageKey => $messageVal,
        ], $code);
    }
}

if (!function_exists('dataJson')) {
    function dataJson(string $dataKey, mixed $data, string $message = "", int $code = 200, bool $status = true)
    {
        return response()->json([
            'status' => $status,
            'status code' => $code,
            'message' => $message,
            $dataKey => $data,
        ], $code);
    }
}

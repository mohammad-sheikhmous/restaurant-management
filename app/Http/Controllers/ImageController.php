<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($name)
    {
        if (Storage::disk('products')->exists($name)) {
            $file = Storage::disk('products')->get($name);
            $type = Storage::disk('products')->mimeType($name);

        } elseif (Storage::disk('categories')->exists($name)) {
            $file = Storage::disk('categories')->get($name);
            $type = Storage::disk('categories')->mimeType($name);

        } elseif (Storage::disk('users')->exists($name)) {
            $file = Storage::disk('users')->get($name);
            $type = Storage::disk('users')->mimeType($name);
        } else
            return messageJson('image not found', false, 404);

        return response($file, 200)->header('Content-Type', $type);
    }
}

<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth('user')->user()->makeHidden(['created_at', 'status']);

        return dataJson('profile_info', $user, 'Profile Information');
    }

    // update data profile
    public function update(UserRequest $request)
    {
        $user = auth('user')->user();

        $data = $request->except('status', 'password', 'email', 'image');

        if ($request->hasFile('image')) {
            if ($user->image)
                $data['image'] = updateImage($request->last_name ?? $user->last_name, $user->image,
                    $request->image, 'users');
            else
                $data['image'] = storeImage($request->last_name ?? $user->last_name, $request->image, 'users');
        }

        $user->update($data);

        return messageJson('Profile information has been modified.');
    }

    public function updateUserImage(Request $request)
    {
        $request->validate(['image' => 'required|image|mimes:jpg,png,jpeg,gif,svg']);

        $user = auth('user')->user();
        if ($request->hasFile('image')) {
            if ($user->image)
                $image = updateImage($request->last_name ?? $user->last_name, $user->image,
                    $request->image, 'users');
            else
                $image = storeImage($request->last_name ?? $user->last_name, $request->image, 'users');

            $user->update(['image' => $image]);
        }

        return messageJson('Profile image updated successfully.');
    }
}

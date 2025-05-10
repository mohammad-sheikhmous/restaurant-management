<?php

namespace App\Http\Controllers\Dashbaoard\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return response()->json(Auth::user());
    }

    // تحديث بيانات البروفايل
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $data = $request->only(['first_name','last_name','location', 'email']);


        if(File::exists($user->image))
        {
            File::delete($user->image);
        }

        $image = $request->file('image');
        $image_name=time() . '.' . $image->getClientOriginalExtension();
        $image->move('ProfileImage/',$image_name);
        $user->image="ProfileImage/".$image_name;
        $user->save();


        $user->update($data);

        return response()->json([
            'message' => 'تم التحديث بنجاح',
            'user' => $user
        ]);
    }
}

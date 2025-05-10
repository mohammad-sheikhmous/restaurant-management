<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = $this->create($request);

        // to send Confirmation OTP to user email
        $user->notify(new OtpNotification());

        $message = 'the user created successfully,check verification code sent you to confirm the email';
        return dataJson('user', $user, $message, 201);
    }

    protected function create(RegisterRequest $request)
    {
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->hasFile('image')) {

            $file_name = storeImage($request->last_name, $request->image, 'users');

            $user->update(['image' => $file_name]);
        }
        return $user;
    }
}

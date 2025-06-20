<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Cart;
use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class RegisterController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('guest:user', only: ['register']),
        ];
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->create($request);

        // to send Confirmation OTP to user email
        $user->notify(new OtpNotification());

        $guest_token = $request->header('guest_token');
        if ($guest_token && $cart = Cart::where('guest_token', $guest_token)->first())
            $cart->update(['user_id' => $user->id]);

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

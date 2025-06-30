<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use App\Models\Wishlist;
use App\Notifications\OtpNotification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:user', only: ['logout']),
            new Middleware('guest:user', only: ['login']),
        ];
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $fields['email'])->first();

        // check from user credentials
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return messageJson('البريد أو كلمة المرور غير صحيحة', false, 401);
        }

        if (!$user->email_verified_at) {
            $user->notify(new OtpNotification());

            $message = 'The user has not confirmed his email yet. A verification code has been sent to confirm the email.';
            return messageJson($message, false, 401);
        }

        $guest_token = $request->header('guest_token');
        if ($guest_token) {
            if ($cart = Cart::where('guest_token', $guest_token)->first())
                $cart->update(['user_id' => $user->id]);

            if ($wishlist = Wishlist::where('guest_token', $guest_token)->first())
                $wishlist->update(['user_id' => $user->id]);
        }

        // generate token after verify from user credentials
        $token = $user->createToken('user-token')->plainTextToken;

        return dataJson('token', $token, 'logged in');
    }

    public function logout()
    {
        auth('user')->user()->tokens()->delete();
        return messageJson('logged out successfully...');
    }
}

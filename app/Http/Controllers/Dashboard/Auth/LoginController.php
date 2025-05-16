<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
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
            new Middleware('auth:admin', only: ['logout']),
            new Middleware('guest:admin', only: ['login']),
        ];
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $admin = Admin::where('email', $fields['email'])->first();

        // check from admin credentials
        if (!$admin || !Hash::check($fields['password'], $admin->password)) {
            return messageJson('البريد أو كلمة المرور غير صحيحة', false, 401);
        }

        // generate token after verify from admin credentials
        $token = $admin->createToken('admin-token')->plainTextToken;

        return dataJson('token', $token,'logged in');
    }

    public function logout()
    {
        auth('admin')->user()->tokens()->delete();
        return messageJson('logged out successfully...');
    }
}

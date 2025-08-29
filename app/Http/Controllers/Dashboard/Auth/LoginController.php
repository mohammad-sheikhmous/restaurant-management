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
            'password' => 'required',
            'role' => 'required|string|in:manager,delivery_driver,admin,chief'
        ]);

        $admin = Admin::where('email', $fields['email'])->first();

        // check from admin credentials
        if (!$admin || !Hash::check($fields['password'], $admin->password)) {
            return messageJson('البريد أو كلمة المرور غير صحيحة', false, 401);
        }

        // Check the role by role parameters
        if (
            $request->role
            !==
            str_replace(' ', '_', trim(strtolower($admin->role->getTranslation('name', 'en'))))
        ) {
            $role = str_replace('_', ' ',ucfirst($request->role));
            return messageJson("Unauthorized for $role App", false, 403);
        }

        // generate token after verify from admin credentials
        $token = $admin->createToken("$request->role-token")->plainTextToken;

        return dataJson('token', $token, 'Logged in');
    }

    public function logout()
    {
        auth('admin')->user()->tokens()->delete();
        return messageJson('Logged out successfully...');
    }
}

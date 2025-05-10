<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        // تحقق من وجود المستخدم
        $user = User::where('email', $fields['email'])->first();

        // تحقق من المستخدم وكلمة المرور
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return messageJson('البريد أو كلمة المرور غير صحيحة', false, 401);
        }

        if (!$user->email_verified_at) {
            $user->notify(new OtpNotification());

            $message = 'The user has not confirmed his email yet. A verification code has been sent to confirm the email.';
            return messageJson($message, false, 401);
        }


        // توليد التوكن بعد التأكد من صحة البيانات
        $token = $user->createToken('myapp-token')->plainTextToken;

        return dataJson('token', $token,'logged in');
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return messageJson('logged out successfully...');
    }
}

<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Notifications\OtpNotification;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    public static function middleware()
    {
        return [
            new Middleware('guest:admin', except: ['reset']),
            new Middleware('auth:admin', only: ['reset']),
        ];
    }

    // To send otp to your email for confirmation it
    public function sendOTP(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $admin = Admin::where('email', $request->email)->first();
        if (!$admin)
            return messageJson('The email is invalid.!', false, 422);

        // Generate a 6-digit code and send it by email
        $admin->notify(new OtpNotification());
        return messageJson('The code sent you successfully.');
    }

    // To verify that the code is correct
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'OTP' => 'required',
            'role' => 'required|string|in:manger,delivery_driver,admin,chief'
        ]);

        $admin = Admin::where('email', $request->email)->first();
        if (!$admin)
            return messageJson('The email is invalid.!', false, 422);

        // Check the role by role parameters
        if (
            $request->role
            !==
            str_replace(' ', '_', trim(strtolower($admin->role->getTranslation('name', 'en'))))
        ) {
            $role = str_replace('_', ' ', ucfirst($request->role));
            return messageJson("Unauthorized for $role App", false, 403);
        }

        // verify that the otp is valid
        $OTP = (new Otp())->validate($admin->email, $request->OTP);
        if ($OTP->status == false)
            return messageJson('Code is invalid..!', false, 401);

        $message = 'The code verified successfully., You are ready to reset password';

        $token = $admin->createToken('admin-token')->plainTextToken;

        return dataJson('token', $token, $message);
    }

    // reset the password after verifying the code
    public function reset(Request $request)
    {
        $request->validate([
            'password' => ['required', 'min:8', 'confirmed', Password::min(8)
                ->max(50)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()],
            'role' => 'required|string|in:manger,delivery_driver,admin,chief'
        ]);

        $admin = auth('admin')->user();

        // Check the role by role parameters
        if (
            $request->role
            !==
            str_replace(' ', '_', trim(strtolower($admin->role->getTranslation('name', 'en'))))
        ) {
            $role = str_replace('_', ' ', ucfirst($request->role));
            return messageJson("Unauthorized for $role App", false, 403);
        }

        $admin->update(['password' => $request->password]);

        if ($request->boolean('logout_oth_dev'))
            $admin->tokens()->where('id', '!=', $admin->currentAccessToken()->id)->delete();

        return messageJson('Your password changed successfully');
    }
}

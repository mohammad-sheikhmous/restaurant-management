<?php

namespace App\Http\Controllers\Dashbaoard\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\OtpNotification;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    // To send otp to your email for confirmation it
    public function sendOTP(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        // Generate a 6-digit code and send it by email
        $user->notify(new OtpNotification());
        return messageJson('The code sent you successfully.');
    }

    // To verify that the code is correct
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email',
            'OTP' => 'required'
        ]);

        $user = User::whereEmail($request->email)->first();

        // verify that the otp is valid
        $OTP = (new Otp())->validate($user->email, $request->OTP);
        if ($OTP->status == false)
            return messageJson('Code is invalid..!', false, 401);

        $message = 'The code verified successfully., You are ready to reset password';

        if ($request->is('api/confirmation/*')) {
            $message = 'The Email Confirmed Successfully.';
            $user->update(['email_verified_at' => now()]);
        }

        $token = $user->createToken('user')->plainTextToken;

        return dataJson('token', $token, $message);
    }
}

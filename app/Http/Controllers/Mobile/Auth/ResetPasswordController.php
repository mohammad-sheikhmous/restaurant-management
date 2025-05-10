<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum'),
        ];
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
        ]);

        $user = auth()->user();
        $user->update(['password' => $request->password]);

        if ($request->boolean('logout_oth_dev'))
            $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return messageJson('Your password changed successfully');
    }
}

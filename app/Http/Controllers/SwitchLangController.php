<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class SwitchLangController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $token = \request()->bearerToken();
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken)
            return response()->json(['message' => 'Unauthenticated.'], 401);

        $new_language = $accessToken->tokenable->language == 'en' ? 'ar' : 'en';

        $accessToken->tokenable->language = $new_language;
        $accessToken->tokenable->save();

        app()->setLocale($new_language);

        return messageJson(__('messages.The App Language switched...'));
    }
}

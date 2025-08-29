<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class SetUserLang
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $locale = auth()->user()->language;
        } else {
            $token = $request->bearerToken();
            $accessToken = PersonalAccessToken::findToken($token);

            if ($accessToken) {
                $locale = $accessToken->tokenable->language;
            } else {

                $allowedLocales = ['en', 'ar'];
                $locale = substr($request->header('Accept-Language'), 0, 2);

                if (!in_array($locale, $allowedLocales)) {
                    $locale = config('app.locale');
                }
            }
        }
        app()->setLocale($locale);

        return $next($request);
    }
}

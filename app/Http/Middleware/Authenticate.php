<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class Authenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if ($guards[0] === 'api') {
            try {
                if (! $user = JWTAuth::parseToken()->authenticate()) {
                    return response()->json(['error' => 'Пользователь не найден'], 404);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'Token недействителен'], 401);
            }
        }

        return $next($request);
    }
}

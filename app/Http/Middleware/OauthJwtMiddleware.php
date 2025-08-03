<?php

namespace App\Http\Middleware;

use App\Helpers\JwtHelper;
use App\Models\User;
use Closure;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OauthJwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = str_replace('Bearer ', '', $request->header('Authorization'));

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            $decoded = JwtHelper::decode($token);
        } catch (ExpiredException $e) {
            return response()->json(['error' => 'Token expired'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // Inject user ke auth()
        $user = User::find($decoded->sub);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 401);
        }

        if (!empty($roles) && !$user->hasAnyRole($roles)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        auth()->login($user);

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\OauthAccessToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class OauthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        $access = OauthAccessToken::where('token', $token)->first();

        if (!$access || $access->expires_at < now()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        auth()->login($access->user);
        return $next($request);
    }
}

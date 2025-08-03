<?php

namespace App\Http\Controllers;

use App\Models\OauthAccessToken;
use App\Models\OauthClient;
use App\Models\OauthRefreshToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OauthController extends Controller
{
    public function token(Request $request)
    {
        $client = OauthClient::where('client_id', $request->client_id)->first();

        if (!$client || $client->client_secret !== hash('sha256', $request->client_secret)) {
            return response()->json(['error' => 'Invalid client credentials'], 401);
        }

        // Validasi user
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid user credentials'], 401);
        }

        // Buat Access Token
        $accessToken = Str::random(60);
        $access = OauthAccessToken::create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'token' => $accessToken,
            'expires_at' => now()->addHour(),
        ]);

        // Buat Refresh Token
        $refreshToken = Str::random(60);
        OauthRefreshToken::create([
            'access_token_id' => $access->id,
            'token' => $refreshToken,
            'expires_at' => now()->addDays(30),
        ]);

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'refresh_token' => $refreshToken
        ]);
    }

    public function refresh(Request $request)
    {
        $refresh = OauthRefreshToken::where('token', $request->refresh_token)->first();

        if (!$refresh || $refresh->expires_at < now()) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }

        // Buat Access Token baru
        $accessToken = Str::random(60);
        $access = OauthAccessToken::create([
            'user_id' => $refresh->accessToken->user_id,
            'client_id' => $refresh->accessToken->client_id,
            'token' => $accessToken,
            'expires_at' => now()->addHour(),
        ]);

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]);
    }
}

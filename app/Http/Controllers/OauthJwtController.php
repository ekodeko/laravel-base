<?php

namespace App\Http\Controllers;

use App\Models\OauthAccessToken;
use App\Models\OauthClient;
use App\Models\OauthRefreshToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Helpers\JwtHelper;
use App\Models\JwtBlacklist;

class OauthJwtController extends Controller
{
    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole('user');
        return response()->json(['message' => 'Registered successfully']);
    }

    public function login(Request $request)
    {
        // $client = OauthClient::where('client_id', $request->client_id)->first();

        // if (!$client || $client->client_secret !== hash('sha256', $request->client_secret)) {
        //     return response()->json(['error' => 'Invalid client credentials'], 401);
        // }

        // Validasi user
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid user credentials'], 401);
        }

        // Buat JWT Access Token
        $payload = [
            'iss' => 'custom-oauth2',          // Issuer
            'sub' => $user->id,                // Subject (User ID)
            // 'client_id' => $client->id,
            'iat' => time(),                   // Issued at
            'exp' => time() + 3600             // Expired (1 jam)
        ];

        $accessToken = JwtHelper::encode($payload);

        // Buat Refresh Token (acak)
        $refreshToken = Str::random(60);
        OauthRefreshToken::create([
            'token' => $refreshToken,
            'access_token_id' => null, // tidak butuh access token id sekarang
            'expires_at' => now()->addDays(30),
        ]);

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'refresh_token' => $refreshToken,
            'permissions' => $user->getAllPermissions()->pluck('name')
        ]);
    }


    public function refresh(Request $request)
    {
        $refresh = OauthRefreshToken::where('token', $request->refresh_token)->first();

        if (!$refresh || $refresh->expires_at < now()) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }

        // Buat JWT baru
        $payload = [
            'iss' => 'custom-oauth2',
            'sub' => auth()->id(),
            'iat' => time(),
            'exp' => time() + 3600
        ];
        $accessToken = JwtHelper::encode($payload);

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600
        ]);
    }

    public function logout(Request $request)
    {
        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        JwtBlacklist::create([
            'token' => $token,
            'blacklisted_at' => now()
        ]);
        return response()->json(['message' => 'Logged out']);
    }
}

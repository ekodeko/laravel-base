<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    public static function encode($payload)
    {
        $key = env('JWT_SECRET', 'secret123');
        return JWT::encode($payload, $key, 'HS256');
    }

    public static function decode($token)
    {
        $key = env('JWT_SECRET', 'secret123');
        return JWT::decode($token, new Key($key, 'HS256'));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthRefreshToken extends Model
{
    protected $fillable = ['access_token_id', 'token', 'expires_at'];
}

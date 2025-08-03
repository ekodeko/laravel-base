<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthAccessToken extends Model
{
    protected $fillable = ['user_id', 'client_id', 'token', 'expires_at'];

    function user() {
        return $this->belongsTo(User::class);
    }
}

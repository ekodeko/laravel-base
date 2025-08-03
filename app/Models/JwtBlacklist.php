<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JwtBlacklist extends Model
{
    protected $fillable = ['token', 'blacklisted_at'];
}

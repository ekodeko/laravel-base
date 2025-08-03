<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthClient extends Model
{
    protected $fillable = ['name', 'client_id', 'client_secret'];
}

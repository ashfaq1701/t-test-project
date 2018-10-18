<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    protected $table = 'refresh_tokens';

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
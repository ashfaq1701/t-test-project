<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photos';

    public function user () {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function profile_photo_of () {
        return $this->hasOne('App\User', 'profile_photo_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'teams';
    protected $fillable = ['name', 'fund', 'country_id', 'user_id'];

    public function players() {
        return $this->hasMany('App\Models\Player', 'team_id', 'id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function country() {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id');
    }

    public function placedTransfers() {
        return $this->hasMany('App\Models\Transfer', 'placed_from_id', 'id');
    }

    public function acceptedTransfers() {
        return $this->hasMany('App\Models\Transfer', 'transferred_to_id', 'id');
    }
}
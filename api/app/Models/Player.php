<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $table = 'players';
    protected $fillable = ['first_name', 'last_name', 'age', 'price', 'country_id', 'team_id', 'player_role_id'];

    public function team() {
        return $this->belongsTo('App\Models\Team', 'team_id', 'id');
    }

    public function playerRole() {
        return $this->belongsTo('App\Models\PlayerRole', 'player_role_id', 'id');
    }

    public function country() {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id');
    }

    public function transfers() {
        return $this->hasMany('App\Models\Transfer', 'player_id', 'id');
    }
}
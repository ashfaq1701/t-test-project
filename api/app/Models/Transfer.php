<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $table = 'transfers';
    protected $fillable = ['asking_price', 'player_id', 'placed_from_id', 'transfer_completed_at',
        'transferred_to_id', 'is_notified'];

    public function placedFrom() {
        return $this->belongsTo('App\Models\Team', 'placed_from_id', 'id');
    }

    public function transferredTo() {
        return $this->belongsTo('App\Models\Team', 'transferred_to_id', 'id');
    }

    public function player() {
        return $this->belongsTo('App\Models\Player', 'player_id', 'id');
    }
}
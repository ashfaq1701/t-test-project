<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Country extends Model
{
    protected $table = 'countries';
    protected $fillable = ['name', 'alpha_2'];

    public function teams() {
        return $this->hasMany('App\Models\Team', 'country_id', 'id');
    }

    public function players() {
        return $this->hasMany('App\Models\Player', 'country_id', 'id');
    }
}
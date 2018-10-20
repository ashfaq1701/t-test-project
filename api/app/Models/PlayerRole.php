<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PlayerRole extends Model
{
    protected $table = 'player_roles';
    protected $fillable = ['name'];

    public function players() {
        return $this->hasMany('App\Models\Player', 'player_role_id', 'id');
    }

    public static function getIds() {
        return self::query()->where('id', '>', 0)->pluck('id')->toArray();
    }
}
<?php

use Illuminate\Database\Seeder;
use App\Models\PlayerRole;

class PlayerRoleSeeder extends Seeder
{
    public function run()
    {
        PlayerRole::create(['name' => 'Goalkeeper']);
        PlayerRole::create(['name' => 'Defender']);
        PlayerRole::create(['name' => 'Midfielder']);
        PlayerRole::create(['name' => 'Attacker']);
    }
}
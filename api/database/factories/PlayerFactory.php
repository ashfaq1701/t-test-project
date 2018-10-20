<?php

use Faker\Generator as Faker;
use App\Models\Country;

$factory->define(App\Models\Player::class, function (Faker $faker) {
    $countries = Country::getIds();

    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'age' => $faker->numberBetween(18, 40),
        'price' => 1000000,
        'country_id' => $faker->randomElement($countries),
        'team_id' => 1,
        'player_role_id' => 1
    ];
});
<?php

use Faker\Generator as Faker;
use App\Models\Country;

$factory->define(App\Models\Team::class, function (Faker $faker) {
    $countries = Country::getIds();

    return [
        'name' => $faker->company,
        'fund' => 5000000,
        'country_id' => $faker->randomElement($countries),
        'user_id' => 1
    ];
});
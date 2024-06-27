<?php

use Wlhrtr\StateMachine\Tests\TestModels\SalesManager;
use Faker\Generator as Faker;

$factory->define(SalesManager::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});

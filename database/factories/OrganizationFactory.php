<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\App\Models\Db\Organization::class, function (Faker $faker) {
    return [
        'organization_name' => $faker->company,
        'owner_id'          => rand(1, 21),
        'registration_date' => $faker->date(),
    ];
});

$factory->define(\App\Models\Db\UserOrganization::class, function () {
    return [
        'organization_id'   => rand(1, 100),
        'user_id'           => rand(21, 222),
    ];
});
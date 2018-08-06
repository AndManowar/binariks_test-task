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

$factory->define(\App\Models\Db\Task::class, function (Faker $faker) {
    return [
        'author_id'       => rand(1, 21),
        'performer_id'    => rand(22, 222),
        'organization_id' => rand(1, 100),
        'name'            => $faker->words(3, true),
        'status'          => rand(1, 5),
        'deadline'        => $faker->date(),
    ];
});

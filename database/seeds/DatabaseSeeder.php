<?php

use App\Models\Db\Organization;
use App\Models\Db\Task;
use App\Models\Db\User;
use App\Models\Db\UserOrganization;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo 'Создаем юзеров'.PHP_EOL;
        // Тестовый овнер
        User::create([
            'name'     => 'owner_name',
            'surname'  => 'owner_surname',
            'email'    => 'owner@owner.owner',
            'password' => bcrypt('123456'),
            'role'     => User::ROLE_OWNER,
        ]);

        // Создадим 20 Руководителей
        factory(User::class, 20)->create([
            'role' => User::ROLE_OWNER,
        ]);

        // Тестовый перформер
        User::create([
            'name'     => 'performer_name',
            'surname'  => 'performer_surname',
            'email'    => 'performer@performer.performer',
            'password' => bcrypt('123456'),
            'role'     => User::ROLE_PERFORMER,
        ]);

        // Создадим 200 исполнителей
        factory(User::class, 200)->create([
            'role' => User::ROLE_PERFORMER,
        ]);

        echo 'Создаем организации'.PHP_EOL;
        factory(Organization::class, 100)->create();
        echo 'Приглашаем юзеров в организации'.PHP_EOL;
        factory(UserOrganization::class, 200)->create();
        echo 'Создаем здачи'.PHP_EOL;
        factory(Task::class, 500)->create();
        echo 'Закончили'.PHP_EOL;

    }
}

<?php

namespace Tests\Unit\Auth;

use App\Events\OnUserRegister;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class AuthenticationTest
 * @package Tests\Unit\Auth
 */
class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Generator
     */
    private $faker;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    /**
     * Попытка логина с невалидными данными
     *
     * @return void
     */
    public function testLoginWithInvalidCredentials(): void
    {
        $this->json('POST', 'api/auth/login', [
            'email'    => 'owner@owner.owner',
            'password' => '1233456',
        ])->assertStatus(202);
    }

    /**
     * Попытка логина с валидными данными
     *
     * @return void
     */
    public function testLoginWithValidCredentials(): void
    {
        $this->json('POST', 'api/auth/login', [
            'email'    => 'owner@owner.owner',
            'password' => '123456',
        ])->assertStatus(200);
    }


    /**
     * Проверка регистрации валидного юзера
     *
     * @return void
     */
    public function testUserRegistrationWithValidData(): void
    {
        $this->json('POST', 'api/auth/register', [
            'email'            => $this->faker->email,
            'password'         => '123456',
            'password_confirm' => '123456',
            'name'             => $this->faker->lastName,
            'surname'          => $this->faker->firstName,
            'role'             => rand(1, 2),
        ])->assertStatus(200);
    }

    /**
     * Проверка регистрации юзера с невалидными данными (ошибки валидации)
     *
     * @return void
     */
    public function testUserRegistrationWithInvalidData(): void
    {
        $this->json('POST', 'api/auth/register', [
            'email'    => $this->faker->email,
            'password' => '123456',
            'name'     => $this->faker->lastName,
            'surname'  => $this->faker->firstName,
            'role'     => rand(1, 15),
        ])->assertStatus(422);
    }

    /**
     * Проверка срабатывания ивента при регистрации
     *
     * @return void
     * @throws \Exception
     */
    public function testRegistrationEventCall(): void
    {
        $this->expectsEvents(OnUserRegister::class);

        $this->json('POST', 'api/auth/register', [
            'email'            => $this->faker->email,
            'password'         => '123456',
            'password_confirm' => '123456',
            'name'             => $this->faker->lastName,
            'surname'          => $this->faker->firstName,
            'role'             => rand(1, 2),
        ]);
    }
}

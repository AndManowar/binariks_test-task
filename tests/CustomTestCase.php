<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 06.08.18
 * Time: 13:34
 */

namespace Tests;

use JWTAuth;
use Illuminate\Foundation\Testing\TestResponse;

/**
 * Кастомный АПИ метод для авторизированных запросов
 *
 * Class CustomTestCase
 * @package Tests
 */
class CustomTestCase extends TestCase
{
    /**
     * @param $user
     * @param string $method
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return TestResponse
     */
    protected function apiAs($user, string $method, string $uri, array $data = [], array $headers = []): TestResponse
    {
        $headers = array_merge([
            'Authorization' => 'Bearer ' . JWTAuth::fromUser($user),
        ], $headers);

        return $this->json($method, $uri, $data, $headers);
    }
}
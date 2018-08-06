<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 04.08.2018
 * Time: 17:58
 */

namespace App\Contracts;

use App\Models\Db\User;

/**
 * Interface AuthenticationServiceInterface
 * @package App\Contracts
 */
interface AuthenticationServiceInterface
{
    /**
     * Регистрация
     *
     * @param array $data
     * @return bool
     */
    public function register(array $data): bool;

    /**
     * Метод логина
     *
     * @param array $credentials
     * @return false|string
     */
    public function login(array $credentials);

    /**
     * Получаем юзера
     *
     * @return User
     */
    public function getUser(): User;
}
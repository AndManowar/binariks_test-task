<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 03.08.2018
 * Time: 22:09
 */

namespace App\Services;

use App\Contracts\AuthenticationServiceInterface;
use App\Events\OnUserRegister;
use App\Models\Db\User;
use Illuminate\Support\Facades\Event;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Сервис аутентификации
 *
 * Class AuthService
 * @package App\Services
 */
class AuthService implements AuthenticationServiceInterface
{
    /**
     * @var User
     */
    protected $user;

    /**
     * Регистрация
     *
     * @param array $data
     * @return bool
     */
    public function register(array $data): bool
    {
        $this->user = new User($data);
        $this->user->password = bcrypt($this->user->password);

        if (!$this->user->save()) {
            return false;
        }

        Event::fire(new OnUserRegister($this->user));

        return true;
    }


    /**
     * Метод логина
     *
     * @param array $credentials
     * @return false|string
     */
    public function login(array $credentials)
    {
        return JWTAuth::attempt($credentials);
    }

    /**
     * Получаем юзера
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
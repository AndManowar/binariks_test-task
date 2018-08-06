<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 04.08.2018
 * Time: 17:57
 */

namespace App\Providers;

use App\Contracts\AuthenticationServiceInterface;
use App\Services\AuthService;
use Illuminate\Support\ServiceProvider;

/**
 * Class AuthenticationServiceProvider
 * @package App\Providers
 */
class AuthenticationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AuthenticationServiceInterface::class, function () {
            return new AuthService();
        });
    }
}
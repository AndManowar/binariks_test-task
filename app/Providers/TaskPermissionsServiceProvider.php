<?php

namespace App\Providers;

use App\Services\Task\CheckTaskPermissionsService;
use Illuminate\Support\ServiceProvider;
use App\Contracts\CheckTaskPermissionInterface;

/**
 * Class TaskPermissionsServiceProvider
 * @package App\Providers
 */
class TaskPermissionsServiceProvider extends ServiceProvider
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
        $this->app->singleton(CheckTaskPermissionInterface::class, function () {
            return new CheckTaskPermissionsService();
        });
    }
}

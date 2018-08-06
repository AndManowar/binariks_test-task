<?php

namespace App\Providers;

use App\Services\Organization\CheckOrganizationPermissionsService;
use Illuminate\Support\ServiceProvider;
use App\Contracts\CheckOrganizationPermissionsInterface;

/**
 * Class OrganizationPermissionsServiceProvider
 * @package App\Providers
 */
class OrganizationPermissionsServiceProvider extends ServiceProvider
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
        $this->app->singleton(CheckOrganizationPermissionsInterface::class, function () {
            return new CheckOrganizationPermissionsService();
        });
    }
}

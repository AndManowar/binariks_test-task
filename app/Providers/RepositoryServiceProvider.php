<?php

namespace App\Providers;

use App\Api\V1\Http\Controllers\Organization\OrganizationController;
use App\Api\V1\Http\Controllers\Task\TaskController;
use App\Contracts\RepositoryInterface;
use App\Repositories\Organization\OrganizationRepository;
use App\Repositories\Task\TaskRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Class RepositoryServiceProvider
 * @package App\Providers
 */
class RepositoryServiceProvider extends ServiceProvider
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
        $this->app->when(OrganizationController::class)
            ->needs(RepositoryInterface::class)
            ->give(function () {
                return new OrganizationRepository();
            });

        $this->app->when(TaskController::class)
            ->needs(RepositoryInterface::class)
            ->give(function () {
                return new TaskRepository();
            });
    }
}

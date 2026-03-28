<?php

namespace ECommerce\User\Providers;

use ECommerce\User\Services\AuthService;
use ECommerce\User\Services\UserService;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/user.php', 'user');

        $this->app->bind(AuthService::class);
        $this->app->bind(UserService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');

        $this->publishes([
            __DIR__ . '/../../config/user.php' => config_path('user.php'),
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ], 'ecommerce-user');
    }
}

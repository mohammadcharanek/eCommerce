<?php

namespace App\Modules\User\Providers;

use App\Modules\User\Services\AuthService;
use App\Modules\User\Services\UserService;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthService::class);
        $this->app->bind(UserService::class);
    }
}

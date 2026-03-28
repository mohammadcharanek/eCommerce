<?php

namespace App\Providers;

use App\Modules\User\Models\User;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Use our modular User model as auth user
    }
}

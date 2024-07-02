<?php

namespace App\Providers;

use App\Http\Interfaces\UpcomingEventRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Http\Interfaces\UserRepositoryInterface;
use App\Http\Repositories\UserRepository;
use App\Http\Repositories\UpcomingEventRepository; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UpcomingEventRepositoryInterface::class, UpcomingEventRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
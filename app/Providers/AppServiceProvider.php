<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Interfaces\UserRepositoryInterface;
use App\Http\Interfaces\UpcomingEventRepositoryInterface;
use App\Http\Interfaces\AnnouncementRepositoryInterface;
use App\Http\Repositories\UserRepository;
use App\Http\Repositories\UpcomingEventRepository; 
use App\Http\Repositories\AnnouncementRepository;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UpcomingEventRepositoryInterface::class, UpcomingEventRepository::class);
        $this->app->bind(AnnouncementRepositoryInterface::class, AnnouncementRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
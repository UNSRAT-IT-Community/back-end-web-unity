<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Interfaces\UpcomingEventRepositoryInterface;
use App\Http\Interfaces\UserRepositoryInterface;
use App\Http\Interfaces\GalleryRepositoryInterface;
use App\Http\Repositories\UserRepository;
use App\Http\Repositories\UpcomingEventRepository;
use App\Http\Repositories\GalleryRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UpcomingEventRepositoryInterface::class, UpcomingEventRepository::class);
        $this->app->bind(GalleryRepositoryInterface::class, GalleryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

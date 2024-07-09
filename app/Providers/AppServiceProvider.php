<?php

namespace App\Providers;

use App\Http\Interfaces\UpcomingEventRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Http\Interfaces\UserRepositoryInterface;
use App\Http\Repositories\UserRepository;
use App\Http\Repositories\UpcomingEventRepository;
use App\Http\Interfaces\CommunityAdsInterface;
use App\Http\Repositories\CommunityAdsRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UpcomingEventRepositoryInterface::class, UpcomingEventRepository::class);
        $this->app->bind(CommunityAdsInterface::class, CommunityAdsRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

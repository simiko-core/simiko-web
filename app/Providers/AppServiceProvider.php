<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Post;
use App\Models\UnitKegiatan;
use App\Models\UnitKegiatanProfile;
use App\Observers\EventObserver;
use App\Observers\PostObserver;
use App\Observers\UnitKegiatanProfileObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Post::observe(PostObserver::class);
        Event::observe(EventObserver::class);
        UnitKegiatanProfile::observe(UnitKegiatanProfileObserver::class);
    }
}

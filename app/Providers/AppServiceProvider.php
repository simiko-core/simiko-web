<?php

namespace App\Providers;

use App\Models\UnitKegiatan;
use App\Models\UnitKegiatanProfile;
use App\Models\Achievement;
use App\Models\ActivityGallery;
use App\Models\Feed;
use App\Models\Admin;
use App\Observers\UnitKegiatanProfileObserver;
use App\Observers\AchievementObserver;
use App\Observers\ActivityGalleryObserver;
use App\Observers\FeedObserver;
use App\Observers\AdminObserver;
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
        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
        
        Model::unguard();
        UnitKegiatanProfile::observe(UnitKegiatanProfileObserver::class);
        Achievement::observe(AchievementObserver::class);
        ActivityGallery::observe(ActivityGalleryObserver::class);
        Feed::observe(FeedObserver::class);
        Admin::observe(AdminObserver::class);
    }
}

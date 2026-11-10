<?php

namespace App\Providers;

use App\Models\Destination;
use App\Models\TourType;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Enquiry forms: per-IP limits (burst + hourly).
        RateLimiter::for('enquiries', fn (Request $r) => [
            Limit::perMinute(5)->by($r->ip()),
            Limit::perHour(20)->by($r->ip()),
        ]);

        // Admin login: per email+IP.
        RateLimiter::for('admin-login', fn (Request $r) => Limit::perMinute(5)->by(strtolower((string) $r->input('email')).'|'.$r->ip()));

        // Navigation data for the public layout (one query each, only on rendered pages).
        View::composer(['layouts.site', 'partials.*'], function ($view) {
            static $nav = null;
            $nav ??= [
                'navTourTypes' => TourType::active()->get(['id', 'name', 'slug']),
                'navDestinations' => Destination::active()->get(['id', 'name', 'slug']),
            ];
            $view->with($nav);
        });
    }
}

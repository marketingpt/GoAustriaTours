<?php

use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\CaptureTracking;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['admin' => AdminAuthenticate::class]);
        $middleware->web(append: [SecurityHeaders::class, CaptureTracking::class]);
        $middleware->trustProxies(at: '*');
    })
    ->withSchedule(function (Schedule $schedule) {
        $enabled = filter_var(env('PMT_SYNC_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
        if ($enabled) {
            $minutes = max(1, (int) env('PMT_SYNC_MINUTES', 10));
            $companyId = (int) env('PMT_COMPANY_ID', 3);
            $schedule->command('pmt:sync-packages --company-id='.$companyId)->everyMinutes($minutes);
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

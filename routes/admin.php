<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\TourTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    // Auth (login is rate limited: 5/min per email+IP)
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:admin-login')->name('login.submit');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        // Enquiries
        Route::get('enquiries/export', [EnquiryController::class, 'export'])->name('enquiries.export');
        Route::get('enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('enquiries/{enquiry}', [EnquiryController::class, 'show'])->name('enquiries.show');
        Route::put('enquiries/{enquiry}', [EnquiryController::class, 'update'])->name('enquiries.update');
        Route::delete('enquiries/{enquiry}', [EnquiryController::class, 'destroy'])->name('enquiries.destroy');
        Route::post('enquiries/{enquiry}/retry-ghl', [EnquiryController::class, 'retryGhl'])->name('enquiries.retry-ghl');

        // Simple CRUD lists (index/create/store/edit/update/destroy + move up/down)
        $crud = function (string $uri, string $controller, bool $sortable = true) {
            Route::resource($uri, $controller)->except('show')->parameters([$uri => 'id']);
            if ($sortable) {
                Route::post("{$uri}/{id}/move", [$controller, 'move'])->name("{$uri}.move");
            }
        };
        $crud('packages', PackageController::class);
        Route::post('packages/{id}/duplicate', [PackageController::class, 'duplicate'])->name('packages.duplicate');
        Route::post('packages/{id}/publish', [PackageController::class, 'publish'])->name('packages.publish');
        Route::post('packages/{id}/feature', [PackageController::class, 'feature'])->name('packages.feature');
        $crud('tour-types', TourTypeController::class);
        $crud('destinations', DestinationController::class);
        $crud('quiz', QuizController::class);
        $crud('testimonials', TestimonialController::class);
        $crud('faqs', FaqController::class);
        $crud('users', AdminUserController::class, false);

        // Pages
        Route::get('pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('pages/{slug}', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('pages/{slug}', [PageController::class, 'update'])->name('pages.update');

        // Settings
        Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

        // Media
        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::put('media/{media}', [MediaController::class, 'update'])->name('media.update');
        Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
    });
});

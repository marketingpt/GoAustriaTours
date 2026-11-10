<?php

use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

/* ---------- Public site ---------- */
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/packages', [SiteController::class, 'packages'])->name('packages.index');
Route::get('/packages/{slug}', [SiteController::class, 'package'])->name('packages.show');
Route::get('/destinations', [SiteController::class, 'destinations'])->name('destinations.index');
Route::get('/destinations/{slug}', [SiteController::class, 'destination'])->name('destinations.show');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::get('/terms-and-conditions', fn () => app(SiteController::class)->legal('terms'))->name('terms');
Route::get('/privacy-policy', fn () => app(SiteController::class)->legal('privacy'))->name('privacy');
Route::get('/thank-you', [SiteController::class, 'thankYou'])->name('thank-you');

/* One endpoint for every enquiry form */
Route::post('/enquiry', [EnquiryController::class, 'store'])->middleware('throttle:enquiries')->name('enquiry.store');

/* SEO */
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

/* Admin (routes/admin.php) */
require __DIR__.'/admin.php';

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailCampaignController;
use App\Http\Controllers\EmailRecipientController;
use App\Http\Controllers\SingleEmailController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\EmailTrackingController;
use App\Http\Controllers\EmailAnalyticsController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Kampanyalar
Route::resource('campaigns', EmailCampaignController::class);
Route::post('campaigns/{campaign}/start', [EmailCampaignController::class, 'start'])->name('campaigns.start');
Route::post('campaigns/{campaign}/pause', [EmailCampaignController::class, 'pause'])->name('campaigns.pause');
Route::get('campaigns/{campaign}/analytics', [EmailAnalyticsController::class, 'show'])->name('campaigns.analytics');

// Alıcılar
Route::post('campaigns/{campaign}/recipients/import', [EmailRecipientController::class, 'import'])->name('recipients.import');
Route::post('campaigns/{campaign}/recipients', [EmailRecipientController::class, 'store'])->name('recipients.store');

// Tek email gönderimi
Route::get('emails/send', [SingleEmailController::class, 'create'])->name('emails.single');
Route::post('emails/send', [SingleEmailController::class, 'send'])->name('emails.send');

// Email Şablonları
Route::resource('templates', EmailTemplateController::class);

// Email Tracking
Route::get('tracking/pixel/{hash}', [EmailTrackingController::class, 'pixel'])->name('tracking.pixel');
Route::get('tracking/click/{hash}', [EmailTrackingController::class, 'click'])->name('tracking.click');



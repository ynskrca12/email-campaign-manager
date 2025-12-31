<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailCampaignController;
use App\Http\Controllers\EmailRecipientController;
use App\Http\Controllers\SingleEmailController;

Route::get('/', function () {
    return redirect()->route('campaigns.index');
});

// Kampanyalar
Route::resource('campaigns', EmailCampaignController::class);
Route::post('campaigns/{campaign}/start', [EmailCampaignController::class, 'start'])->name('campaigns.start');
Route::post('campaigns/{campaign}/pause', [EmailCampaignController::class, 'pause'])->name('campaigns.pause');

// Alıcılar
Route::post('campaigns/{campaign}/recipients/import', [EmailRecipientController::class, 'import'])->name('recipients.import');
Route::post('campaigns/{campaign}/recipients', [EmailRecipientController::class, 'store'])->name('recipients.store');

// Tek email gönderimi
Route::get('emails/send', [SingleEmailController::class, 'create'])->name('emails.single');
Route::post('emails/send', [SingleEmailController::class, 'send'])->name('emails.send');

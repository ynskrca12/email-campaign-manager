<?php

namespace App\Jobs;

use App\Models\EmailRecipient;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 3;

    public function __construct(
        private EmailRecipient $recipient
    ) {
        // Delay'i burada değil, dispatch ederken vereceğiz
    }

    public function handle(EmailService $emailService): void
    {
        Log::info("Email gönderiliyor: {$this->recipient->email}");

        $emailService->sendCampaignEmail($this->recipient);

        // Kampanya tamamlanmış mı kontrol et
        $campaign = $this->recipient->campaign;
        $campaign->refresh();

        if ($campaign->sent_count + $campaign->failed_count >= $campaign->total_recipients) {
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            Log::info("Kampanya tamamlandı: {$campaign->name}");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Email gönderimi başarısız: {$this->recipient->email}", [
            'error' => $exception->getMessage()
        ]);

        $this->recipient->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);

        $this->recipient->campaign->increment('failed_count');
    }
}

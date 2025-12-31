<?php

namespace App\Jobs;

use App\Models\EmailRecipient;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 3;

    private int $delaySeconds; // İsim değişti: $delay -> $delaySeconds

    public function __construct(
        private EmailRecipient $recipient,
        int $delay = 1
    ) {
        $this->delaySeconds = $delay; // İsim değişti

        // Gecikme ile queue'ya ekle
        $this->delay($delay);
    }

    public function handle(EmailService $emailService): void
    {
        $emailService->sendCampaignEmail($this->recipient);
    }

    public function failed(\Throwable $exception): void
    {
        $this->recipient->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);

        $this->recipient->campaign->increment('failed_count');
    }
}

<?php

namespace App\Jobs;

use App\Models\EmailCampaign;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessEmailCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 saat
    public $tries = 3;

    public function __construct(
        private EmailCampaign $campaign
    ) {}

    public function handle(EmailService $emailService): void
    {
        Log::info("Kampanya başlatıldı: {$this->campaign->name}");

        $recipients = $this->campaign->pendingRecipients()->get();

        foreach ($recipients as $recipient) {
            // Kampanya durdurulmuş mu kontrol et
            $this->campaign->refresh();
            if ($this->campaign->status !== 'processing') {
                Log::info("Kampanya durduruldu: {$this->campaign->name}");
                break;
            }

            SendCampaignEmail::dispatch($recipient, $this->campaign->delay_between_emails);
        }

        // Tüm emailler gönderildi mi kontrol et
        $this->campaign->refresh();
        if ($this->campaign->sent_count + $this->campaign->failed_count >= $this->campaign->total_recipients) {
            $this->campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            Log::info("Kampanya tamamlandı: {$this->campaign->name}");
        }
    }
}

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

        if ($recipients->isEmpty()) {
            Log::info("Gönderilecek alıcı yok: {$this->campaign->name}");

            // Kampanyayı tamamla
            $this->campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return;
        }

        $delaySeconds = 0; // İlk email hemen gönderilir

        foreach ($recipients as $index => $recipient) {
            // Kampanya durdurulmuş mu kontrol et
            $this->campaign->refresh();
            if ($this->campaign->status !== 'processing') {
                Log::info("Kampanya durduruldu: {$this->campaign->name}");
                break;
            }

            // Her email için artan gecikme ile kuyruğa ekle
            SendCampaignEmail::dispatch($recipient)
                ->delay(now()->addSeconds($delaySeconds));

            Log::info("Email kuyruğa eklendi", [
                'campaign_id' => $this->campaign->id,
                'recipient' => $recipient->email,
                'index' => $index + 1,
                'total' => $recipients->count(),
                'delay_seconds' => $delaySeconds,
                'scheduled_time' => now()->addSeconds($delaySeconds)->format('Y-m-d H:i:s')
            ]);

            // Bir sonraki email için gecikmeyi artır
            $delaySeconds += $this->campaign->delay_between_emails;
        }

        Log::info("Kampanya işleme alındı", [
            'campaign' => $this->campaign->name,
            'total_emails' => $recipients->count(),
            'total_duration' => gmdate('H:i:s', $delaySeconds),
            'will_complete_at' => now()->addSeconds($delaySeconds)->format('Y-m-d H:i:s')
        ]);

        // Not: Status'u burada completed yapma!
        // Her email gönderildikçe SendCampaignEmail job'ı kontrol edecek
    }
}

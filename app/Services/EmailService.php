<?php

namespace App\Services;

use App\Models\EmailRecipient;
use App\Models\EmailLog;
use App\Mail\CampaignEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    public function __construct(
        private EmailTrackingService $trackingService
    ) {}

    public function sendEmail(string $to, string $toName, string $subject, string $body, string $fromEmail, string $fromName): bool
    {
        try {
            Mail::to($to)
                ->send(new CampaignEmail($subject, $body, $fromEmail, $fromName, $toName));

            EmailLog::create([
                'to_email' => $to,
                'to_name' => $toName,
                'subject' => $subject,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Email gönderim hatası: ' . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject,
            ]);

            EmailLog::create([
                'to_email' => $to,
                'to_name' => $toName,
                'subject' => $subject,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'sent_at' => now(),
            ]);

            return false;
        }
    }

    public function sendCampaignEmail(EmailRecipient $recipient): bool
{
    $campaign = $recipient->campaign;

    // Değişkenleri değiştir ({{name}}, {{email}} gibi)
    $body = $this->replaceVariables($campaign->body, $recipient);

    // Link tracking KAPALI (geçici)
    // $bodyWithTracking = $this->trackingService->replaceLinksWithTracking($body, $campaign);

    // Tracking pixel URL oluştur
    $trackingPixelUrl = $this->trackingService->generateTrackingPixel($campaign, $recipient);

    try {
        Mail::to($recipient->email)
            ->send(new CampaignEmail(
                $campaign->subject,
                $body, // bodyWithTracking yerine body
                $campaign->from_email,
                $campaign->from_name,
                $recipient->name,
                $trackingPixelUrl,
                $recipient->id
            ));

        $recipient->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $campaign->increment('sent_count');

        EmailLog::create([
            'campaign_id' => $campaign->id,
            'recipient_id' => $recipient->id,
            'to_email' => $recipient->email,
            'to_name' => $recipient->name,
            'subject' => $campaign->subject,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return true;
    } catch (\Exception $e) {
        \Log::error('Kampanya email hatası: ' . $e->getMessage(), [
            'campaign_id' => $campaign->id,
            'recipient_id' => $recipient->id,
        ]);

        $recipient->update([
            'status' => 'failed',
            'error_message' => $e->getMessage(),
        ]);

        $campaign->increment('failed_count');

        EmailLog::create([
            'campaign_id' => $campaign->id,
            'recipient_id' => $recipient->id,
            'to_email' => $recipient->email,
            'to_name' => $recipient->name,
            'subject' => $campaign->subject,
            'status' => 'failed',
            'error_message' => $e->getMessage(),
            'sent_at' => now(),
        ]);

        return false;
    }
}

    private function replaceVariables(string $content, EmailRecipient $recipient): string
    {
        $variables = [
            '{{name}}' => $recipient->name ?? '',
            '{{email}}' => $recipient->email,
        ];

        // Custom fields ekle
        if ($recipient->custom_fields) {
            foreach ($recipient->custom_fields as $key => $value) {
                $variables["{{" . $key . "}}"] = $value;
            }
        }

        return str_replace(array_keys($variables), array_values($variables), $content);
    }
}

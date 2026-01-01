<?php

namespace App\Services;

use App\Models\EmailCampaign;
use App\Models\EmailRecipient;
use App\Models\EmailOpen;
use App\Models\EmailClick;
use App\Models\TrackingLink;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class EmailTrackingService
{
    /**
     * Tracking pixel URL oluştur
     */
    public function generateTrackingPixel(EmailCampaign $campaign, EmailRecipient $recipient): string
    {
        $hash = $this->generateHash($campaign->id, $recipient->id);
        return route('tracking.pixel', ['hash' => $hash]);
    }

    /**
     * Link'leri tracking URL'leri ile değiştir
     */
    public function replaceLinksWithTracking(string $content, EmailCampaign $campaign): string
    {
        // HTML içindeki tüm link'leri bul
        $pattern = '/<a\s+(?:[^>]*?\s+)?href="([^"]*)"/i';

        return preg_replace_callback($pattern, function ($matches) use ($campaign) {
            $originalUrl = $matches[1];

            // Zaten tracking URL'i ise dokunma
            if (str_contains($originalUrl, route('tracking.click', '', false))) {
                return $matches[0];
            }

            // Tracking link oluştur veya varsa al
            $trackingLink = TrackingLink::firstOrCreate(
                [
                    'campaign_id' => $campaign->id,
                    'original_url' => $originalUrl,
                ],
                [
                    'tracking_hash' => Str::random(32),
                ]
            );

            $trackingUrl = route('tracking.click', ['hash' => $trackingLink->tracking_hash]);

            return str_replace($originalUrl, $trackingUrl, $matches[0]);
        }, $content);
    }

    /**
     * Email açılma kaydı
     */
    public function trackOpen(string $hash, $request): void
    {
        [$campaignId, $recipientId] = $this->decodeHash($hash);

        $campaign = EmailCampaign::find($campaignId);
        $recipient = EmailRecipient::find($recipientId);

        if (!$campaign || !$recipient) {
            return;
        }

        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        EmailOpen::create([
            'campaign_id' => $campaign->id,
            'recipient_id' => $recipient->id,
            'email' => $recipient->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
            'browser' => $agent->browser(),
            'os' => $agent->platform(),
            'opened_at' => now(),
        ]);
    }

    /**
     * Link tıklama kaydı
     */
    public function trackClick(string $hash, $request): ?string
    {
        $trackingLink = TrackingLink::where('tracking_hash', $hash)->first();

        if (!$trackingLink) {
            return null;
        }

        $trackingLink->increment('click_count');

        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        // Kampanya ve alıcı bilgisini query string'den al (email template'de ekleyeceğiz)
        $recipientId = $request->get('r');
        $recipient = $recipientId ? EmailRecipient::find($recipientId) : null;

        EmailClick::create([
            'campaign_id' => $trackingLink->campaign_id,
            'recipient_id' => $recipient?->id,
            'email' => $recipient?->email ?? 'unknown',
            'original_url' => $trackingLink->original_url,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
            'browser' => $agent->browser(),
            'os' => $agent->platform(),
            'clicked_at' => now(),
        ]);

        return $trackingLink->original_url;
    }

    /**
     * Hash oluştur
     */
    private function generateHash(int $campaignId, int $recipientId): string
    {
        return base64_encode("{$campaignId}:{$recipientId}");
    }

    /**
     * Hash'i çöz
     */
    private function decodeHash(string $hash): array
    {
        $decoded = base64_decode($hash);
        return explode(':', $decoded);
    }
}

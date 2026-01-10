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
        if (str_contains($originalUrl, 'tracking/click') || str_contains($originalUrl, 'tracking/pixel')) {
            return $matches[0];
        }

        // Anchor link (#) veya mailto: ise dokunma
        if (str_starts_with($originalUrl, '#') || str_starts_with($originalUrl, 'mailto:')) {
            return $matches[0];
        }

        // Boş URL kontrolü
        if (empty($originalUrl) || $originalUrl === '' || $originalUrl === 'http://' || $originalUrl === 'https://') {
            return $matches[0];
        }

        try {
            // Tracking link oluştur veya varsa al
            $trackingLink = TrackingLink::firstOrCreate(
                [
                    'campaign_id' => $campaign->id,
                    'original_url' => $originalUrl,
                ],
                [
                    'tracking_hash' => \Illuminate\Support\Str::random(32),
                ]
            );

            // Hash kontrolü
            if (empty($trackingLink->tracking_hash)) {
                \Log::warning('Tracking hash boş!', [
                    'tracking_link_id' => $trackingLink->id,
                    'url' => $originalUrl
                ]);
                return $matches[0]; // Hata varsa orijinal URL'i döndür
            }

            $trackingUrl = route('tracking.click', ['hash' => $trackingLink->tracking_hash]);

            return str_replace($originalUrl, $trackingUrl, $matches[0]);

        } catch (\Exception $e) {
            \Log::error('Link tracking hatası: ' . $e->getMessage(), [
                'url' => $originalUrl,
                'campaign_id' => $campaign->id
            ]);
            return $matches[0]; // Hata varsa orijinal linki döndür
        }

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

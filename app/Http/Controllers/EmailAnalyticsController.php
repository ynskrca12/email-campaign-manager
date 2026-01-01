<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaign;
use App\Models\EmailOpen;
use App\Models\EmailClick;
use Illuminate\Support\Facades\DB;

class EmailAnalyticsController extends Controller
{
    public function show(EmailCampaign $campaign)
    {
        // Genel istatistikler
        $stats = [
            'sent' => $campaign->sent_count,
            'unique_opens' => $campaign->opens()->distinct('email')->count('email'),
            'total_opens' => $campaign->opens()->count(),
            'unique_clicks' => $campaign->clicks()->distinct('email')->count('email'),
            'total_clicks' => $campaign->clicks()->count(),
            'open_rate' => $campaign->open_rate,
            'click_rate' => $campaign->click_rate,
        ];

        // Cihaz dağılımı
        $deviceStats = $campaign->opens()
            ->select('device', DB::raw('count(*) as count'))
            ->groupBy('device')
            ->get();

        // Tarayıcı dağılımı
        $browserStats = $campaign->opens()
            ->select('browser', DB::raw('count(*) as count'))
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // İşletim sistemi dağılımı
        $osStats = $campaign->opens()
            ->select('os', DB::raw('count(*) as count'))
            ->groupBy('os')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Saatlik açılma grafiği
        $hourlyOpens = $campaign->opens()
            ->select(DB::raw('HOUR(opened_at) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // En çok tıklanan linkler
        $topLinks = $campaign->trackingLinks()
            ->orderByDesc('click_count')
            ->limit(10)
            ->get();

        // Coğrafi dağılım
        $countryStats = $campaign->opens()
            ->select('country', DB::raw('count(*) as count'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Son açılmalar
        $recentOpens = $campaign->opens()
            ->with('recipient')
            ->latest('opened_at')
            ->limit(20)
            ->get();

        // Son tıklamalar
        $recentClicks = $campaign->clicks()
            ->with('recipient')
            ->latest('clicked_at')
            ->limit(20)
            ->get();

        return view('campaigns.analytics', compact(
            'campaign',
            'stats',
            'deviceStats',
            'browserStats',
            'osStats',
            'hourlyOpens',
            'topLinks',
            'countryStats',
            'recentOpens',
            'recentClicks'
        ));
    }
}

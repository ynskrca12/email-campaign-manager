<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaign;
use App\Models\EmailLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_campaigns' => EmailCampaign::count(),
            'active_campaigns' => EmailCampaign::where('status', 'processing')->count(),
            'total_emails_sent' => EmailLog::where('status', 'sent')->count(),
            'total_emails_failed' => EmailLog::where('status', 'failed')->count(),
        ];

        // Son 7 günlük istatistikler
        $dailyStats = EmailLog::select(
            DB::raw('DATE(sent_at) as date'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent'),
            DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
        )
        ->where('sent_at', '>=', now()->subDays(7))
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

        // Son kampanyalar
        $recentCampaigns = EmailCampaign::with('recipients')
            ->latest()
            ->take(5)
            ->get();

        // Son email logları
        $recentLogs = EmailLog::latest('sent_at')
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'dailyStats', 'recentCampaigns', 'recentLogs'));
    }
}

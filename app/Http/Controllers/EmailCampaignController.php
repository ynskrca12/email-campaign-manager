<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaign;
use App\Services\EmailCampaignService;
use Illuminate\Http\Request;

class EmailCampaignController extends Controller
{
    public function __construct(
        private EmailCampaignService $campaignService
    ) {}

    public function index()
    {
        $campaigns = EmailCampaign::withCount('recipients')
            ->latest()
            ->paginate(10);

        return view('campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return view('campaigns.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'from_email' => 'required|email',
            'from_name' => 'required|string|max:255',
            'delay_between_emails' => 'nullable|integer|min:1|max:60',
        ]);

        $campaign = $this->campaignService->createCampaign($validated);

        return redirect()
            ->route('campaigns.show', $campaign)
            ->with('success', 'Kampanya oluşturuldu!');
    }

    public function show(EmailCampaign $campaign)
    {
        $campaign->load(['recipients' => function($query) {
            $query->latest()->limit(100);
        }]);

        $stats = $this->campaignService->getCampaignStats($campaign);

        return view('campaigns.show', compact('campaign', 'stats'));
    }

    public function start(EmailCampaign $campaign)
    {
        try {
            $this->campaignService->startCampaign($campaign);
            return back()->with('success', 'Kampanya başlatıldı!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function pause(EmailCampaign $campaign)
    {
        $this->campaignService->pauseCampaign($campaign);
        return back()->with('success', 'Kampanya durduruldu!');
    }

    public function destroy(EmailCampaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('campaigns.index')->with('success', 'Kampanya silindi!');
    }
}

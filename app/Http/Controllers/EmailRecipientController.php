<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaign;
use App\Services\EmailCampaignService;
use Illuminate\Http\Request;

class EmailRecipientController extends Controller
{
    public function __construct(
        private EmailCampaignService $campaignService
    ) {}

    public function import(Request $request, EmailCampaign $campaign)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $count = $this->campaignService->importRecipients($campaign, $request->file('file'));
            return back()->with('success', "{$count} alıcı başarıyla içe aktarıldı!");
        } catch (\Exception $e) {
            return back()->with('error', 'İçe aktarma hatası: ' . $e->getMessage());
        }
    }

    public function store(Request $request, EmailCampaign $campaign)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
        ]);

        $this->campaignService->addRecipient($campaign, $validated);

        return back()->with('success', 'Alıcı eklendi!');
    }
}

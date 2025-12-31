<?php

namespace App\Services;

use App\Models\EmailCampaign;
use App\Models\EmailRecipient;
use App\Jobs\ProcessEmailCampaign;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmailRecipientsImport;

class EmailCampaignService
{
    public function createCampaign(array $data): EmailCampaign
    {
        return DB::transaction(function () use ($data) {
            return EmailCampaign::create([
                'name' => $data['name'],
                'subject' => $data['subject'],
                'body' => $data['body'],
                'from_email' => $data['from_email'],
                'from_name' => $data['from_name'],
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'delay_between_emails' => $data['delay_between_emails'] ?? 1,
            ]);
        });
    }

    public function importRecipients(EmailCampaign $campaign, $file): int
    {
        $import = new EmailRecipientsImport($campaign);
        Excel::import($import, $file);

        $count = $import->getRowCount();
        $campaign->update(['total_recipients' => $count]);

        return $count;
    }

    public function addRecipient(EmailCampaign $campaign, array $recipientData): EmailRecipient
    {
        $recipient = $campaign->recipients()->create($recipientData);
        $campaign->increment('total_recipients');

        return $recipient;
    }

    public function startCampaign(EmailCampaign $campaign): void
    {
        if ($campaign->status !== 'draft' && $campaign->status !== 'scheduled') {
            throw new \Exception('Bu kampanya başlatılamaz.');
        }

        $campaign->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        ProcessEmailCampaign::dispatch($campaign);
    }

    public function pauseCampaign(EmailCampaign $campaign): void
    {
        $campaign->update(['status' => 'draft']);
    }

    public function getCampaignStats(EmailCampaign $campaign): array
    {
        return [
            'total' => $campaign->total_recipients,
            'sent' => $campaign->sent_count,
            'failed' => $campaign->failed_count,
            'pending' => $campaign->total_recipients - $campaign->sent_count - $campaign->failed_count,
            'progress' => $campaign->progress_percentage,
        ];
    }
}

<?php

namespace App\Imports;

use App\Models\EmailCampaign;
use App\Models\EmailRecipient;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class EmailRecipientsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private EmailCampaign $campaign;
    private int $rowCount = 0;

    public function __construct(EmailCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function model(array $row): ?EmailRecipient
    {
        $this->rowCount++;

        // Custom fields - email ve name dışındaki kolonlar
        $customFields = [];
        $excludedFields = ['email', 'name', 'ad', 'soyad', 'isim'];

        foreach ($row as $key => $value) {
            if (!in_array(strtolower($key), $excludedFields)) {
                $customFields[$key] = $value;
            }
        }

        return new EmailRecipient([
            'campaign_id' => $this->campaign->id,
            'email' => $row['email'] ?? $row['e_posta'] ?? null,
            'name' => $row['name'] ?? $row['ad'] ?? $row['isim'] ?? null,
            'custom_fields' => !empty($customFields) ? $customFields : null,
            'status' => 'pending',
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'e_posta' => 'required_without:email|email',
        ];
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}

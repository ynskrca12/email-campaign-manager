<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailRecipient extends Model
{
    protected $fillable = [
        'campaign_id',
        'email',
        'name',
        'custom_fields',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'sent_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class);
    }
}

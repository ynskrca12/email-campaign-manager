<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailOpen extends Model
{
    protected $fillable = [
        'campaign_id',
        'recipient_id',
        'email',
        'ip_address',
        'user_agent',
        'country',
        'city',
        'device',
        'browser',
        'os',
        'opened_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(EmailRecipient::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    protected $fillable = [
        'campaign_id',
        'recipient_id',
        'to_email',
        'to_name',
        'subject',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
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

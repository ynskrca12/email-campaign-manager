<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailCampaign extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'subject',
        'body',
        'from_email',
        'from_name',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'total_recipients',
        'sent_count',
        'failed_count',
        'delay_between_emails',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function recipients(): HasMany
    {
        return $this->hasMany(EmailRecipient::class, 'campaign_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(EmailLog::class, 'campaign_id');
    }

    public function pendingRecipients(): HasMany
    {
        return $this->recipients()->where('status', 'pending');
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->total_recipients === 0) {
            return 0;
        }
        return round(($this->sent_count / $this->total_recipients) * 100, 2);
    }
}

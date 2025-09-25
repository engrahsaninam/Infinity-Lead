<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'email_account_id',
        'sent_at',
        'response',
        'error',
        'sent',
        'replied',
        'skipped',
        'open',
        'blacklisted',
        'bounced',
        'followup_1',
        'followup_2',
    ];
    public function subscriber(){
        return $this->belongsTo(Subscriber::class);
    }
    public function campaign(){
        return $this->belongsTo(Campaign::class);
    }

}

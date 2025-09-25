<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailAccount extends Model
{
    //
    protected $fillable=[
        'type',
        'user_id',
        'google_id',
        'email',
        'name',
        'access_token',
        'refresh_token',
        'expires_in',
        'profile',
        'per_minute',
        'volume',
        'password',
        'host',
        'port',
        'username',
        'encryption',
    ];
    public function getPortAttribute()
    {
        return (int) $this->attributes['port'];
    }
    

    const TYPE_GOOGLE='google';
    const TYPE_SMTP='smtp';
    public function getProfileAttribute()
    {
        return $this->attributes['profile'];
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_email_account');
    }

    public static function sent_emails_today($id): int
    {
        return Analytics::where('email_account_id', $id)
            ->where(function ($query) {
                $today = now('UTC')->toDateString();
                $query->whereDate('sent_at', $today)
                    ->orWhereDate('followup_1', $today)
                    ->orWhereDate('followup_2', $today);
            })->count();
    }
}

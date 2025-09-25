<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    //
    protected $fillable = [
        'user_id',
        'column_id',
        'email',
        'first_name',
        'middle_name',
        'last_name',
        'job_title',
        'phone',
        'city',
        'company',
        'website',
        'status',
        'linkedin',
        'sender_email_account_id',
    ];
    public function sender()
    {
        return $this->belongsTo(EmailAccount::class, 'sender_email_account_id');
    }

    const STATUS_NOT_CONTACTED = 0;
    const STATUS_CONTACTED = 1;
    const CONTACTED = 1;
    protected $appends = [
        'created_on',
    ];
    public function getCreatedOnAttribute()
    {
        return Infinity::stamp($this->attributes['created_at']);
    }
    public function chat()
    {
        return $this->hasMany(Chat::class);
    }
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($lead) {
            $lead->chat()->delete();
            $lead->notifications()->delete();
        });
    }
    public function notifications()
    {
        return $this->hasMany(ReplyNotification::class, 'lead_id');
    }
}

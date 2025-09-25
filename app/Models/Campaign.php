<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    const __RUN_PRODUCTION__ = true;
    const __PER_MINUTE_SENDING_LIMIT__ = 100;

    protected $fillable = [
        'user_id',
        'name',
        'status',
        'controls',
        'template_id',
        'list_id',
        'days',
        'day',
        'start',
        'end',
        'timezone',
        'deliverability',
        'subject',
        'attachment',
        'google_sheets',
    ];


    public function getOAttachmentAttribute()
    {
        $path = $this->attributes['attachment'] ?? '';
        if ($path) {
            return self::clear_stamp_from_attachment($path);
        }
        return '';
    }
    public static function clear_stamp_from_attachment($path)
    {
        $filename = basename($path);
        return preg_replace('/^\d+-/', '', $filename);
    }

    public function list(){
        return $this->belongsTo(Lists::class,'list_id');
    }
    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    const STATUS_DRAFT = 0;
    const STATUS_LAUNCHED = 1;
    const STATUS_PAUSED = 2;
    const STATUS_COMPLETED = 3;
    const SHEET_TYPE_CSV = 'csv';
    const SHEET_TYPE_GOOGLE = 'google';
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected $appends = [
        'o_attachment',
        'created_on',
        'accounts',
        'csv_name',
        'count_header',
        'total_sent_count',
        'total_count',
        'total_replied_count'
    ];
    public function getTotalCountAttribute()
    {
        $number = $this->analytics()->count();
        if ($number >= 1000000000) {
            return round($number / 1000000000, 1) . 'B';
        } elseif ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'k';
        }
        return $number;
    }
    public function getTotalSentCountAttribute()
    {

        $number = $this->analytics()->where('sent', 1)->count();
        if ($number >= 1000000000) {
            return round($number / 1000000000, 1) . 'B';
        } elseif ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'k';
        }
        return $number;
    }

    public function getTotalRepliedCountAttribute()
    {
        $number = $this->analytics()->where('replied', 1)->count();
        if ($number >= 1000000000) {
            return round($number / 1000000000, 1) . 'B';
        } elseif ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'k';
        }
        return $number;
    }

    public function getCsvNameAttribute()
    {
        if ($this->sheet_type === self::SHEET_TYPE_CSV && $this->csv) {
            $filename = pathinfo($this->csv, PATHINFO_BASENAME);
            return preg_replace('/^\d{10}-/', '', $filename);
        }
        return '';
    }
    public function getAccountsAttribute()
    {
        return $this->emailAccounts->pluck('id');
    }
    public function getCountHeaderAttribute()
    {
        if ($this->header) {
            return count($this->header->headers);
        }
        return 0;
    }

    public function getControlsAttribute()
    {
        return $this->attributes['controls'] ? explode(',', $this->attributes['controls']) : '';
    }
    public function getDaysAttribute()
    {
        return $this->attributes['days'] ? explode(',', $this->attributes['days']) : '';
    }
    public function getDeliveryabilityAttribute()
    {
        return $this->attributes['deliverability'] ? explode(',', $this->attributes['deliverability']) : '';
    }

    public function getGoogleSheetsAttribute()
    {
        $value = $this->attributes['google_sheets'] ?? '';
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value)) {
            return json_decode($value, true);
        }
        return [];
    }

    public function setControlsAttribute($value)
    {
        $this->attributes['controls'] = is_array($value) ? implode(',', $value) : $value;
    }
    public function setDaysAttribute($value)
    {
        $this->attributes['days'] = is_array($value) ? implode(',', $value) : $value;
    }
    public function setDeliverabilityAttribute($value)
    {
        $this->attributes['deliverability'] = is_array($value) ? implode(',', $value) : $value;
    }

    public function getCreatedOnAttribute()
    {
        return [
            'Y' => $this->created_at ? $this->created_at->format('Y') : null, // Year
            'y' => $this->created_at ? $this->created_at->format('y') : null, // Short Year
            'm' => $this->created_at ? $this->created_at->format('m') : null, // Month (01-12)
            'M' => $this->created_at ? $this->created_at->format('M') : null, // Short Month Name
            'F' => $this->created_at ? $this->created_at->format('F') : null, // Full Month Name
            'd' => $this->created_at ? $this->created_at->format('d') : null, // Day (01-31)
            'D' => $this->created_at ? $this->created_at->format('D') : null, // Short Day Name (Mon)
            'l' => $this->created_at ? $this->created_at->format('l') : null, // Full Day Name (Monday)
            'H' => $this->created_at ? $this->created_at->format('H') : null, // 24-hour format (00-23)
            'h' => $this->created_at ? $this->created_at->format('h') : null, // 12-hour format (01-12)
            'i' => $this->created_at ? $this->created_at->format('i') : null, // Minutes (00-59)
            's' => $this->created_at ? $this->created_at->format('s') : null, // Seconds (00-59)
            'A' => $this->created_at ? $this->created_at->format('A') : null, // AM/PM
            'a' => $this->created_at ? $this->created_at->format('a') : null, // am/pm
            'timestamp' => $this->created_at ? $this->created_at->timestamp : null, // Timestamp
        ];
    }
    public function emailAccounts()
    {
        return $this->belongsToMany(EmailAccount::class, 'campaign_email_account');
    }
    public function followups()
    {
        return $this->hasMany(Followup::class);
    }

    // public function rows()
    // {
    //     return $this->hasMany(self::class, 'campaign_id');
    // }
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($campaign) {
            $campaign->followups->each->delete();
        });
    }
    protected static function booted()
    {
        /*static::created(function ($campaign) {
            if ($campaign->status === self::STATUS_LAUNCHED) {
                ProcessCampaignJob::dispatch($campaign);
            }
        });
        static::updated(function ($campaign) {
            if ($campaign->isDirty('status') && $campaign->status === self::STATUS_LAUNCHED) {
                ProcessCampaignJob::dispatch($campaign);
            }
        });*/
    }

    public static function selectEmailAccount(Campaign $campaign): ?EmailAccount
    {
        foreach ($campaign->emailAccounts as $account) {
            if ($account->type === EmailAccount::TYPE_SMTP) {
                return $account;
            }

            if (EmailAccount::sent_emails_today($account->id) <= 40) {
                return $account;
            }
        }
        return null;
    }
    public function analytics(){
        return $this->hasMany(Analytics::class);
    }
}

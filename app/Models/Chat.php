<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    //
    protected $fillable = [
        'user_id',
        'lead_id',
        'from',
        'to',
        'cc',
        'response',
        'subject',
        'message_id',
        'message'
    ];



    public function getCcAttribute()
    {
        return $this->attributes['cc'] ? explode(',', $this->attributes['cc']) : '';
    }
    public function setCcAttribute($value)
    {
        $this->attributes['cc'] = is_array($value) ? implode(',', $value) : $value;
    }
    public function account()
    {
        return $this->belongsTo(EmailAccount::class, 'from');
    }
    protected $appends = [
        'created_on',
    ];
    public function getCreatedOnAttribute()
    {
        return Infinity::stamp($this->attributes['created_at']);
    }
    public function getResponseAttribute()
    {
        return json_decode($this->attributes['response'], true);
    }
}

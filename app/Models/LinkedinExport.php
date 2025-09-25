<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkedinExport extends Model
{
    //
    protected $fillable = [
        'user_id',
        'name',
        'file',
        'company_sync',
        'generate_email_sync',
        'verify_email_sync',
    ];
    protected $appends = [
        'created_on',
        'count',
    ];
    public function getCreatedOnAttribute()
    {
        return Infinity::stamp($this->attributes['created_at']);
    }
    public function getCountAttribute()
    {
        return $this->records()->count();
    }

    public function records()
    {
        return $this->hasMany(LinkedinExportData::class);
    }
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($lead) {
            $lead->records()->each(function ($record) {
                $record->delete();
            });
        });
    }
}

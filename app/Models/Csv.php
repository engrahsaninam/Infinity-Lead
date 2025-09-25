<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use function GuzzleHttp\json_decode;

class Csv extends Model
{
    protected $fillable=[
        'list_id',
        'file',
        'type',
        'status',
        'headers',
        'mapping',
        'rows',
        'uploaded',
        'spreadsheet_id',
        'sheet_id',
    ];
    public function subscribers(){return $this->hasMany(Subscriber::class);}
    protected $appends=[
        'file_name',
        'columns',
    ];
    public function getFileNameAttribute()
    {
        $baseName = basename($this->attributes['file']);
        // Remove time- prefix if present (e.g., 1751454053-1749700934-customers-10000.csv)
        $baseName = preg_replace('/^\d+-/', '', $baseName);
        return $baseName;
    }
    const STATUS_DRAFT=0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_COMPLETED = 2;
    const TYPE_CSV = 'csv';
    const TYPE_GOOGLE = 'google';
    public function getHeadersAttribute($value)
    {
        return json_decode($value, true);
    }
    public function getColumnsAttribute()
    {
        return count(json_decode($this->attributes['headers'],true));
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($csv) {
            $csv->subscribers()->chunkById(10000, function ($items) {
                $items->each->delete();
            });
        });
    }
}

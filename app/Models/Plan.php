<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $casts = [
        'options' => 'array',
    ];
    protected $fillable = [
        'name',
        'description',
        'price',
        'currency_id',
        'credits',
        'options'
    ];
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
    public static function defaultOptions()
    {
        $options = array_merge([], [
            'email_max' => '-1',
            'list_max' => '-1',
            'subscriber_max' => '-1',
            'subscriber_per_list_max' => '-1',
            'campaign_max' => '-1',
            'automation_max' => '-1',
            'max_size_upload_total' => '500',
            'max_file_size_upload' => '5',
        ]);
        return $options;
    }
}

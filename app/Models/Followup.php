<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Followup extends Model
{
    protected $fillable = [
        'campaign_id',
        'days',
        'message',
    ];
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}

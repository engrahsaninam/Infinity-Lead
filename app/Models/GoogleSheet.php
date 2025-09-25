<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleSheet extends Model
{
    //
    protected $fillable = [
        'user_id',
        'email',
        'google_access_token',
        'google_refresh_token',
        'google_token_expiry',
        'google_sheets'
    ];
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
}

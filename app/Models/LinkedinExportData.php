<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkedinExportData extends Model
{
    //
    protected $fillable = [
        'linkedin_export_id',
        'name',
        'first_name',
        'last_name',
        'title',
        'company',
        'profile',
        'url',
        'website',
        'region',
    ];
    public function emails()
    {
        return $this->hasMany(GeneratedEmail::class, 'linked_in_export_data_id');
    }
}

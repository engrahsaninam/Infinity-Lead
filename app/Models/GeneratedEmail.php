<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedEmail extends Model
{
    protected $fillable = ['linked_in_export_data_id', 'email', 'status'];

    public function record()
    {
        return $this->belongsTo(LinkedinExportData::class, 'linked_in_export_data_id');
    }
    //
}

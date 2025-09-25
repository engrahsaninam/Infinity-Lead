<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NameValidation extends Model
{
    //
    protected $fillable = [
        'symbols',
        'remove',
        'replace',
    ];
    public $timestamps = false;
}

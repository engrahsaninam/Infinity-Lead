<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapping extends Model
{
    protected $fillable = [
        'list_id',
        'tag_id',
        'mappings',
    ];
}

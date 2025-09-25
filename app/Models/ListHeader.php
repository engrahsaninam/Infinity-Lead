<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListHeader extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'headers',
        'mapping',
        'total_rows',
        'total_uploaded',
    ];

    public function setHeadersAttribute($value)
    {
        $this->attributes['headers'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getHeadersAttribute($value)
    {
        return json_decode($value, true);
    }
    public function setMappingAttribute($value)
    {
        $this->attributes['mapping'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getMappingAttribute($value)
    {
        return json_decode($value, true);
    }
}

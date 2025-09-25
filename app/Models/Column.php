<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    //
    protected $fillable = [
        'user_id',
        'name',
        'color',
        'primary',
    ];
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}

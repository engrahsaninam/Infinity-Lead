<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    protected $fillable = [
        'name',
        'domain',
        'format'
    ];
    public const FORMATS = [
        'first.last',
        'first-last',
        'firstlast',
        'f.last',
        'f-last',
        'flast',
        'last.first',
        'last-first',
        'lastfirst',
        'l.first',
        'l-first',
        'lfirst',

        'first',
        'last',
    ];
}

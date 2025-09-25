<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SendingServer extends Model
{
    const STATUS_ACTIVE=1;
    const STATUS_UNVERIFIED = 0;
    protected $fillable = [
        'user_id',
        'type',
        'api_key',
        'domain',
        'host',
        'name',
        'email',
        'username',
        'password',
        'port',
        'status',
        'encryption'
    ];
}

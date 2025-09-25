<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'key',
        'tool',
    ];

    const TOOL_EMAILABLE = 'emailable';
    const TOOL_REOON = 'reoon';
    const TOOL_ZERO_BOUNCE = 'zerobounce';
    const TOOL_NEVER_BOUNCE = 'neverbounce';
    const TOOL_MILLION_VERIFIER = 'millionverifier';

    const TYPE_AI = 'open_ai';
    const TYPE_VT = 'verification_tool';
    //
}

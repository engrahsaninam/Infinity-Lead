<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    //
    protected $fillable = [
        'name',
        'title',
        'profile',
        'twitter_url',
        'facebook_url',
        'instagram_url',
        'linkedin_url',
    ];
}

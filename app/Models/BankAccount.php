<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'stripe_card_id',
        'card_type',
        'card_number',
        'expiry',
        'preferred',
    ];
    public function getCardNumberAttribute($value)
    {
        return '**** **** **** **** ' . $value;
    }
}

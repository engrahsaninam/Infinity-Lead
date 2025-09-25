<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    //
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'publishable_key',
        'secret_key',
        'status',
        'instructions'
    ];
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;




    public function getInstructionsAttribute()
    {
        $attribute = $this->attributes['instructions'];
        $attribute = preg_replace(
            '/<p[^>]*>\s*Powered by\s*<a[^>]*>Froala Editor<\/a>\s*<\/p>/i',
            '',
            $attribute
        );
        return $attribute;
    }
}

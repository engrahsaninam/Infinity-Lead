<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable=[
        'user_id',
        'name',
        'type',
        'body',
        'signature',
    ];
    const HTML='html';
    const TEXT = 'text';
    public function getBodyAttribute(){
        $body = $this->attributes['body'];
        $body = preg_replace(
            '/<p[^>]*>\s*Powered by\s*<a[^>]*>Froala Editor<\/a>\s*<\/p>/i',
            '',
            $body
        );
        return $body;
    }

    public function getSignatureAttribute()
    {
        $signature = $this->attributes['signature'];
        $signature = preg_replace(
            '/<p[^>]*>\s*Powered by\s*<a[^>]*>Froala Editor<\/a>\s*<\/p>/i',
            '',
            $signature
        );
        return $signature;
    }
    protected $appends = [
        'created_on',
    ];
    public function getCreatedOnAttribute()
    {
        return Infinity::stamp($this->created_at);
    }
}

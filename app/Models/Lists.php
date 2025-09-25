<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lists extends Model
{
    protected $fillable=[
        'user_id',
        'name',
    ];
    protected $appends = [
        'created_on',
        'count_subscribers',
    ];


    const TYPE_SHEET='sheet';
    const TYPE_GOOGLE= 'google';
    public function getCreatedOnAttribute()
    {
        return Infinity::stamp($this->created_at);
    }
    public function csv(){
        return $this->hasOne(Csv::class,'list_id');
    }

    public function getCountSubscribersAttribute(){
        return $this->csv ? $this->csv->subscribers()->count() : 0;
    }
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($user) {
            $user->csv->delete();
        });
    }
}

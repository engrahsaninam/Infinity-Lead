<?php

namespace App\Models;

use App\Traits\CommonTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Subscription extends Model
{
    
    
    public function getCidAttribute(){
        return 'IL/INV/'.str_pad($this->attributes['id'],5,'X',STR_PAD_LEFT);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class)->withDefault();
    }
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class,'payment_method_id')->withDefault();
    }
    protected $fillable = [
        'user_id',
        'plan_id',
        'payment_method_id',
        'start',
        'end',
        'status',
        'reject_reason',
        'payment_response',
        'price',
        'credits',
        'recurring',
        'code'
    ];
    const STATUS_ACTIVE = 'active';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_PENDING = 'pending';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCEL = 'cancelled';
    const IS_RECURRING = 1;
    const NOT_RECURRING = 0;
    protected $appends=[
        'start_on',
        'end_on',
        'cid'
    ];
    public function getStartOnAttribute()
    {
        return Infinity::stamp($this->attributes['start']);
    }
    public function getEndOnAttribute()
    {
        return Infinity::stamp($this->attributes['end']);
    }

    public static function cancel($userId){
        $user=User::find($userId);
        $subscription = $user->activeSubscription();
        if (!$subscription) {
            return false;
        }
        $subscription->status = self::STATUS_CANCEL;
        $subscription->save();
        $user->update([
            'status' => User::STATUS_INACTIVE,
        ]);
        return true;
    }
}

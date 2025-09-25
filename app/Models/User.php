<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;

    const STATUS_INACTIVE = 0;
    const STATUS_RESTRICTED = 2;
    const STATUS_ACTIVE = 1;

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    public function bankAccounts(){
        return $this->hasMany(BankAccount::class);
    }
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'google_id',
        'profile',
        'role',
        'stripe_customer_id',
        'status',
        'email_verified_at',
        'remember_token'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $appends = [
        'name'
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function domains()
    {
        return $this->hasMany(Domain::class);
    }
    public function getNameAttribute()
    {
        $first_name = '';
        $last_name = '';
        if (array_key_exists('first_name', $this->attributes)) {
            $first_name = $this->attributes['first_name'];
        }
        if (array_key_exists('last_name', $this->attributes)) {
            $last_name = $this->attributes['last_name'];
        }

        return $first_name . ' ' . $last_name;
    }
    public function accounts()
    {
        return $this->hasMany(EmailAccount::class);
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class)->orderBy('created_at', 'desc');
    }
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->with('plan')
            ->latest() // optional: pick latest one
            ->where('status', Subscription::STATUS_ACTIVE)->first();
    }
    public function active_subscription()
    {
        return $this->hasOne(Subscription::class)
            ->with('plan')
            ->latest() // optional: pick latest one
            ->where('status', Subscription::STATUS_ACTIVE);
    }
    public function assigned_subscription()
    {
        return $this->hasOne(Subscription::class)
            ->with('plan')
            ->latest() 
            ->where('status', Subscription::STATUS_ASSIGNED);
    }
    public function getProfileAttribute()
    {
        $file = $this->attributes['profile'];
        if ($file && file_exists(public_path($file))) {
            return $file;
        }
        return url('profile.png');
    }

    public function blacklists()
    {
        return $this->hasMany(BlackList::class);
    }
    public function billing_info()
    {
        return $this->hasOne(BillingInfo::class);
    }
    public function campaigns(){
        return $this->hasMany(Campaign::class,'user_id');
    }
    public function tags()
    {
        return $this->hasMany(Tag::class, 'user_id');
    }

    public function lists()
    {
        return $this->hasMany(Lists::class,'user_id');
    }
    public function columns()
    {
        return $this->hasMany(Column::class, 'user_id');
    }
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($user) {
            $user->columns->each->delete();
            $user->bankAccounts->each->delete();
            $user->lists->each->delete();
            $user->campaigns->each->delete();
            $user->accounts->each->delete();
            $user->tags->each->delete();
            $user->subscriptions->each->delete();
            $user->billing_info?->delete();
            $user->blacklists->each->delete();
        });
    }
}

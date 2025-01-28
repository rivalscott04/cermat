<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'is_active',
        'province',
        'regency'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function subscriptions()
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    public function hasActiveSubscription()
    {
        return $this->subscription &&
            $this->subscription->payment_status === 'paid' &&
            $this->subscription->end_date > now();
    }
}

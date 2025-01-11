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
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    public function hasActiveSubscription()
    {
        // return $this->subscription &&
        //     $this->subscription->payment_status === 'paid' &&
        //     $this->subscription->end_date > now();
        return $this->is_active == 1;
    }
}

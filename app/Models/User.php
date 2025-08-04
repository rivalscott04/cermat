<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable
{
    use Notifiable, Impersonate;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'is_active',
        'province',
        'regency',
        'role'
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
        if ($this->is_active) {
            return true;
        }

        return $this->subscriptions &&
            $this->subscriptions->payment_status === 'paid' &&
            $this->subscriptions->end_date > now();
    }

    public function hasilTes()
    {
        return $this->hasMany(HasilTes::class);
    }

    public function userTryoutSoal()
    {
        return $this->hasMany(UserTryoutSoal::class);
    }

    public function getPaketAksesAttribute()
    {
        if (!$this->hasActiveSubscription()) {
            return 'free';
        }
        
        // Logika untuk menentukan paket berdasarkan subscription
        // Bisa disesuaikan dengan kebutuhan bisnis
        return 'premium'; // Default untuk sementara
    }

    /**
     * Check if user can be impersonated
     */
    public function canBeImpersonated()
    {
        return $this->role !== 'admin' && $this->id !== auth()->id();
    }

    /**
     * Check if user can impersonate others
     */
    public function canImpersonate()
    {
        return $this->role === 'admin';
    }
}

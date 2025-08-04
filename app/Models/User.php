<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;

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
     * Check if current user is being impersonated
     */
    public static function isImpersonating()
    {
        return Session::has('impersonate_id');
    }

    /**
     * Get the original admin user who is impersonating
     */
    public static function getOriginalUser()
    {
        if (self::isImpersonating()) {
            $originalUserId = Session::get('impersonate_id');
            return self::find($originalUserId);
        }
        return null;
    }

    /**
     * Get impersonation duration in minutes
     */
    public static function getImpersonationDuration()
    {
        if (self::isImpersonating() && Session::has('impersonate_started_at')) {
            return now()->diffInMinutes(Session::get('impersonate_started_at'));
        }
        return null;
    }

    /**
     * Check if user can be impersonated
     */
    public function canBeImpersonated()
    {
        return $this->role !== 'admin' && $this->id !== auth()->id();
    }
}

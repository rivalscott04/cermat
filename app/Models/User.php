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
        'role',
        'package' // Tambahkan field package
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Konstanta untuk package types
    const PACKAGE_KECERMATAN = 'kecermatan';
    const PACKAGE_PSIKOLOGI = 'psikologi';
    const PACKAGE_LENGKAP = 'lengkap';

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
     * Check if user can access Tes Kecermatan
     */
    public function canAccessKecermatan()
    {
        if (!$this->hasActiveSubscription()) {
            return false;
        }

        return in_array($this->package, [
            self::PACKAGE_KECERMATAN,
            self::PACKAGE_LENGKAP
        ]);
    }

    /**
     * Check if user can access Tryout CBT
     */
    public function canAccessTryout()
    {
        if (!$this->hasActiveSubscription()) {
            return false;
        }

        return in_array($this->package, [
            self::PACKAGE_PSIKOLOGI,
            self::PACKAGE_LENGKAP
        ]);
    }

    /**
     * Get available menu items based on package
     */
    public function getAvailableMenus()
    {
        $menus = [];

        if ($this->canAccessKecermatan()) {
            $menus[] = 'kecermatan';
        }

        if ($this->canAccessTryout()) {
            $menus[] = 'tryout';
        }

        return $menus;
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

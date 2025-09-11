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
    const PACKAGE_FREE = 'free';
    const PACKAGE_KECERMATAN = 'kecermatan';
    const PACKAGE_KECERDASAN = 'kecerdasan';
    const PACKAGE_KEPRIBADIAN = 'kepribadian';
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

        // Handle legacy mapping
        $legacyMapping = config('packages.legacy_mapping', []);
        $userPackage = $this->package ?? 'free';
        
        // Convert legacy package names to new ones
        return $legacyMapping[$userPackage] ?? $userPackage;
    }

    /**
     * Get package limits for current user
     */
    public function getPackageLimits()
    {
        $packageConfig = config('packages.package_limits');
        $userPackage = $this->paket_akses;
        
        return $packageConfig[$userPackage] ?? $packageConfig['free'];
    }

    /**
     * Get maximum tryouts allowed for current user
     */
    public function getMaxTryouts()
    {
        return $this->getPackageLimits()['max_tryouts'];
    }

    /**
     * Get allowed categories for current user
     */
    public function getAllowedCategories()
    {
        return $this->getPackageLimits()['allowed_categories'];
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
        // Free user bisa akses 1 tryout
        if (!$this->hasActiveSubscription()) {
            return true;
        }

        return in_array($this->package, [
            self::PACKAGE_KECERDASAN,
            self::PACKAGE_KEPRIBADIAN,
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
     * Check if user can access specific tryout based on package
     */
    public function canAccessSpecificTryout($tryout)
    {
        $userPackage = $this->paket_akses;
        $allowedTypes = $this->getAllowedPackageTypes($userPackage);
        
        return in_array($tryout->jenis_paket, $allowedTypes);
    }

    /**
     * Get allowed package types for user
     */
    private function getAllowedPackageTypes($userPackage)
    {
        $mapping = [
            'free' => ['free'],
            'kecerdasan' => ['free', 'kecerdasan'],
            'kepribadian' => ['free', 'kepribadian'],
            'lengkap' => ['free', 'kecerdasan', 'kepribadian', 'lengkap']
        ];
        
        return $mapping[$userPackage] ?? ['free'];
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

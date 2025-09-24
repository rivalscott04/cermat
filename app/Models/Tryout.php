<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tryout extends Model
{
    protected $fillable = [
        'judul',
        'deskripsi',
        'struktur',
        'shuffle_questions',
        'durasi_menit',
        'akses_paket',
        'jenis_paket',
        'is_active'
    ];

    protected $casts = [
        'struktur' => 'array',
        'shuffle_questions' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function userTryoutSoal()
    {
        return $this->hasMany(UserTryoutSoal::class);
    }

    public function blueprints()
    {
        return $this->hasMany(TryoutBlueprint::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPaket($query, $paket)
    {
        return $query->where('akses_paket', $paket);
    }

    public function scopeByJenisPaket($query, $jenisPaket)
    {
        return $query->where('jenis_paket', $jenisPaket);
    }

    public function scopeForUserPackage($query, $userPackage)
    {
        $allowedTypes = $this->getAllowedPackageTypes($userPackage);
        return $query->whereIn('jenis_paket', $allowedTypes);
    }

    private function getAllowedPackageTypes($userPackage)
    {
        // FREE users: always allow main types; quota handled in controller
        if ($userPackage === 'free') {
            return ['kecerdasan', 'kepribadian', 'lengkap'];
        }

        // Use dynamic mapping if available (not currently used here, but fetched to honor future configs)
        $dynamicMapping = \App\Models\PackageCategoryMapping::getAllMappings();
        // If dynamic mapping is empty, fall back to standard rules

        // For non-FREE packages
        $standard = [
            'kecerdasan' => ['kecerdasan'],
            'kepribadian' => ['kepribadian'],
            // Paket lengkap boleh melihat semua jenis
            'lengkap' => ['kecerdasan', 'kepribadian', 'lengkap']
        ];

        return $standard[$userPackage] ?? [];
    }

    /**
     * Get dynamic package mapping from database
     */
    public static function getDynamicPackageMapping()
    {
        return \App\Models\PackageCategoryMapping::getAllMappings();
    }

    public function getTotalSoalAttribute()
    {
        // Jika ada blueprints, hitung dari blueprints
        if ($this->relationLoaded('blueprints') || $this->blueprints()->exists()) {
            return $this->blueprints()->sum('jumlah');
        }
        
        // Fallback ke struktur lama untuk backward compatibility
        return array_sum($this->struktur ?? []);
    }
} 
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
        // Gunakan mapping dinamis berbasis kategori dari database
        $dynamicMapping = \App\Models\PackageCategoryMapping::getAllMappings();

        // Default fallback jika mapping kosong
        if (empty($dynamicMapping)) {
            return ['free'];
        }

        // Untuk paket selain FREE, hanya bisa mengakses paketnya sendiri
        if ($userPackage !== 'free') {
            $standard = [
                'kecerdasan' => ['kecerdasan'],
                'kepribadian' => ['kepribadian'],
                'lengkap' => ['lengkap']
            ];

            return $standard[$userPackage] ?? [];
        }

        // Untuk FREE, tentukan jenis yang boleh diakses berdasarkan overlap kategori
        $allowedTypes = ['free'];

        $freeCategories = $dynamicMapping['free'] ?? [];
        $kecerdasanCategories = $dynamicMapping['kecerdasan'] ?? [];
        if (!empty(array_intersect($freeCategories, $kecerdasanCategories))) {
            $allowedTypes[] = 'kecerdasan';
        }

        $kepribadianCategories = $dynamicMapping['kepribadian'] ?? [];
        if (!empty(array_intersect($freeCategories, $kepribadianCategories))) {
            $allowedTypes[] = 'kepribadian';
        }

        $lengkapCategories = $dynamicMapping['lengkap'] ?? [];
        if (!empty(array_intersect($freeCategories, $lengkapCategories))) {
            $allowedTypes[] = 'lengkap';
        }

        return $allowedTypes;
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
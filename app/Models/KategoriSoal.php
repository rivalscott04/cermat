<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriSoal extends Model
{
    protected $table = 'kategori_soal';
    
    protected $fillable = [
        'nama',
        'kode',
        'deskripsi',
        'scoring_mode',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function soals()
    {
        return $this->hasMany(Soal::class, 'kategori_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        if (is_bool($status)) {
            return $query->where('is_active', $status);
        }
        if ($status === 'aktif') {
            return $query->where('is_active', true);
        }
        if ($status === 'nonaktif') {
            return $query->where('is_active', false);
        }
        return $query;
    }

    public function scopeSearch($query, $term)
    {
        $trimmed = trim((string) $term);
        if ($trimmed === '') {
            return $query;
        }
        return $query->where(function ($q) use ($trimmed) {
            $q->where('nama', 'like', '%' . str_replace('%', '\\%', $trimmed) . '%')
              ->orWhere('kode', 'like', '%' . str_replace('%', '\\%', strtoupper($trimmed)) . '%');
        });
    }

    /**
     * Determine if this category uses weighted scoring (1..5 like TKP).
     * Backward-compat: NULL is treated as 'weighted'.
     */
    public function isWeighted(): bool
    {
        $mode = $this->scoring_mode;
        return $mode === null || $mode === 'weighted';
    }

    /**
     * Scope categories by scoring_mode. If $mode is 'weighted', include NULL for backward compat.
     */
    public function scopeByScoringMode($query, string $mode)
    {
        if ($mode === 'weighted') {
            return $query->where(function ($q) {
                $q->whereNull('scoring_mode')->orWhere('scoring_mode', 'weighted');
            });
        }
        return $query->where('scoring_mode', $mode);
    }
} 
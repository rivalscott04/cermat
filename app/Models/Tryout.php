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

    public function getTotalSoalAttribute()
    {
        return array_sum($this->struktur);
    }
} 
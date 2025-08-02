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
} 
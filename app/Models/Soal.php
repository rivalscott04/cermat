<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $table = 'soals';

    protected $fillable = [
        'pertanyaan',
        'tipe',
        'kategori_id',
        'pembahasan',
        'jawaban_benar',
        'gambar',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriSoal::class, 'kategori_id');
    }

    public function opsi()
    {
        return $this->hasMany(OpsiSoal::class, 'soal_id');
    }

    public function userTryoutSoal()
    {
        return $this->hasMany(UserTryoutSoal::class, 'soal_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKategori($query, $kategoriId)
    {
        return $query->where('kategori_id', $kategoriId);
    }

    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    // Accessor for image URL
    public function getGambarUrlAttribute()
    {
        if ($this->gambar) {
            return asset('storage/' . $this->gambar);
        }
        return null;
    }
}

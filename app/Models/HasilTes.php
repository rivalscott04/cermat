<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilTes extends Model
{
  protected $fillable = [
    'user_id',
    'skor_benar',
    'skor_salah',
    'waktu_total',
    'average_time',
    'detail_jawaban',
    'tanggal_tes',
  ];
}

<?php

namespace Database\Seeders;

use App\Models\KategoriSoal;
use Illuminate\Database\Seeder;

class KategoriSoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            [
                'nama' => 'Tes Wawasan Kebangsaan (TWK)',
                'kode' => 'TWK',
                'deskripsi' => 'Soal-soal tentang wawasan kebangsaan, Pancasila, UUD 1945, dan NKRI'
            ],
            [
                'nama' => 'Tes Intelejensi Umum (TIU)',
                'kode' => 'TIU',
                'deskripsi' => 'Soal-soal kemampuan verbal, numerik, dan logika'
            ],
            [
                'nama' => 'Tes Karakteristik Pribadi (TKP)',
                'kode' => 'TKP',
                'deskripsi' => 'Soal-soal tentang karakteristik pribadi dan kepribadian'
            ],
            [
                'nama' => 'Tes Psikotes',
                'kode' => 'PSIKOTES',
                'deskripsi' => 'Soal-soal psikotes untuk mengukur kemampuan kognitif'
            ],
            [
                'nama' => 'Tes Kemampuan Dasar (TKD)',
                'kode' => 'TKD',
                'deskripsi' => 'Soal-soal kemampuan dasar umum'
            ]
        ];

        foreach ($kategoris as $kategori) {
            KategoriSoal::create($kategori);
        }
    }
} 
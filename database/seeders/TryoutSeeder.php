<?php

namespace Database\Seeders;

use App\Models\Tryout;
use App\Models\TryoutBlueprint;
use App\Models\KategoriSoal;
use Illuminate\Database\Seeder;

class TryoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil kategori soal yang tersedia
        $kategoris = KategoriSoal::all();
        
        if ($kategoris->isEmpty()) {
            $this->command->warn('Tidak ada kategori soal ditemukan. Jalankan KategoriSoalSeeder terlebih dahulu.');
            return;
        }

        // Tryout 1: Tryout Dasar
        $tryout1 = Tryout::create([
            'judul' => 'Tryout Dasar POLRI',
            'deskripsi' => 'Tryout dasar untuk persiapan tes POLRI dengan 10 soal dalam 10 menit',
            'struktur' => [
                'TWK' => 4,
                'TIU' => 3,
                'TKP' => 3
            ],
            'durasi_menit' => 10,
            'akses_paket' => 'free',
            'jenis_paket' => 'free',
            'shuffle_questions' => true,
            'is_active' => true
        ]);

        // Blueprint untuk Tryout 1
        $this->createBlueprints($tryout1, [
            ['kategori' => 'TWK', 'level' => 'dasar', 'jumlah' => 2],
            ['kategori' => 'TWK', 'level' => 'mudah', 'jumlah' => 2],
            ['kategori' => 'TIU', 'level' => 'dasar', 'jumlah' => 2],
            ['kategori' => 'TIU', 'level' => 'mudah', 'jumlah' => 1],
            ['kategori' => 'TKP', 'level' => 'dasar', 'jumlah' => 2],
            ['kategori' => 'TKP', 'level' => 'mudah', 'jumlah' => 1],
        ]);

        // Tryout 2: Tryout Lanjutan
        $tryout2 = Tryout::create([
            'judul' => 'Tryout Lanjutan POLRI',
            'deskripsi' => 'Tryout lanjutan untuk persiapan tes POLRI dengan 10 soal dalam 10 menit',
            'struktur' => [
                'TWK' => 3,
                'TIU' => 4,
                'TKP' => 3
            ],
            'durasi_menit' => 10,
            'akses_paket' => 'premium',
            'jenis_paket' => 'kecerdasan',
            'shuffle_questions' => true,
            'is_active' => true
        ]);

        // Blueprint untuk Tryout 2
        $this->createBlueprints($tryout2, [
            ['kategori' => 'TWK', 'level' => 'mudah', 'jumlah' => 2],
            ['kategori' => 'TWK', 'level' => 'sedang', 'jumlah' => 1],
            ['kategori' => 'TIU', 'level' => 'mudah', 'jumlah' => 2],
            ['kategori' => 'TIU', 'level' => 'sedang', 'jumlah' => 2],
            ['kategori' => 'TKP', 'level' => 'mudah', 'jumlah' => 2],
            ['kategori' => 'TKP', 'level' => 'sedang', 'jumlah' => 1],
        ]);

        $this->command->info('TryoutSeeder berhasil dijalankan!');
        $this->command->info('Dibuat 2 tryout dengan total 10 soal masing-masing dalam 10 menit.');
    }

    /**
     * Buat blueprint untuk tryout
     */
    private function createBlueprints(Tryout $tryout, array $blueprints)
    {
        foreach ($blueprints as $blueprint) {
            $kategori = KategoriSoal::where('kode', $blueprint['kategori'])->first();
            
            if ($kategori) {
                TryoutBlueprint::create([
                    'tryout_id' => $tryout->id,
                    'kategori_id' => $kategori->id,
                    'level' => $blueprint['level'],
                    'jumlah' => $blueprint['jumlah']
                ]);
            }
        }
    }
}

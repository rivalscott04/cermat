<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'SILVER(TRYOUT)',
                'description' => 'Paket tryout untuk persiapan tes POLRI',
                'price' => 49900,
                'old_price' => 99500,
                'label' => null,
                'features' => [
                    'Akses hanya untuk 1x24 jam setelah terkonfirmasi pembayaran.',
                    'PRE TEST (Try Out) Simulasi Tes hanya 1 kali. (Sistem CAT).',
                    'Model soal mirip seperti Real Test dengan 1000 Bank Soal.'
                ],
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'GOLD',
                'description' => 'Paket lengkap dengan akses e-course',
                'price' => 999500,
                'old_price' => null,
                'label' => 'PALING LARIS',
                'features' => [
                    'Akses Member E-Course untuk 1 priode Seleksi Persiapan Tes POLRI 2025.',
                    'LATIHAN Soal per Mapel (Sistem CAT).',
                    'TRY OUT Simulasi Tes (Sistem CAT). Model sistem seperti Real Test dengan 1000 Bank Soal.',
                    'Bonus Latihan Kecermatan Unlimited dengan Sistem CAT, (Angka, Simbol, Huruf, dan Kombinasi).',
                    'Pembahasan Soal AKADEMIK dan PSIKOTES (Detail) pada menu Riwayat hasil Try Out.',
                    'Note:Member Akses E-Course dapat digunakan 24 jam Non Stop.'
                ],
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'PLATINUM',
                'description' => 'Paket premium dengan video pembelajaran dan modul',
                'price' => 1199500,
                'old_price' => null,
                'label' => 'DIREKOMENDASIKAN',
                'features' => [
                    'Akses Member E-Course dengan materi lengkap, untuk 1 priode Seleksi Persiapan Tes POLRI 2025.',
                    'Video Pembelajaran AKADEMIK meliputi: (Wawasan Kebangsaan, Pengetahuan Umum, Tes Penalaran Numerik, B. Inggris) dan PSIKOTEST Secara Singkat, Jelas dan Mudah Dipahami.',
                    'Modul Pembelajaran bisa diDOWNLOAD (Soft Copy).',
                    'LATIHAN Soal per Mapel (Sistem CAT).',
                    'TRY OUT Simulasi Tes (Sistem CAT). Model sistem seperti Real Test dengan 1000 Bank Soal.',
                    'Bonus Latihan Kecermatan Unlimited dengan Sistem CAT, (Angka, Simbol, Huruf, dan Kombinasi).',
                    'Pembahasan Soal AKADEMIK dan PSIKOTES (Detail) pada menu Riwayat hasil Try Out.',
                    'Note:Member Akses E-Course dapat digunakan 24 jam Non Stop.'
                ],
                'is_active' => true,
                'sort_order' => 3
            ]
        ];

        foreach ($packages as $packageData) {
            Package::create($packageData);
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        $packages = [
            [
                'name' => 'SILVER(TRYOUT)',
                'old_price' => '99.500',
                'price' => '49.900',
                'label' => '',
                'features' => [
                    'Akses hanya untuk 1x24 jam setelah terkonfirmasi pembayaran.',
                    'PRE TEST (Try Out) Simulasi Tes hanya 1 kali. (Sistem CAT).',
                    'Model soal mirip seperti Real Test dengan 1000 Bank Soal.'
                ]
            ],
            [
                'name' => 'GOLD',
                'price' => '999.500',
                'label' => 'PALING LARIS',
                'features' => [
                    'Akses Member E-Course untuk 1 priode Seleksi Persiapan Tes POLRI 2025.',
                    'LATIHAN Soal per Mapel (Sistem CAT).',
                    'TRY OUT Simulasi Tes (Sistem CAT). Model sistem seperti Real Test dengan 1000 Bank Soal.',
                    'Bonus Latihan Kecermatan Unlimited dengan Sistem CAT, (Angka, Simbol, Huruf, dan Kombinasi).',
                    'Pembahasan Soal AKADEMIK dan PSIKOTES (Detail) pada menu Riwayat hasil Try Out.',
                    'Note:Member Akses E-Course dapat digunakan 24 jam Non Stop.'
                ]
            ],
            [
                'name' => 'PLATINUM',
                'price' => '1.199.500',
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
                ]
            ]
        ];

        return view('kecermatan.harga', compact('packages'));
    }
}

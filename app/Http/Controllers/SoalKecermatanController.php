<?php

namespace App\Http\Controllers;

use App\Models\SoalKecermatan;
use App\Models\HasilTes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SoalKecermatanController extends Controller
{
    /**
     * Menampilkan halaman tes
     */
    public function index()
    {
        return view('kecermatan.soal');
    }

    /**
     * Mengambil set soal berikutnya
     */
    public function getNextSoal(Request $request)
    {
        $currentSet = $request->input('current_set', 0);
        $nextSet = $currentSet + 1;

        if ($nextSet > 10) {
            return response()->json([
                'success' => false,
                'message' => 'Tes telah selesai'
            ]);
        }

        // Ambil 5 huruf acak untuk set soal ini
        $huruf = $this->generateHurufSet();

        return response()->json([
            'success' => true,
            'data' => [
                'set_number' => $nextSet,
                'soal' => $huruf,
                'is_last' => ($nextSet == 10)
            ]
        ]);
    }

    /**
     * Generate set huruf untuk satu soal
     */
    private function generateHurufSet()
    {
        $huruf = range('A', 'Z');
        shuffle($huruf);
        $selected = array_slice($huruf, 0, 5);
        
        $soal = [];
        foreach ($selected as $index => $h) {
            $soal[] = [
                'huruf' => $h,
                'opsi' => chr(65 + $index) // A, B, C, D, E
            ];
        }

        return $soal;
    }

    /**
     * Simpan hasil tes
     */
    public function simpanHasil(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'skor_benar' => 'required|integer',
            'skor_salah' => 'required|integer',
            'waktu_total' => 'required|integer',
            'detail_jawaban' => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            $hasil = HasilTes::create([
                'user_id' => $request->user_id,
                'skor_benar' => $request->skor_benar,
                'skor_salah' => $request->skor_salah,
                'waktu_total' => $request->waktu_total,
                'detail_jawaban' => json_encode($request->detail_jawaban),
                'tanggal_tes' => now()
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Hasil tes berhasil disimpan',
                'data' => $hasil
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan hasil tes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

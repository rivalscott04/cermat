<?php

namespace App\Http\Controllers;

use App\Models\HasilTes;
use Illuminate\Http\Request;
use App\Models\SoalKecermatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SoalKecermatanController extends Controller
{
    /**
     * Menampilkan halaman tes
     */
    public function index(Request $request)
    {
        $questions = $request->query('questions', []);
        if (empty($questions) || count($questions) !== 10) {
            return redirect()->route('kecermatan');
        }

        // Store questions in session for later use
        session(['kecermatan_questions' => $questions]);

        return view('kecermatan.soal', [
            'questions' => $questions
        ]);
    }

    /**
     * Mengambil set soal berikutnya
     */
    public function getNextSoal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_set' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }

        $currentSet = $request->input('current_set');
        $nextSet = $currentSet + 1;

        // Get questions from session
        $questions = session('kecermatan_questions', []);

        if ($nextSet > 10 || empty($questions)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'set_number' => $nextSet,
                    'is_last' => true
                ]
            ]);
        }

        // Convert the current question to the required format
        $currentQuestion = $questions[$nextSet - 1];
        $karakterSet = $this->formatQuestionToKarakterSet($currentQuestion);

        return response()->json([
            'success' => true,
            'data' => [
                'set_number' => $nextSet,
                'soal' => $karakterSet,
                'is_last' => ($nextSet >= 10)
            ]
        ]);
    }


    /**
     * Generate set karakter untuk satu soal berdasarkan jenis
     */
    private function formatQuestionToKarakterSet($question)
    {
        $characters = str_split($question);
        $soal = [];

        // Take only the first 5 characters if the question is longer
        $characters = array_slice($characters, 0, 5);

        // Pad with random characters if less than 5
        while (count($characters) < 5) {
            $characters[] = chr(rand(65, 90)); // Add random uppercase letters
        }

        foreach ($characters as $index => $char) {
            $soal[] = [
                'huruf' => $char,
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
            // Hitung rata-rata waktu
            $totalQuestions = count($request->detail_jawaban);
            $averageTime = $totalQuestions > 0 ? $request->waktu_total / $totalQuestions : 0;

            // Simpan hasil tes ke database
            $hasil = HasilTes::create([
                'user_id' => $request->user_id,
                'skor_benar' => $request->skor_benar,
                'skor_salah' => $request->skor_salah,
                'waktu_total' => $request->waktu_total,
                'average_time' => $averageTime,
                'detail_jawaban' => json_encode($request->detail_jawaban), // Now includes detailed question data
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

    public function hasilTes()
    {
        $user_id = auth()->id();
        $hasilTes = HasilTes::where('user_id', $user_id)->latest()->first();

        if (!$hasilTes) {
            return redirect()->route('kecermatan')->with('error', 'Hasil tes tidak ditemukan.');
        }

        return view('kecermatan.hasil', ['hasilTes' => $hasilTes]);
    }
}

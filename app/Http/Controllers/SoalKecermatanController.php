<?php

namespace App\Http\Controllers;

use App\Models\HasilTes;
use Illuminate\Http\Request;
use App\Models\SoalKecermatan;
use Illuminate\Support\Facades\DB;
use App\Services\KecermatanService;
use Illuminate\Support\Facades\Validator;

class SoalKecermatanController extends Controller
{
    /**
     * Menampilkan halaman tes
     */
    public function index(Request $request)
    {
        $questions = $request->query('questions', []);
        $cardId = $request->query('card_id');
        if (empty($questions) || count($questions) !== 10) {
            return redirect()->route('kecermatan');
        }

        // Store questions in session for later use
        session([
            'kecermatan_questions' => $questions,
            'kecermatan_card_id' => $cardId
        ]);

        return view('kecermatan.soal', [
            'questions' => $questions,
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
    public function simpanHasil(Request $request, KecermatanService $service)
    {
        $request->validate([
            'user_id' => 'required',
            'skor_benar' => 'required|integer',
            'skor_salah' => 'required|integer',
            'waktu_total' => 'required|integer',
            'detail_jawaban' => 'required|array'
        ]);

        try {
            $data = $request->all();

            // Handle card_id from session if not in request or invalid
            if (!isset($data['card_id']) || $data['card_id'] === '?' || $data['card_id'] === '') {
                $data['card_id'] = session('kecermatan_card_id');
            }

            $hasil = $service->simpanHasil($data);

            return response()->json([
                'success' => true,
                'message' => 'Hasil tes berhasil disimpan',
                'data' => $hasil
            ]);
        } catch (\Exception $e) {
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

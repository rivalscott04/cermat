<?php

namespace App\Http\Controllers;

use App\Models\HasilTes;
use Illuminate\Http\Request;
use App\Models\TesKecermatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KecermatanController extends Controller
{
    /**
     * Menampilkan halaman tes kecermatan
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->canAccessKecermatan()) {
            return redirect()->route('user.profile', ['userId' => $user->id])
                ->with('error', 'Anda tidak memiliki akses ke Tes Kecermatan.');
        }

        return view('kecermatan.index', [
            'user' => $user,
            'packageInfo' => $this->getPackageInfo($user->package)
        ]);
    }

    private function getPackageInfo($package)
    {
        $packageInfo = [
            'kecermatan' => [
                'name' => 'Paket Kecermatan',
                'features' => ['Tes Kecermatan'],
                'description' => 'Akses khusus untuk Tes Kecermatan'
            ],
            'psikologi' => [
                'name' => 'Paket Psikologi',
                'features' => ['Tryout CBT'],
                'description' => 'Akses khusus untuk Tryout CBT'
            ],
            'lengkap' => [
                'name' => 'Paket Lengkap',
                'features' => ['Tes Kecermatan', 'Tryout CBT'],
                'description' => 'Akses lengkap untuk semua fitur'
            ]
        ];

        return $packageInfo[$package] ?? $packageInfo['kecermatan'];
    }

    /**
     * Menghasilkan karakter acak sesuai jenis yang dipilih
     */
    public function generateKarakter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis' => 'required|in:huruf,angka,simbol,acak'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis karakter tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $jenis = $request->jenis;
        $hasil = [];


        // Karakter yang tersedia
        $huruf = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $angka = '0123456789';
        $simbol = '!@#$%^&*()_+-=[]{}|;:",.<>?';

        // Menghasilkan 9 set karakter (untuk 9 kolom)
        for ($i = 0; $i < 10; $i++) {
            $karakterSet = '';

            switch ($jenis) {
                case 'huruf':
                    $karakterSet = $this->generateRandomString($huruf);
                    break;
                case 'angka':
                    $karakterSet = $this->generateRandomString($angka);
                    break;
                case 'simbol':
                    $karakterSet = $this->generateRandomString($simbol);
                    break;
                case 'acak':
                    $karakterSet = $this->generateRandomString($huruf . $angka . $simbol);
                    break;
            }

            $hasil[] = $karakterSet;
        }

        // Store session for later use
        session(['kecermatan_session' => true]);

        return response()->json([
            'success' => true,
            'data' => $hasil
        ]);
    }

    /**
     * Generate string acak dengan panjang 5 karakter
     */
    private function generateRandomString($characters)
    {
        $result = '';
        $length = strlen($characters);

        for ($i = 0; $i < 5; $i++) {
            $result .= $characters[rand(0, $length - 1)];
        }

        return $result;
    }

    /**
     * Mengambil statistik tes user
     */
    public function getStatistik($userId)
    {
        $statistik = HasilTes::where('user_id', $userId)
            ->selectRaw('
                jenis_tes,
                COUNT(*) as total_tes,
                AVG(skor_benar) as rata_rata_benar,
                AVG(waktu_total) as rata_rata_waktu,
                MAX(skor_benar) as nilai_tertinggi
            ')
            ->groupBy('jenis_tes')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $statistik
        ]);
    }

    public function riwayat($userId)
    {
        $hasil = HasilTes::where('user_id', $userId)
            ->orderBy('tanggal_tes', 'asc')
            ->get();

        return view('kecermatan.riwayat', compact('hasil'));
    }

    public function detailTes($id)
    {
        $hasilTes = HasilTes::findOrFail($id);
        return view('kecermatan.detail', compact('hasilTes'));
    }
}

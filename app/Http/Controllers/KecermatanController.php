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
    public function index()
    {
        // Middleware sudah menghandle validasi, tapi Anda bisa menambah validasi extra jika diperlukan
        $user = Auth::user();

        // Optional: Double check di controller level
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
     * Menyimpan hasil tes kecermatan
     */
    public function simpanHasil(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'jenis_tes' => 'required|in:huruf,angka,simbol,acak',
            'jawaban' => 'required|array|size:9',
            'jawaban.*' => 'required|string|size:5',
            'waktu_pengerjaan' => 'required|integer',
            'jumlah_benar' => 'required|integer|min:0|max:9',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $hasilTes = HasilTes::create([
                'user_id' => $request->user_id,
                'jenis_tes' => $request->jenis_tes,
                'jawaban' => json_encode($request->jawaban),
                'waktu_pengerjaan' => $request->waktu_pengerjaan,
                'jumlah_benar' => $request->jumlah_benar,
                'tanggal_tes' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Hasil tes berhasil disimpan',
                'data' => $hasilTes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan hasil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan hasil tes user
     */
    public function hasilTes($userId)
    {
        $hasil = HasilTes::where('user_id', $userId)
            ->orderBy('tanggal_tes', 'desc')
            ->get();

        return view('kecermatan.hasil', compact('hasil'));
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
                AVG(jumlah_benar) as rata_rata_benar,
                AVG(waktu_pengerjaan) as rata_rata_waktu,
                MAX(jumlah_benar) as nilai_tertinggi
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

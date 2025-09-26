<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\HasilTes;
use App\Models\Tryout;
use App\Models\KategoriSoal;
use App\Models\UserTryoutSession;
use App\Models\PackageCategoryMapping;
use Illuminate\Support\Facades\DB;

class LaporanKemampuanController extends Controller
{
    /**
     * Halaman utama laporan kemampuan
     */
    public function index()
    {
        return view('laporan-kemampuan.index');
    }

    /**
     * Halaman laporan paket lengkap
     */
    public function paketLengkap()
    {
        $users = User::whereHas('hasilTes')
            ->with(['hasilTes' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get();

        return view('laporan-kemampuan.paket-lengkap', compact('users'));
    }

    /**
     * Halaman laporan per paket
     */
    public function perPaket()
    {
        $packages = PackageCategoryMapping::with('kategori')
            ->select('package_type')
            ->distinct()
            ->get();

        return view('laporan-kemampuan.per-paket', compact('packages'));
    }

    /**
     * Halaman detail laporan per paket
     */
    public function perPaketDetail($package)
    {
        $packageName = $package;
        return view('laporan-kemampuan.per-paket-detail', compact('packageName'));
    }

    /**
     * Get kategori soal berdasarkan paket
     */
    public function getKategoriByPaket(Request $request)
    {
        $packageType = $request->package_type;
        
        $kategori = PackageCategoryMapping::where('package_type', $packageType)
            ->with('kategori')
            ->get()
            ->pluck('kategori');

        return response()->json($kategori);
    }

    /**
     * Get siswa berdasarkan paket dan kategori
     */
    public function getSiswaByPaket(Request $request)
    {
        $packageType = $request->package_type;
        $kategoriId = $request->kategori_id;

        // Get kategori soal yang termasuk dalam paket
        $kategoriIds = PackageCategoryMapping::where('package_type', $packageType)
            ->pluck('kategori_id');

        // Get siswa yang pernah tes dengan kategori dalam paket tersebut
        $users = User::whereHas('hasilTes', function($query) use ($kategoriIds, $kategoriId) {
            if ($kategoriId) {
                // Filter berdasarkan kategori tertentu
                $query->where('kategori_soal_id', $kategoriId);
            } else {
                // Ambil semua kategori dalam paket
                $query->whereIn('kategori_soal_id', $kategoriIds);
            }
        })->get();

        return response()->json($users);
    }

    /**
     * Generate laporan paket lengkap untuk siswa
     */
    public function generateLaporanPaketLengkap(Request $request)
    {
        $userId = $request->user_id;
        $user = User::findOrFail($userId);

        // Get semua hasil tes siswa
        $hasilTes = HasilTes::where('user_id', $userId)
            ->with(['kategoriSoal', 'tryout'])
            ->orderBy('created_at', 'asc')
            ->get();

        if ($hasilTes->isEmpty()) {
            return redirect()->back()->with('error', 'Siswa belum memiliki data tes.');
        }

        // Analisis data
        $analisis = $this->analisisPaketLengkap($hasilTes);

        return view('laporan-kemampuan.detail-paket-lengkap', compact('user', 'analisis'));
    }

    /**
     * Generate laporan per paket untuk siswa
     */
    public function generateLaporanPerPaket(Request $request)
    {
        $userId = $request->user_id;
        $packageType = $request->package_type;
        $kategoriId = $request->kategori_id;

        $user = User::findOrFail($userId);

        // Get kategori soal dalam paket
        $kategoriIds = PackageCategoryMapping::where('package_type', $packageType)
            ->pluck('kategori_id');

        // Get hasil tes untuk kategori dalam paket
        $hasilTes = HasilTes::where('user_id', $userId)
            ->whereIn('kategori_soal_id', $kategoriIds)
            ->with(['kategoriSoal', 'tryout'])
            ->orderBy('created_at', 'asc')
            ->get();

        if ($hasilTes->isEmpty()) {
            return redirect()->back()->with('error', 'Siswa belum memiliki data tes untuk paket ini.');
        }

        // Analisis data
        $analisis = $this->analisisPerPaket($hasilTes, $packageType);

        return view('laporan-kemampuan.detail-per-paket', compact('user', 'analisis', 'packageName'));
    }

    /**
     * Analisis data untuk paket lengkap
     */
    private function analisisPaketLengkap($hasilTes)
    {
        $groupedByKategori = $hasilTes->groupBy('kategori_soal_id');
        
        $analisis = [];
        
        foreach ($groupedByKategori as $kategoriId => $tes) {
            $kategori = $tes->first()->kategoriSoal;
            $tesPertama = $tes->first();
            $tesTerakhir = $tes->last();
            
            $analisis[] = [
                'kategori' => $kategori,
                'tes_pertama' => $tesPertama,
                'tes_terakhir' => $tesTerakhir,
                'selisih_skor' => $tesTerakhir->skor - $tesPertama->skor,
                'persentase_naik' => $tesPertama->skor > 0 ? (($tesTerakhir->skor - $tesPertama->skor) / $tesPertama->skor) * 100 : 0,
                'total_tes' => $tes->count(),
                'skor_tertinggi' => $tes->max('skor'),
                'skor_terendah' => $tes->min('skor'),
                'rata_rata' => $tes->avg('skor')
            ];
        }

        // Sort berdasarkan selisih skor (terbesar dulu)
        usort($analisis, function($a, $b) {
            return $b['selisih_skor'] <=> $a['selisih_skor'];
        });

        return $analisis;
    }

    /**
     * Analisis data untuk per paket
     */
    private function analisisPerPaket($hasilTes, $packageType)
    {
        $groupedByKategori = $hasilTes->groupBy('kategori_soal_id');
        
        $analisis = [];
        
        foreach ($groupedByKategori as $kategoriId => $tes) {
            $kategori = $tes->first()->kategoriSoal;
            $tesPertama = $tes->first();
            $tesTerakhir = $tes->last();
            
            $analisis[] = [
                'kategori' => $kategori,
                'tes_pertama' => $tesPertama,
                'tes_terakhir' => $tesTerakhir,
                'selisih_skor' => $tesTerakhir->skor - $tesPertama->skor,
                'persentase_naik' => $tesPertama->skor > 0 ? (($tesTerakhir->skor - $tesPertama->skor) / $tesPertama->skor) * 100 : 0,
                'total_tes' => $tes->count(),
                'skor_tertinggi' => $tes->max('skor'),
                'skor_terendah' => $tes->min('skor'),
                'rata_rata' => $tes->avg('skor')
            ];
        }

        // Sort berdasarkan selisih skor (terbesar dulu)
        usort($analisis, function($a, $b) {
            return $b['selisih_skor'] <=> $a['selisih_skor'];
        });

        return $analisis;
    }
}

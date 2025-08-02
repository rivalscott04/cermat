<?php

namespace App\Http\Controllers;

use App\Models\Tryout;
use App\Models\KategoriSoal;
use App\Models\Soal;
use App\Models\UserTryoutSoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TryoutController extends Controller
{
    public function index()
    {
        $tryouts = Tryout::active()->paginate(20);
        return view('admin.tryout.index', compact('tryouts'));
    }

    public function create()
    {
        $kategoris = KategoriSoal::active()->get();
        return view('admin.tryout.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'durasi_menit' => 'required|integer|min:1',
            'akses_paket' => 'required|in:free,premium,vip',
            'struktur' => 'required|array',
            'struktur.*' => 'required|integer|min:0'
        ]);

        // Validate that we have enough questions for each category
        foreach ($request->struktur as $kategoriId => $jumlah) {
            if ($jumlah > 0) {
                $availableSoals = Soal::active()->byKategori($kategoriId)->count();
                if ($availableSoals < $jumlah) {
                    return back()->withErrors([
                        "struktur.{$kategoriId}" => "Kategori ini hanya memiliki {$availableSoals} soal, tidak cukup untuk {$jumlah} soal"
                    ]);
                }
            }
        }

        Tryout::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'struktur' => $request->struktur,
            'durasi_menit' => $request->durasi_menit,
            'akses_paket' => $request->akses_paket
        ]);

        return redirect()->route('admin.tryout.index')->with('success', 'Tryout berhasil dibuat');
    }

    public function show(Tryout $tryout)
    {
        $tryout->load(['userTryoutSoal.soal.kategori']);
        return view('admin.tryout.show', compact('tryout'));
    }

    public function edit(Tryout $tryout)
    {
        $kategoris = KategoriSoal::active()->get();
        return view('admin.tryout.edit', compact('tryout', 'kategoris'));
    }

    public function update(Request $request, Tryout $tryout)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'durasi_menit' => 'required|integer|min:1',
            'akses_paket' => 'required|in:free,premium,vip',
            'struktur' => 'required|array',
            'struktur.*' => 'required|integer|min:0'
        ]);

        $tryout->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'struktur' => $request->struktur,
            'durasi_menit' => $request->durasi_menit,
            'akses_paket' => $request->akses_paket
        ]);

        return redirect()->route('admin.tryout.index')->with('success', 'Tryout berhasil diperbarui');
    }

    public function destroy(Tryout $tryout)
    {
        $tryout->delete();
        return redirect()->route('admin.tryout.index')->with('success', 'Tryout berhasil dihapus');
    }

    // User-facing methods
    public function userIndex()
    {
        $user = auth()->user();
        $paket = $user->paket_akses;
        
        $tryouts = Tryout::active()
            ->where(function($query) use ($paket) {
                switch ($paket) {
                    case 'free':
                        $query->where('akses_paket', 'free');
                        break;
                    case 'premium':
                        $query->whereIn('akses_paket', ['free', 'premium']);
                        break;
                    case 'vip':
                        $query->whereIn('akses_paket', ['free', 'premium', 'vip']);
                        break;
                }
            })
            ->get();

        return view('user.tryout.index', compact('tryouts'));
    }

    public function start(Tryout $tryout)
    {
        $user = auth()->user();
        
        // Check if user can access this tryout
        if (!$this->canAccessTryout($user, $tryout)) {
            return redirect()->route('user.tryout.index')->with('error', 'Anda tidak memiliki akses ke tryout ini');
        }

        // Check if user already has questions for this tryout
        $existingSoals = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->count();

        if ($existingSoals == 0) {
            // Generate questions for user
            $this->generateQuestionsForUser($user, $tryout);
        }

        return redirect()->route('user.tryout.work', $tryout);
    }

    public function work(Tryout $tryout, Request $request)
    {
        $user = auth()->user();
        $questionNumber = $request->get('question', 1);
        
        $userSoals = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->with(['soal.opsi', 'soal.kategori'])
            ->orderBy('urutan')
            ->get();

        if ($userSoals->isEmpty()) {
            return redirect()->route('user.tryout.index')->with('error', 'Soal tidak ditemukan');
        }

        $currentQuestion = $userSoals->where('urutan', $questionNumber)->first();
        if (!$currentQuestion) {
            $currentQuestion = $userSoals->first();
        }

        $totalQuestions = $userSoals->count();
        
        // Calculate time left (simple implementation - can be enhanced with session storage)
        $timeLeft = $tryout->durasi_menit * 60; // Default to full time

        return view('user.tryout.work', compact('tryout', 'userSoals', 'currentQuestion', 'totalQuestions', 'timeLeft'));
    }

    public function submitAnswer(Request $request, Tryout $tryout)
    {
        $request->validate([
            'soal_id' => 'required|exists:soals,id',
            'jawaban' => 'required|string'
        ]);

        $user = auth()->user();
        
        $userSoal = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('soal_id', $request->soal_id)
            ->first();

        if (!$userSoal) {
            return response()->json(['success' => false, 'message' => 'Soal tidak ditemukan']);
        }

        $jawabanArray = json_decode($request->jawaban, true);
        if (!is_array($jawabanArray)) {
            $jawabanArray = [$request->jawaban];
        }

        $soal = $userSoal->soal;
        $skor = $this->calculateScore($soal, $jawabanArray);

        $userSoal->update([
            'jawaban_user' => $jawabanArray,
            'skor' => $skor,
            'sudah_dijawab' => true
        ]);

        return response()->json([
            'success' => true,
            'skor' => $skor
        ]);
    }

    public function finish(Tryout $tryout)
    {
        $user = auth()->user();
        
        $userAnswers = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->with(['soal.opsi', 'soal.kategori'])
            ->orderBy('urutan')
            ->get();

        if ($userAnswers->isEmpty()) {
            return redirect()->route('user.tryout.index')->with('error', 'Data tryout tidak ditemukan');
        }

        $totalScore = $userAnswers->sum('skor');
        $totalQuestions = $userAnswers->count();
        $correctAnswers = $userAnswers->where('skor', '>', 0)->count();
        $wrongAnswers = $totalQuestions - $correctAnswers;

        // Calculate category scores
        $categoryScores = [];
        $categoryGroups = $userAnswers->groupBy('soal.kategori_id');
        
        foreach ($categoryGroups as $kategoriId => $answers) {
            $kategori = $answers->first()->soal->kategori;
            $categoryScore = $answers->sum('skor');
            $categoryCorrect = $answers->where('skor', '>', 0)->count();
            $categoryTotal = $answers->count();
            
            $categoryScores[] = [
                'nama' => $kategori->nama,
                'score' => $categoryScore,
                'correct' => $categoryCorrect,
                'total' => $categoryTotal
            ];
        }

        return view('user.tryout.result', compact(
            'tryout', 
            'userAnswers', 
            'totalScore', 
            'totalQuestions', 
            'correctAnswers', 
            'wrongAnswers',
            'categoryScores'
        ));
    }

    private function canAccessTryout($user, $tryout)
    {
        $paket = $user->paket_akses;
        
        switch ($tryout->akses_paket) {
            case 'free':
                return true;
            case 'premium':
                return in_array($paket, ['premium', 'vip']);
            case 'vip':
                return $paket === 'vip';
            default:
                return false;
        }
    }

    private function generateQuestionsForUser($user, $tryout)
    {
        $urutan = 1;
        
        foreach ($tryout->struktur as $kategoriId => $jumlah) {
            if ($jumlah > 0) {
                $soals = Soal::active()
                    ->byKategori($kategoriId)
                    ->inRandomOrder()
                    ->limit($jumlah)
                    ->get();

                foreach ($soals as $soal) {
                    UserTryoutSoal::create([
                        'user_id' => $user->id,
                        'tryout_id' => $tryout->id,
                        'soal_id' => $soal->id,
                        'urutan' => $urutan++
                    ]);
                }
            }
        }
    }

    private function calculateScore($soal, $jawaban)
    {
        switch ($soal->tipe) {
            case 'benar_salah':
                return $jawaban[0] === $soal->jawaban_benar ? 1 : 0;
                
            case 'pg_satu':
                return $jawaban[0] === $soal->jawaban_benar ? 1 : 0;
                
            case 'pg_bobot':
                $totalBobot = 0;
                foreach ($jawaban as $opsi) {
                    $opsiSoal = $soal->opsi()->where('opsi', $opsi)->first();
                    if ($opsiSoal) {
                        $totalBobot += $opsiSoal->bobot;
                    }
                }
                return min($totalBobot, 1); // Max score is 1
                
            case 'pg_pilih_2':
                if (count($jawaban) !== 2) return 0;
                
                $skor = 0;
                foreach ($jawaban as $opsi) {
                    $opsiSoal = $soal->opsi()->where('opsi', $opsi)->first();
                    if ($opsiSoal && $opsiSoal->bobot > 0) {
                        $skor += 0.5; // Each correct answer worth 0.5
                    }
                }
                return $skor;
                
            default:
                return 0;
        }
    }
} 
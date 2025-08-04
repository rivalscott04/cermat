<?php

namespace App\Http\Controllers;

use App\Models\Tryout;
use App\Models\KategoriSoal;
use App\Models\Soal;
use App\Models\UserTryoutSoal;
use App\Models\UserTryoutSession; // Tambahkan model ini untuk tracking session
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        // Statistik Penggunaan
        $totalPeserta = $tryout->userTryoutSoal()->distinct('user_id')->count();
        $totalAttempts = UserTryoutSession::where('tryout_id', $tryout->id)->count();
        $completedSessions = UserTryoutSession::where('tryout_id', $tryout->id)
            ->where('status', 'completed')
            ->count();
        $activeSessions = UserTryoutSession::where('tryout_id', $tryout->id)
            ->where('status', 'active')
            ->count();

        // Hitung rata-rata skor
        $averageScore = 0;
        if ($totalPeserta > 0) {
            $scores = DB::table('user_tryout_soal')
                ->select('user_id', DB::raw('SUM(skor) as total_score'))
                ->where('tryout_id', $tryout->id)
                ->groupBy('user_id')
                ->pluck('total_score');
            $averageScore = $scores->avg();
        }

        // Struktur soal dengan informasi kategori
        $strukturSoal = [];
        $totalSoal = array_sum($tryout->struktur ?? []);

        foreach ($tryout->struktur ?? [] as $kategoriId => $jumlah) {
            if ($jumlah > 0) {
                $kategori = KategoriSoal::find($kategoriId);
                $soalTersedia = $kategori ? $kategori->soals()->count() : 0;
                $persentase = $totalSoal > 0 ? round(($jumlah / $totalSoal) * 100, 1) : 0;

                $strukturSoal[] = [
                    'kategori_id' => $kategoriId,
                    'kategori' => $kategori,
                    'jumlah' => $jumlah,
                    'soal_tersedia' => $soalTersedia,
                    'persentase' => $persentase
                ];
            }
        }

        // Peserta terbaru
        $recentParticipants = [];
        if ($totalPeserta > 0) {
            $recentSessions = UserTryoutSession::with('user')
                ->where('tryout_id', $tryout->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            foreach ($recentSessions as $session) {
                $userScore = UserTryoutSoal::where('user_id', $session->user_id)
                    ->where('tryout_id', $tryout->id)
                    ->sum('skor');

                $recentParticipants[] = [
                    'session' => $session,
                    'score' => $userScore
                ];
            }
        }

        // Tingkat penyelesaian
        $completionRate = $totalAttempts > 0 ?
            number_format(($completedSessions / $totalAttempts) * 100, 1) : 0;

        return view('admin.tryout.show', compact(
            'tryout',
            'totalPeserta',
            'totalAttempts',
            'completedSessions',
            'activeSessions',
            'averageScore',
            'strukturSoal',
            'totalSoal',
            'recentParticipants',
            'completionRate'
        ));
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
    public function userIndex(Request $request)
    {
        // Clear auto fullscreen session if requested
        if ($request->has('clear_auto_fullscreen') || $request->isMethod('post')) {
            session()->forget('auto_fullscreen_tryout');
        }
        
        $user = auth()->user();
        $paket = $user->paket_akses;

        $tryouts = Tryout::active()
            ->where(function ($query) use ($paket) {
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

    public function start(Tryout $tryout, Request $request)
    {
        $user = auth()->user();

        // Check if user can access this tryout
        if (!$this->canAccessTryout($user, $tryout)) {
            return redirect()->route('user.tryout.index')->with('error', 'Anda tidak memiliki akses ke tryout ini');
        }

        // Check if this is a restart request
        $restart = $request->get('restart', false);

        // Check existing session
        $existingSession = UserTryoutSession::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('status', 'active')
            ->first();

        // If restart is requested or no active session, create new session
        if ($restart || !$existingSession) {
            // Delete existing questions and session
            UserTryoutSoal::where('user_id', $user->id)
                ->where('tryout_id', $tryout->id)
                ->delete();

            if ($existingSession) {
                $existingSession->update(['status' => 'abandoned']);
            }

            // PERUBAHAN: Create new session dengan shuffle_seed yang baru
            $newSession = UserTryoutSession::create([
                'user_id' => $user->id,
                'tryout_id' => $tryout->id,
                'started_at' => now(),
                'status' => 'active',
                'shuffle_seed' => rand(1, 999999) // Generate seed acak untuk session ini
            ]);

            // Generate new questions dengan seed dari session
            $this->generateQuestionsForUser($user, $tryout, $newSession->shuffle_seed);
        }

        // Set session flag untuk auto fullscreen
        session(['auto_fullscreen_tryout' => $tryout->id]);
        
        return redirect()->route('user.tryout.work', $tryout);
    }

    public function getRemainingTime(Tryout $tryout)
    {
        $user = auth()->user();

        // Get active session
        $session = UserTryoutSession::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak aktif'
            ]);
        }

        // Calculate remaining time
        $startTime = Carbon::parse($session->started_at);
        $currentTime = now();
        $elapsedMinutes = $startTime->diffInMinutes($currentTime);
        $remainingMinutes = max(0, $tryout->durasi_menit - $elapsedMinutes);
        $remainingSeconds = $remainingMinutes * 60;

        // Get more precise remaining time in seconds
        $elapsedSeconds = $startTime->diffInSeconds($currentTime);
        $totalSeconds = $tryout->durasi_menit * 60;
        $preciseRemainingSeconds = max(0, $totalSeconds - $elapsedSeconds);

        return response()->json([
            'success' => true,
            'remainingTime' => $preciseRemainingSeconds,
            'elapsedTime' => $elapsedSeconds,
            'totalTime' => $totalSeconds
        ]);
    }

    public function work(Tryout $tryout, Request $request)
    {
        $user = auth()->user();
        $questionNumber = $request->get('question', 1);

        // Get or create active session
        $session = UserTryoutSession::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return redirect()->route('user.tryout.start', $tryout)
                ->with('error', 'Session tidak ditemukan. Silakan mulai tryout kembali.');
        }

        // Get user questions
        $userSoals = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->with(['soal.opsi', 'soal.kategori'])
            ->orderBy('urutan')
            ->get();

        // If no questions found, generate them
        if ($userSoals->isEmpty()) {
            $this->generateQuestionsForUser($user, $tryout, $session->shuffle_seed);

            $userSoals = UserTryoutSoal::where('user_id', $user->id)
                ->where('tryout_id', $tryout->id)
                ->with(['soal.opsi', 'soal.kategori'])
                ->orderBy('urutan')
                ->get();
        }

        if ($userSoals->isEmpty()) {
            return redirect()->route('user.tryout.index')
                ->with('error', 'Gagal menggenerate soal. Silakan coba lagi.');
        }

        $currentQuestion = $userSoals->where('urutan', $questionNumber)->first();
        if (!$currentQuestion) {
            $currentQuestion = $userSoals->first();
        }

        // PERUBAHAN: Shuffle options dengan session seed
        $currentQuestion = $this->shuffleQuestionOptions($currentQuestion, $session->shuffle_seed);

        $totalQuestions = $userSoals->count();

        // Calculate remaining time
        $startTime = Carbon::parse($session->started_at);
        $currentTime = now();
        $elapsedSeconds = $startTime->diffInSeconds($currentTime);
        $totalSeconds = $tryout->durasi_menit * 60;
        $timeLeft = max(0, $totalSeconds - $elapsedSeconds);

        // If time is up, redirect to finish
        if ($timeLeft <= 0) {
            $session->update([
                'status' => 'completed',
                'finished_at' => now()
            ]);

            return redirect()->route('user.tryout.finish', $tryout)
                ->with('warning', 'Waktu tryout telah habis.');
        }

        return view('user.tryout.work', compact(
            'tryout',
            'userSoals',
            'currentQuestion',
            'totalQuestions',
            'timeLeft',
            'session'
        ));
    }

    public function resetAnswer(Request $request, Tryout $tryout)
    {
        $request->validate([
            'soal_id' => 'required|exists:soals,id',
        ]);

        $userSoal = UserTryoutSoal::where('user_id', auth()->id())
            ->where('tryout_id', $tryout->id)
            ->where('soal_id', $request->soal_id)
            ->first();

        if ($userSoal) {
            $userSoal->update([
                'jawaban_user' => null,
                'sudah_dijawab' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil direset'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Soal tidak ditemukan'
        ], 404);
    }

    public function submitAnswer(Request $request, Tryout $tryout)
    {
        $request->validate([
            'soal_id' => 'required|exists:soals,id',
            'jawaban' => 'required'
        ]);

        $user = auth()->user();

        // Check if session is still active
        $session = UserTryoutSession::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak aktif'
            ]);
        }

        // Check if time is still available
        $startTime = Carbon::parse($session->started_at);
        $elapsedMinutes = $startTime->diffInMinutes(now());
        if ($elapsedMinutes >= $tryout->durasi_menit) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu tryout telah habis'
            ]);
        }

        $userSoal = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('soal_id', $request->soal_id)
            ->first();

        if (!$userSoal) {
            return response()->json(['success' => false, 'message' => 'Soal tidak ditemukan']);
        }

        // Handle different input types
        $jawabanArray = $request->jawaban;
        if (is_string($jawabanArray)) {
            $decoded = json_decode($jawabanArray, true);
            $jawabanArray = $decoded ?: [$jawabanArray];
        }
        if (!is_array($jawabanArray)) {
            $jawabanArray = [$jawabanArray];
        }

        // PERUBAHAN: Convert shuffled answer back to original using session seed
        $originalJawaban = $this->convertShuffledAnswerToOriginal($jawabanArray, $userSoal, $session->shuffle_seed);

        $soal = $userSoal->soal;
        $skor = $this->calculateScore($soal, $originalJawaban);

        $userSoal->update([
            'jawaban_user' => $jawabanArray, // Simpan jawaban yang sudah diacak untuk display
            'jawaban_original' => $originalJawaban, // Simpan jawaban asli untuk scoring
            'skor' => $skor,
            'sudah_dijawab' => true,
            'answered_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'skor' => $skor
        ]);
    }


    // Metode ini tetap sama
    private function createOptionMapping($originalOptions, $shuffledOptions)
    {
        $mapping = [];
        $letters = ['A', 'B', 'C', 'D', 'E'];

        foreach ($shuffledOptions as $index => $shuffledOption) {
            // Cari posisi asli dari opsi ini
            $originalIndex = array_search($shuffledOption['opsi'], array_column($originalOptions, 'opsi'));
            $originalLetter = $letters[$originalIndex];
            $newLetter = $letters[$index];

            $mapping[$newLetter] = $originalLetter;
        }

        return $mapping;
    }

    // PERUBAHAN: Convert jawaban yang diacak kembali ke jawaban asli dengan session seed
    private function convertShuffledAnswerToOriginal($shuffledAnswers, $userSoal, $sessionSeed)
    {
        // Skip untuk soal benar/salah
        if ($userSoal->soal->tipe == 'benar_salah') {
            return $shuffledAnswers;
        }

        // PENTING: Gunakan logic yang SAMA dengan getConsistentShuffledOptions
        $seed = intval(crc32($sessionSeed . '_' . $userSoal->soal_id));

        if ($seed < 0) {
            $seed = abs($seed);
        }

        mt_srand($seed);

        $originalOptions = $userSoal->soal->opsi->toArray();
        $shuffledOptions = $originalOptions;
        shuffle($shuffledOptions);

        $mapping = $this->createOptionMapping($originalOptions, $shuffledOptions);

        // Convert shuffled answers to original
        $originalAnswers = [];
        foreach ($shuffledAnswers as $shuffledAnswer) {
            if (isset($mapping[$shuffledAnswer])) {
                $originalAnswers[] = $mapping[$shuffledAnswer];
            } else {
                $originalAnswers[] = $shuffledAnswer; // fallback
            }
        }

        return $originalAnswers;
    }

    public function debugSessionSeed(Tryout $tryout)
    {
        $user = auth()->user();

        $session = UserTryoutSession::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $userAnswers = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->with('soal')
            ->limit(3)
            ->get();

        $debugInfo = [];

        foreach ($userAnswers as $userAnswer) {
            $sessionSeedFromSession = $session ? $session->shuffle_seed : 'N/A';
            $sessionSeedFromAnswer = $userAnswer->session_seed ?? 'N/A';

            // Test shuffling dengan kedua seed
            $shuffleResult1 = $this->getConsistentShuffledOptions($userAnswer->soal, $sessionSeedFromSession);
            $shuffleResult2 = $this->getConsistentShuffledOptions($userAnswer->soal, $sessionSeedFromAnswer);

            $debugInfo[] = [
                'soal_id' => $userAnswer->soal_id,
                'session_seed_from_session' => $sessionSeedFromSession,
                'session_seed_from_answer' => $sessionSeedFromAnswer,
                'shuffle_same' => $shuffleResult1['options']->pluck('teks')->toArray() === $shuffleResult2['options']->pluck('teks')->toArray(),
                'options_session' => $shuffleResult1['options']->pluck('teks')->toArray(),
                'options_answer' => $shuffleResult2['options']->pluck('teks')->toArray(),
            ];
        }

        return response()->json($debugInfo);
    }

    // PERUBAHAN: Get shuffled options untuk display dengan session seed
    public function getShuffledOptions($soal, $sessionSeed)
    {
        if ($soal->tipe == 'benar_salah') {
            return $soal->opsi;
        }

        // PERUBAHAN: Generate seed yang konsisten dengan session seed
        $seed = crc32($sessionSeed . '_' . $soal->id);
        mt_srand($seed);

        // Shuffle options
        $options = $soal->opsi->toArray();
        shuffle($options);

        return collect($options);
    }

    private function getConsistentShuffledOptions($soal, $sessionSeed)
    {
        // Skip untuk soal benar/salah
        if ($soal->tipe == 'benar_salah') {
            return [
                'options' => $soal->opsi,
                'mapping' => []
            ];
        }

        // Generate seed yang konsisten
        $seed = crc32($sessionSeed . '_' . $soal->id);
        mt_srand($seed);

        // Get original options
        $originalOptions = $soal->opsi->toArray();

        // Shuffle dengan seed yang sama
        $shuffledOptions = $originalOptions;
        shuffle($shuffledOptions);

        // Create mapping
        $letters = ['A', 'B', 'C', 'D', 'E'];
        $shuffleToOriginalMapping = [];
        $originalToShuffleMapping = [];

        foreach ($shuffledOptions as $shuffleIndex => $shuffledOption) {
            // Cari index asli dari opsi ini
            foreach ($originalOptions as $origIndex => $origOption) {
                $shuffledText = is_array($shuffledOption) ? $shuffledOption['teks'] : $shuffledOption->teks;
                $originalText = is_array($origOption) ? $origOption['teks'] : $origOption->teks;

                if ($shuffledText === $originalText) {
                    $originalLetter = $letters[$origIndex];
                    $shuffledLetter = $letters[$shuffleIndex];

                    $shuffleToOriginalMapping[$shuffledLetter] = $originalLetter;
                    $originalToShuffleMapping[$originalLetter] = $shuffledLetter;
                    break;
                }
            }
        }

        return [
            'options' => collect($shuffledOptions),
            'shuffleToOriginal' => $shuffleToOriginalMapping,
            'originalToShuffle' => $originalToShuffleMapping
        ];
    }

    public function getQuestionReviewData($userAnswer)
    {
        $sessionSeed = $userAnswer->session_seed ?? auth()->id();

        // Get consistent shuffled options
        $shuffleData = $this->getConsistentShuffledOptions($userAnswer->soal, $sessionSeed);

        // Convert jawaban benar original ke shuffled
        $correctAnswerOriginal = $userAnswer->soal->jawaban_benar;
        if (!is_array($correctAnswerOriginal)) {
            $correctAnswerOriginal = [$correctAnswerOriginal];
        }

        $correctAnswerShuffled = $this->convertOriginalAnswerToShuffled(
            $correctAnswerOriginal,
            $shuffleData['originalToShuffle']
        );

        // Jawaban user sudah dalam format shuffled (dari jawaban_user)
        $userAnswerShuffled = $userAnswer->jawaban_user ?? [];
        if (!is_array($userAnswerShuffled)) {
            $userAnswerShuffled = [$userAnswerShuffled];
        }

        return [
            'shuffledOptions' => $shuffleData['options'],
            'correctAnswerShuffled' => $correctAnswerShuffled,
            'userAnswerShuffled' => $userAnswerShuffled,
            'mapping' => $shuffleData
        ];
    }

    public function finish(Tryout $tryout)
    {
        $user = auth()->user();

        // Update session status
        $session = UserTryoutSession::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('status', 'active')
            ->first();

        if ($session) {
            $session->update([
                'status' => 'completed',
                'finished_at' => now()
            ]);
        }

        $userAnswers = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->with(['soal.opsi', 'soal.kategori'])
            ->orderBy('urutan')
            ->get();

        if ($userAnswers->isEmpty()) {
            return redirect()->route('user.tryout.index')->with('error', 'Data tryout tidak ditemukan');
        }

        // PENTING: Pastikan semua userAnswer menggunakan session seed yang sama
        $sessionSeed = $session ? $session->shuffle_seed : $user->id;

        // Update semua user answers dengan session seed yang konsisten
        UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->whereNull('session_seed')
            ->update(['session_seed' => $sessionSeed]);

        // Refresh data setelah update
        $userAnswers = $userAnswers->fresh();

        // Generate review data dengan session seed yang konsisten
        $reviewData = [];
        foreach ($userAnswers as $userAnswer) {
            $reviewData[$userAnswer->id] = $this->getQuestionReviewData($userAnswer);
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
            'reviewData', // TAMBAHAN: Pass review data ke view
            'totalScore',
            'totalQuestions',
            'correctAnswers',
            'wrongAnswers',
            'categoryScores'
        ));
    }

    // PERUBAHAN: Helper method untuk generate shuffled options dengan session seed
    public function getShuffledOptionsForReview($soal, $sessionSeed)
    {
        if ($soal->tipe == 'benar_salah') {
            return $soal->opsi;
        }

        // Generate seed yang konsisten dengan session
        $seed = crc32($sessionSeed . '_' . $soal->id);
        mt_srand($seed);

        // Shuffle options
        $options = $soal->opsi->toArray();
        shuffle($options);

        return collect($options);
    }

    public function restart(Tryout $tryout)
    {
        return $this->start($tryout, request()->merge(['restart' => true]));
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

    // PERUBAHAN: Generate questions dengan session seed
    private function generateQuestionsForUser($user, $tryout, $sessionSeed = null)
    {
        $urutan = 1;
        $totalGenerated = 0;

        // Jika tidak ada session seed, generate yang baru
        if (!$sessionSeed) {
            $sessionSeed = rand(1, 999999);
        }

        DB::beginTransaction();

        try {
            foreach ($tryout->struktur as $kategoriId => $jumlah) {
                if ($jumlah > 0) {
                    // Get available questions
                    $availableSoals = Soal::active()
                        ->byKategori($kategoriId)
                        ->count();

                    if ($availableSoals < $jumlah) {
                        throw new \Exception("Kategori ID {$kategoriId} tidak memiliki cukup soal aktif. Tersedia: {$availableSoals}, Dibutuhkan: {$jumlah}");
                    }

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
                            'urutan' => $urutan++,
                            'session_seed' => $sessionSeed, // Simpan session seed untuk referensi
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $totalGenerated++;
                    }
                }
            }

            if ($totalGenerated == 0) {
                throw new \Exception('Tidak ada soal yang berhasil digenerate');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error generating questions: ' . $e->getMessage());
            throw $e;
        }

        return $totalGenerated;
    }


    // Tambahkan method ini ke TryoutController

    /**
     * Generate shuffled options yang konsisten untuk soal tertentu
     */


    /**
     * Convert jawaban original ke shuffled untuk display
     */
    private function convertOriginalAnswerToShuffled($originalAnswers, $mapping)
    {
        if (!is_array($originalAnswers)) {
            $originalAnswers = [$originalAnswers];
        }

        $shuffledAnswers = [];
        foreach ($originalAnswers as $original) {
            $shuffledAnswers[] = $mapping[$original] ?? $original;
        }

        return $shuffledAnswers;
    }

    /**
     * Get review data untuk satu soal
     */


    /**
     * Update method shuffleQuestionOptions untuk konsistensi
     */
    private function shuffleQuestionOptions($userSoal, $sessionSeed)
    {
        // Skip untuk soal benar/salah
        if ($userSoal->soal->tipe == 'benar_salah') {
            return $userSoal;
        }

        // Gunakan method yang konsisten dengan SEED YANG SAMA
        $shuffleData = $this->getConsistentShuffledOptions($userSoal->soal, $sessionSeed);

        // Update soal object dengan opsi yang sudah diacak
        $userSoal->soal->shuffled_opsi = $shuffleData['options'];

        // Store mapping untuk conversion nanti
        $userSoal->option_mapping = $shuffleData['shuffleToOriginal'];

        return $userSoal;
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
                return min($totalBobot, 1);

            case 'pg_pilih_2':
                if (count($jawaban) !== 2) return 0;

                $skor = 0;
                foreach ($jawaban as $opsi) {
                    $opsiSoal = $soal->opsi()->where('opsi', $opsi)->first();
                    if ($opsiSoal && $opsiSoal->bobot > 0) {
                        $skor += 0.5;
                    }
                }
                return $skor;

            default:
                return 0;
        }
    }
}

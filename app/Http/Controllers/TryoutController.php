<?php

namespace App\Http\Controllers;

use App\Models\Tryout;
use App\Models\KategoriSoal;
use App\Models\Soal;
use App\Models\UserTryoutSoal;
use App\Models\UserTryoutSession; // Tambahkan model ini untuk tracking session
use App\Models\TryoutBlueprint;
use App\Models\PackageCategoryMapping;
use App\Services\QuestionSelector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TryoutController extends Controller
{
    public function index()
    {
        $tryouts = Tryout::with('blueprints')->paginate(20);
        return view('admin.tryout.index', compact('tryouts'));
    }
    public function create()
    {
        $kategoris = KategoriSoal::active()->get();

        // Hanya ambil soal yang belum dipakai
        foreach ($kategoris as $kategori) {
            $kategori->available_soals = $kategori->soals()->where('is_used', false)->get();
        }

        $packageMappings = PackageCategoryMapping::getAllMappings();
        return view('admin.tryout.create', compact('kategoris', 'packageMappings'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'durasi_menit' => 'required|integer|min:1',
            'jenis_paket' => 'required|in:free,kecerdasan,kepribadian,lengkap',
            'blueprint' => 'required|array'
        ]);

        $this->validateBlueprint($request->blueprint);

        $tryout = Tryout::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'struktur' => [],
            'shuffle_questions' => (bool) $request->get('shuffle_questions', false),
            'durasi_menit' => $request->durasi_menit,
            'akses_paket' => 'free',
            'jenis_paket' => $request->jenis_paket,
        ]);

        $rows = [];
        foreach ($request->blueprint as $kategoriId => $levels) {
            foreach (['mudah', 'sedang', 'sulit'] as $level) {
                $jumlah = intval($levels[$level] ?? 0);
                if ($jumlah > 0) {
                    $soals = Soal::where('kategori_id', $kategoriId)
                        ->where('level', $level)
                        ->where('is_used', false)
                        ->limit($jumlah)
                        ->get();

                    // Tandai soal sudah dipakai
                    foreach ($soals as $soal) {
                        $soal->update(['is_used' => true]);
                    }

                    // Insert hanya sekali per kategori-level
                    $rows[] = [
                        'tryout_id' => $tryout->id,
                        'kategori_id' => $kategoriId,
                        'level' => $level,
                        'jumlah' => $jumlah,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
        }

        if (!empty($rows)) {
            TryoutBlueprint::insert($rows);
        }

        return redirect()->route('admin.tryout.index')
            ->with('success', 'Tryout berhasil dibuat');
    }


    public function show(Tryout $tryout)
    {
        $tryout->load(['userTryoutSoal.soal.kategori', 'blueprints']);

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

        // Struktur soal dengan informasi kategori (fallback lama)
        $strukturSoal = [];
        $totalSoal = 0;

        if ($tryout->blueprints && $tryout->blueprints->count() > 0) {
            // Hitung total dari blueprint
            $grouped = $tryout->blueprints->groupBy('kategori_id');
            foreach ($grouped as $kategoriId => $rows) {
                $jumlah = $rows->sum('jumlah');
                if ($jumlah > 0) {
                    $kategori = KategoriSoal::find($kategoriId);
                    $soalTersedia = $kategori ? $kategori->soals()->count() : 0;
                    $strukturSoal[] = [
                        'kategori_id' => $kategoriId,
                        'kategori' => $kategori,
                        'jumlah' => $jumlah,
                        'soal_tersedia' => $soalTersedia,
                        'persentase' => 0
                    ];
                    $totalSoal += $jumlah;
                }
            }
            // Set persentase
            foreach ($strukturSoal as &$item) {
                $item['persentase'] = $totalSoal > 0 ? round(($item['jumlah'] / $totalSoal) * 100, 1) : 0;
            }
        } else {
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
        $packageMappings = PackageCategoryMapping::getAllMappings();
        return view('admin.tryout.edit', compact('tryout', 'kategoris', 'packageMappings'));
    }

    public function update(Request $request, Tryout $tryout)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'durasi_menit' => 'required|integer|min:1',
            'jenis_paket' => 'required|in:free,kecerdasan,kepribadian,lengkap',
            'blueprint' => 'required|array'
        ]);

        // Validasi jumlah soal tidak melebihi yang tersedia
        $this->validateBlueprint($request->blueprint);

        // Kembalikan soal lama ke status is_used = false
        $oldBlueprints = $tryout->blueprints;
        foreach ($oldBlueprints as $blueprint) {
            $soals = Soal::where('kategori_id', $blueprint->kategori_id)
                ->where('level', $blueprint->level)
                ->where('is_used', true)
                ->limit($blueprint->jumlah)
                ->get();

            foreach ($soals as $soal) {
                $soal->update(['is_used' => false]);
            }
        }

        $tryout->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'struktur' => [],
            'shuffle_questions' => (bool)$request->get('shuffle_questions', false),
            'durasi_menit' => $request->durasi_menit,
            'jenis_paket' => $request->jenis_paket
        ]);

        // Replace blueprints
        $tryout->blueprints()->delete();

        $rows = [];
        foreach ($request->blueprint as $kategoriId => $levels) {
            foreach (['mudah', 'sedang', 'sulit'] as $level) {
                $jumlah = intval($levels[$level] ?? 0);
                if ($jumlah > 0) {
                    // Ambil soal yang belum dipakai dan tandai sebagai dipakai
                    $soals = Soal::where('kategori_id', $kategoriId)
                        ->where('level', $level)
                        ->where('is_used', false)
                        ->limit($jumlah)
                        ->get();

                    // Tandai soal sudah dipakai
                    foreach ($soals as $soal) {
                        $soal->update(['is_used' => true]);
                    }

                    $rows[] = [
                        'tryout_id' => $tryout->id,
                        'kategori_id' => $kategoriId,
                        'level' => $level,
                        'jumlah' => $jumlah,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
        }

        if (!empty($rows)) {
            TryoutBlueprint::insert($rows);
        }

        return redirect()->route('admin.tryout.index')->with('success', 'Tryout berhasil diperbarui');
    }
    public function destroy(Tryout $tryout)
    {
        // Kembalikan soal ke status is_used = false sebelum menghapus tryout
        $blueprints = $tryout->blueprints;
        foreach ($blueprints as $blueprint) {
            $soals = Soal::where('kategori_id', $blueprint->kategori_id)
                ->where('level', $blueprint->level)
                ->where('is_used', true)
                ->limit($blueprint->jumlah)
                ->get();

            foreach ($soals as $soal) {
                $soal->update(['is_used' => false]);
            }
        }

        // Hapus tryout (akan otomatis menghapus blueprints jika ada cascade)
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
        if (!$user->canAccessTryout()) {
            return redirect()->route('user.profile', ['userId' => $user->id])
                ->with('subscriptionError', 'Anda tidak memiliki akses ke Tryout CBT.');
        }

        // Optional filter by tryout type (kecerdasan/kepribadian/lengkap/free)
        $type = $request->get('type');

        $query = Tryout::active()->with('blueprints')->forUserPackage($user->paket_akses);

        if (!empty($type)) {
            // Validate type is one of the known types
            $allowedTypes = ['free', 'kecerdasan', 'kepribadian', 'lengkap'];
            if (in_array($type, $allowedTypes, true)) {
                $query->byJenisPaket($type);
            }
        }

        // Get tryouts based on user package (and optional filter)
        $tryouts = $query->limit($user->getMaxTryouts())->get();

        return view('user.tryout.index', [
            'user' => $user,
            'tryouts' => $tryouts,
            'availableMenus' => $user->getAvailableMenus()
        ]);
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
            // Jangan hapus attempt sebelumnya. Cukup tandai session lama sebagai abandoned
            if ($existingSession) {
                $existingSession->update(['status' => 'abandoned']);
            }

            // Create session baru
            $newSession = UserTryoutSession::create([
                'user_id' => $user->id,
                'tryout_id' => $tryout->id,
                'started_at' => now(),
                'status' => 'active',
                'shuffle_seed' => rand(1, 999999)
            ]);

            // Generate questions untuk session baru
            $this->generateQuestionsForUser($user, $tryout, $newSession->shuffle_seed, $newSession->id);
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
        $kategoriFilterId = $request->get('kategori_id');

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
        $baseQuery = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('user_tryout_session_id', $session->id)
            ->with(['soal.opsi', 'soal.kategori']);

        // Build per-category counts on full set (before filter)
        $allForCount = (clone $baseQuery)->with('soal')->get();
        $categoryCounts = $allForCount->groupBy(function ($row) {
            return $row->soal->kategori_id;
        })->map->count();

        if (!empty($kategoriFilterId)) {
            $baseQuery->whereHas('soal', function ($q) use ($kategoriFilterId) {
                $q->where('kategori_id', $kategoriFilterId);
            });
        }

        $userSoals = $baseQuery->orderBy('urutan')->get();

        // If no questions found, generate them
        if ($userSoals->isEmpty()) {
            $this->generateQuestionsForUser($user, $tryout, $session->shuffle_seed, $session->id);

            $userSoals = UserTryoutSoal::where('user_id', $user->id)
                ->where('tryout_id', $tryout->id)
                ->where('user_tryout_session_id', $session->id)
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
            'session',
            'categoryCounts',
            'kategoriFilterId'
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
            ->where('user_tryout_session_id', $session->id)
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

        // Backend guard: enforce max selections for multi-correct (e.g., pg_pilih_2)
        $soalForValidation = $userSoal->soal;
        if ($soalForValidation && $soalForValidation->tipe === 'pg_pilih_2') {
            if (count($jawabanArray) > 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maksimal 2 jawaban boleh dipilih untuk soal ini.'
                ], 422);
            }
            if (count($jawabanArray) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harus memilih tepat 2 jawaban untuk soal ini.'
                ], 422);
            }
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

    public function toggleMark(Request $request, Tryout $tryout)
    {
        $request->validate([
            'soal_id' => 'required|exists:soals,id',
            'is_marked' => 'nullable|boolean'
        ]);

        $user = auth()->user();

        $userSoal = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('soal_id', $request->soal_id)
            ->first();

        if (!$userSoal) {
            return response()->json(['success' => false, 'message' => 'Soal tidak ditemukan'], 404);
        }

        $current = (bool)$userSoal->is_marked;
        $newState = $request->has('is_marked') ? (bool)$request->boolean('is_marked') : !$current;

        $userSoal->update(['is_marked' => $newState]);

        return response()->json([
            'success' => true,
            'is_marked' => $newState
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

        // Cari opsi terbaik dari shuffled (bobot tertinggi)
        $shuffledOptionsCollection = collect($shuffleData['options']);
        $bestShuffledOption = $shuffledOptionsCollection->sortByDesc(function ($opt) {
            return is_array($opt) ? $opt['bobot'] : $opt->bobot;
        })->first();

        // Cari letter untuk opsi terbaik sesuai shuffle
        $letters = ['A', 'B', 'C', 'D', 'E'];
        $bestIndex = null;

        // Loop manual untuk mencari index yang tepat
        foreach ($shuffledOptionsCollection->values() as $index => $option) {
            $shuffledText = is_array($option) ? $option['teks'] : $option->teks;
            $shuffledBobot = is_array($option) ? $option['bobot'] : $option->bobot;

            $bestText = is_array($bestShuffledOption) ? $bestShuffledOption['teks'] : $bestShuffledOption->teks;
            $bestBobot = is_array($bestShuffledOption) ? $bestShuffledOption['bobot'] : $bestShuffledOption->bobot;

            // Cocokkan berdasarkan teks DAN bobot untuk memastikan tepat
            if ($shuffledText === $bestText && $shuffledBobot == $bestBobot) {
                $bestIndex = $index;
                break;
            }
        }

        // Fallback jika tidak ditemukan
        if ($bestIndex === null) {
            $bestIndex = 0; // Default ke A jika ada masalah
        }

        $bestOptionLetter = $letters[$bestIndex] ?? 'A';
        $bestOptionBobot = is_array($bestShuffledOption) ? $bestShuffledOption['bobot'] : $bestShuffledOption->bobot;

        return [
            'shuffledOptions' => $shuffleData['options'],
            'correctAnswerShuffled' => $correctAnswerShuffled,
            'userAnswerShuffled' => $userAnswerShuffled,
            'mapping' => $shuffleData,
            'bestOption' => [
                'letter' => $bestOptionLetter,
                'bobot'  => $bestOptionBobot,
            ]
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

        // Jika session tidak ditemukan, cari session yang sudah completed
        if (!$session) {
            $session = UserTryoutSession::where('user_id', $user->id)
                ->where('tryout_id', $tryout->id)
                ->where('status', 'completed')
                ->orderBy('finished_at', 'desc')
                ->first();
        }

        if (!$session) {
            return redirect()->route('user.tryout.index')->with('error', 'Session tryout tidak ditemukan');
        }

        $userAnswers = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('user_tryout_session_id', $session->id)
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
            ->where('user_tryout_session_id', $session->id)
            ->whereNull('session_seed')
            ->update(['session_seed' => $sessionSeed]);

        // Backfill session_id untuk jawaban yang mungkin belum terset
        if ($session) {
            UserTryoutSoal::where('user_id', $user->id)
                ->where('tryout_id', $tryout->id)
                ->whereNull('user_tryout_session_id')
                ->update(['user_tryout_session_id' => $session->id]);
        }

        // Refresh data setelah update
        $userAnswers = $userAnswers->fresh();

        // Generate review data dengan session seed yang konsisten
        $reviewData = [];
        foreach ($userAnswers as $userAnswer) {
            $reviewData[$userAnswer->id] = $this->getQuestionReviewData($userAnswer);
        }

        $totalScore = $userAnswers->sum('skor');
        $totalQuestions = $userAnswers->count();

        // TKP scaling: treat empty as 0; for scaling clamp to min N
        $tkpFinalScore = null;
        $tkpN = null; // jumlah soal TKP
        $tkpT = null; // total poin TKP (raw sum 1..5)
        try {
            $kepribadianKategoriCodes = \App\Models\PackageCategoryMapping::getCategoriesForPackage('kepribadian');
            $tkpQuestions = $userAnswers->filter(function ($ans) use ($kepribadianKategoriCodes) {
                $kategori = $ans->soal->kategori ?? null;
                return $kategori && in_array($kategori->kode, $kepribadianKategoriCodes);
            });

            if ($tkpQuestions->count() > 0) {
                $N = $tkpQuestions->count();
                // Raw T sums selected option weights (already stored in skor per-question for kepribadian as 1..5)
                $T = (int) round($tkpQuestions->sum('skor'));

                $scorer = app(\App\Services\TkpScoringService::class);
                $tkpFinalScore = $scorer->calculateFinalScore($N, $T);

                // expose for view consumption
                $tkpN = $N;
                $tkpT = $T;

                // Persist to session for quick retrieval
                if ($session) {
                    $session->update(['tkp_final_score' => $tkpFinalScore]);
                }
            }
        } catch (\Throwable $e) {
            // Fail-safe: ignore TKP score calculation errors to avoid blocking result page
        }

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

        // Persist TKP score to hasil_tes as a summarized record (no breakdown)
        if (!is_null($tkpFinalScore)) {
            try {
                $durationMinutes = $tryout->durasi_menit;
                $durationSeconds = $durationMinutes * 60;
                $tkpCount = $tkpQuestions->count();
                $averageTime = $tkpCount > 0 ? round($durationSeconds / $tkpCount, 2) : null;

                \App\Models\HasilTes::create([
                    'user_id' => $user->id,
                    'jenis_tes' => 'kepribadian',
                    'skor_benar' => 0,
                    'skor_salah' => 0,
                    'waktu_total' => $durationSeconds,
                    'average_time' => $averageTime,
                    'detail_jawaban' => json_encode([
                        'N' => $tkpCount,
                        'T' => (int) round($tkpQuestions->sum('skor')),
                        'skor_tkp' => $tkpFinalScore,
                    ]),
                    'tkp_final_score' => $tkpFinalScore,
                    'tanggal_tes' => now(),
                ]);
            } catch (\Throwable $e) {
                // ignore persistence errors
            }
        }

        // Server-side per-question review support
        $requestedReview = request()->get('review');
        $currentReviewNumber = is_numeric($requestedReview) ? max(1, min((int)$requestedReview, $totalQuestions)) : 1;
        $currentReviewItem = $userAnswers->firstWhere('urutan', $currentReviewNumber) ?? $userAnswers->first();

        $isTkp = !is_null($tkpFinalScore);

        return view('user.tryout.result', compact(
            'tryout',
            'userAnswers',
            'reviewData',
            'totalScore',
            'totalQuestions',
            'correctAnswers',
            'wrongAnswers',
            'categoryScores',
            'currentReviewNumber',
            'currentReviewItem',
            'tkpFinalScore',
            'tkpN',
            'tkpT',
            'isTkp'
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
        // Use new package system
        return $user->canAccessSpecificTryout($tryout);
    }

    /**
     * Validate blueprint against available questions
     */
    private function validateBlueprint($blueprint)
    {
        $errors = [];
        $totalSoal = 0;
        $kategoriDenganSoal = 0;

        foreach ($blueprint as $kategoriId => $levels) {
            $kategori = KategoriSoal::find($kategoriId);
            if (!$kategori) continue;

            $totalSoalKategori = 0;
            $kategoriMemilikiSoal = false;

            foreach ($levels as $level => $jumlah) {
                $jumlah = (int) $jumlah;
                if ($jumlah <= 0) continue;

                $kategoriMemilikiSoal = true;
                $totalSoalKategori += $jumlah;
                $totalSoal += $jumlah;

                $available = $kategori->soals()->where('level', $level)->where('is_used', false)->count();

                if ($jumlah > $available) {
                    $levelText = ucfirst($level);
                    $errors[] = "Kategori {$kategori->nama} ({$kategori->kode}): Jumlah soal {$levelText} yang diminta ({$jumlah}) melebihi soal yang tersedia ({$available})";
                }
            }

            // Validasi: setiap kategori yang dipilih harus memiliki minimal 1 soal
            if ($totalSoalKategori > 0) {
                $kategoriDenganSoal++;
            }
        }

        // Validasi: minimal 1 soal total di seluruh tryout
        if ($totalSoal == 0) {
            $errors[] = "Tryout harus memiliki minimal 1 soal dari kategori manapun.";
        }

        // Validasi: minimal 1 kategori harus dipilih
        if ($kategoriDenganSoal == 0) {
            $errors[] = "Minimal 1 kategori harus dipilih dengan minimal 1 soal.";
        }

        if (!empty($errors)) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['blueprint' => $errors]
            );
        }
    }

    // PERUBAHAN: Generate questions dengan session seed
    private function generateQuestionsForUser($user, $tryout, $sessionSeed = null, $sessionId = null)
    {
        $urutan = 1;
        $totalGenerated = 0;

        // Jika tidak ada session seed, generate yang baru
        if (!$sessionSeed) {
            $sessionSeed = rand(1, 999999);
        }

        DB::beginTransaction();

        try {
            // 1) Cari baseline: session pertama user untuk tryout ini yang punya set soal
            $baselineSession = UserTryoutSession::where('user_id', $user->id)
                ->where('tryout_id', $tryout->id)
                ->orderBy('id', 'asc')
                ->first();

            $baselineSet = collect();
            if ($baselineSession) {
                $baselineSet = UserTryoutSoal::where('user_id', $user->id)
                    ->where('tryout_id', $tryout->id)
                    ->where('user_tryout_session_id', $baselineSession->id)
                    ->orderBy('urutan')
                    ->get(['soal_id', 'level']);
            }

            // 2) Jika baseline kosong (belum pernah attempt), pilih set soal sesuai blueprint/struktur sebagai baseline
            if ($baselineSet->isEmpty()) {
                if ($tryout->relationLoaded('blueprints') || $tryout->blueprints()->exists()) {
                    $tryout->load('blueprints');
                    $selector = new QuestionSelector();
                    $selector->validateBlueprintAvailability($tryout);
                    $picked = $selector->pickByBlueprint($tryout);
                    $baselineSet = $picked->map(function ($soal) {
                        return (object)['soal_id' => $soal->id, 'level' => $soal->level];
                    });
                } else {
                    // Fallback: gunakan struktur lama per kategori
                    $temp = collect();
                    foreach ($tryout->struktur as $kategoriId => $jumlah) {
                        if ($jumlah > 0) {
                            $availableSoals = Soal::active()->byKategori($kategoriId)->count();
                            if ($availableSoals < $jumlah) {
                                throw new \Exception("Kategori ID {$kategoriId} tidak memiliki cukup soal aktif. Tersedia: {$availableSoals}, Dibutuhkan: {$jumlah}");
                            }
                            $soals = Soal::active()->byKategori($kategoriId)->inRandomOrder()->limit($jumlah)->get();
                            $soals->each(function ($s) use (&$temp) {
                                $temp->push((object)['soal_id' => $s->id, 'level' => $s->level ?? null]);
                            });
                        }
                    }
                    $baselineSet = $temp;
                }

                // Jika ini adalah attempt pertama (sessionId == baselineSession? tidak ada baselineSession), simpan baseline sesuai urutan sekarang
                // Insert langsung dengan urutan (mungkin di-shuffle tergantung setting)
                $ordered = $baselineSet;
            } else {
                // 3) Untuk attempt selanjutnya: gunakan set soal baseline yang sama
                $ordered = $baselineSet;
            }

            // 4) Acak urutan saja per attempt, deterministik dengan session seed
            $ordered = $ordered->values();
            $indices = range(0, $ordered->count() - 1);
            if ($ordered->count() > 0) {
                $seed = crc32($sessionSeed . '_' . $tryout->id . '_' . $user->id . '_order');
                mt_srand($seed);
                shuffle($indices);
            }

            foreach ($indices as $idx) {
                $row = $ordered[$idx];
                UserTryoutSoal::create([
                    'user_id' => $user->id,
                    'tryout_id' => $tryout->id,
                    'user_tryout_session_id' => $sessionId,
                    'soal_id' => $row->soal_id,
                    'level' => $row->level,
                    'urutan' => $urutan++,
                    'session_seed' => $sessionSeed,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $totalGenerated++;
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
        // Check if this is a kepribadian category (TKP)
        $kepribadianKategoriCodes = \App\Models\PackageCategoryMapping::getCategoriesForPackage('kepribadian');
        $isKepribadian = $soal->kategori && in_array($soal->kategori->kode, $kepribadianKategoriCodes);

        switch ($soal->tipe) {
            case 'benar_salah':
                return $jawaban[0] === $soal->jawaban_benar ? 1 : 0;

            case 'pg_satu':
                return $jawaban[0] === $soal->jawaban_benar ? 1 : 0;

            case 'gambar':
                return $jawaban[0] === $soal->jawaban_benar ? 1 : 0;

            case 'pg_bobot':
                $totalBobot = 0;
                foreach ($jawaban as $opsi) {
                    $opsiSoal = $soal->opsi()->where('opsi', $opsi)->first();
                    if ($opsiSoal) {
                        $totalBobot += $opsiSoal->bobot;
                    }
                }

                // For kepribadian categories, return the full bobot (1-5)
                // For non-kepribadian categories, cap at 1 (0-1 scale)
                if ($isKepribadian) {
                    return $totalBobot; // Can be 1-5
                } else {
                    return min($totalBobot, 1); // Cap at 1 for 0-1 scale
                }

            case 'pg_pilih_2':
                if (count($jawaban) !== 2) return 0;

                // Untuk pg_pilih_2, kedua jawaban HARUS benar untuk mendapat skor 1
                // Jika hanya 1 benar atau 0 benar, skor = 0
                $correctCount = 0;
                foreach ($jawaban as $opsi) {
                    $opsiSoal = $soal->opsi()->where('opsi', $opsi)->first();
                    if ($opsiSoal && $opsiSoal->bobot > 0) {
                        $correctCount++;
                    }
                }

                // Hanya berikan skor 1 jika KEDUA jawaban benar (untuk semua kategori)
                return $correctCount === 2 ? 1 : 0;

            default:
                return 0;
        }
    }

    /**
     * Toggle status aktif/nonaktif tryout
     */
    public function toggleStatus(Request $request, Tryout $tryout)
    {
        try {
            // Debug logging
            \Log::info('Toggle Status Request', [
                'tryout_id' => $tryout->id,
                'tryout_title' => $tryout->judul,
                'request_data' => $request->all(),
                'current_status' => $tryout->is_active
            ]);

            $request->validate([
                'is_active' => 'required'
            ]);

            $isActive = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            
            if ($isActive === null) {
                \Log::error('Invalid is_active value', ['value' => $request->is_active]);
                return response()->json([
                    'success' => false,
                    'message' => 'Nilai is_active tidak valid'
                ], 422);
            }

            $tryout->update([
                'is_active' => $isActive
            ]);

            $status = $tryout->is_active ? 'aktif' : 'nonaktif';
            
            \Log::info('Toggle Status Success', [
                'tryout_id' => $tryout->id,
                'new_status' => $tryout->is_active
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Tryout berhasil di{$status}kan",
                'is_active' => $tryout->is_active
            ]);
        } catch (\Exception $e) {
            \Log::error('Toggle Status Error', [
                'tryout_id' => $tryout->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

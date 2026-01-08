<?php

namespace App\Services;

use App\Models\User;
use App\Models\HasilTes;
use App\Models\UserTryoutSession;
use App\Models\PackageCategoryMapping;
use App\Models\ScoringSetting;
use App\Services\ScoringService;
use Illuminate\Support\Collection;

class PaketLengkapService
{
    /**
     * Get completion status for paket lengkap user
     */
    public function getCompletionStatus(User $user): array
    {
        if ($user->paket_akses !== 'lengkap') {
            return [
                'is_eligible' => false,
                'message' => 'User tidak memiliki paket lengkap'
            ];
        }

        // OPTIMASI: Cache hasil untuk user ini (cache 10 detik untuk hasil instant)
        return cache()->remember("paket_lengkap_status_{$user->id}", 10, function () use ($user) {
            try {
                // OPTIMASI: Load semua data dalam 1 query besar
                $allData = $this->getAllCompletionDataInOneQuery($user);
                
                $kecermatanStatus = $allData['kecermatan'] ?? [];
                $kecerdasanStatus = $allData['kecerdasan'] ?? [];
                $kepribadianStatus = $allData['kepribadian'] ?? [];

                // Kecermatan wajib + minimal 1 tryout CBT yang berisi kategori yang dibutuhkan
                $isComplete = isset($kecermatanStatus['completed']) && $kecermatanStatus['completed'] && 
                             (isset($kecerdasanStatus['completed']) && $kecerdasanStatus['completed'] || 
                              isset($kepribadianStatus['completed']) && $kepribadianStatus['completed']);

                return [
                    'is_eligible' => true,
                    'is_complete' => $isComplete,
                    'kecermatan' => $kecermatanStatus,
                    'kecerdasan' => $kecerdasanStatus,
                    'kepribadian' => $kepribadianStatus,
                    'final_score' => $isComplete ? $this->calculateFinalScoreFromData($allData) : null,
                    'scoring_info' => $isComplete ? $this->getScoringInfo($allData) : null
                ];
            } catch (\Throwable $e) {
                \Log::error('Error in getCompletionStatus cache callback', [
                    'error' => $e->getMessage(),
                    'user_id' => $user->id,
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'timestamp' => now()->toDateTimeString(),
                ]);
                // Return safe fallback
                return [
                    'is_eligible' => true,
                    'is_complete' => false,
                    'kecermatan' => ['completed' => false, 'message' => 'Error loading data'],
                    'kecerdasan' => ['completed' => false, 'message' => 'Error loading data'],
                    'kepribadian' => ['completed' => false, 'message' => 'Error loading data'],
                    'final_score' => null,
                    'scoring_info' => null
                ];
            }
        });
    }

    /**
     * Get kecermatan completion status
     */
    private function getKecermatanStatus(User $user): array
    {
        $kecermatanResult = HasilTes::where('user_id', $user->id)
            ->where('jenis_tes', 'kecermatan')
            ->orderBy('tanggal_tes', 'desc')
            ->first();

        if (!$kecermatanResult) {
            return [
                'completed' => false,
                'score' => null,
                'message' => 'Belum mengerjakan tes kecermatan'
            ];
        }

        // Ambil skor akhir langsung dari kolom skor_akhir
        $skorAkhir = $kecermatanResult->skor_akhir;

        return [
            'completed' => true,
            'score' => $skorAkhir,
            'tanggal' => $kecermatanResult->tanggal_tes,
            'message' => 'Tes kecermatan sudah selesai'
        ];
    }

    /**
     * Get kecerdasan completion status
     */
    private function getKecerdasanStatus(User $user): array
    {
        $kecerdasanKategoriCodes = PackageCategoryMapping::getCategoriesForPackage('kecerdasan');
        
        if (empty($kecerdasanKategoriCodes)) {
            return [
                'completed' => false,
                'score' => null,
                'message' => 'Tidak ada kategori kecerdasan yang dikonfigurasi'
            ];
        }

        // Find completed tryouts that contain kecerdasan categories
        $completedTryouts = UserTryoutSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('tryout.blueprints.kategori', function ($query) use ($kecerdasanKategoriCodes) {
                $query->whereIn('kode', $kecerdasanKategoriCodes);
            })
            ->with(['tryout.blueprints.kategori'])
            ->orderBy('finished_at', 'desc')
            ->get();

        if ($completedTryouts->isEmpty()) {
            return [
                'completed' => false,
                'score' => null,
                'message' => 'Belum menyelesaikan tryout yang berisi kategori kecerdasan'
            ];
        }

        // Calculate total score from all kecerdasan categories across all completed tryouts
        $totalScore = 0;
        $totalQuestions = 0;
        $tryoutTitles = [];

        foreach ($completedTryouts as $session) {
            $tryoutTitles[] = $session->tryout->judul;
            
            // Get user answers for this tryout session
            $userAnswers = \App\Models\UserTryoutSoal::where('user_id', $user->id)
                ->where('tryout_id', $session->tryout_id)
                ->where('user_tryout_session_id', $session->id)
                ->with(['soal.kategori'])
                ->get();

            // Filter answers that belong to kecerdasan categories
            $kecerdasanAnswers = $userAnswers->filter(function ($answer) use ($kecerdasanKategoriCodes) {
                $kategori = $answer->soal->kategori ?? null;
                return $kategori && in_array($kategori->kode, $kecerdasanKategoriCodes);
            });

            $totalScore += $kecerdasanAnswers->sum('skor');
            $totalQuestions += $kecerdasanAnswers->count();
        }

        if ($totalQuestions === 0) {
            return [
                'completed' => false,
                'score' => null,
                'message' => 'Tidak ada soal kecerdasan yang ditemukan'
            ];
        }

        // Convert to percentage (0-100)
        $score = round(($totalScore / $totalQuestions) * 100, 2);

        return [
            'completed' => true,
            'score' => $score,
            'tryout_title' => implode(', ', array_unique($tryoutTitles)),
            'tanggal' => $completedTryouts->first()->finished_at,
            'message' => 'Tryout kecerdasan sudah selesai'
        ];
    }

    /**
     * Get kepribadian completion status
     */
    private function getKepribadianStatus(User $user): array
    {
        $kepribadianKategoriCodes = PackageCategoryMapping::getCategoriesForPackage('kepribadian');
        
        if (empty($kepribadianKategoriCodes)) {
            return [
                'completed' => false,
                'score' => null,
                'message' => 'Tidak ada kategori kepribadian yang dikonfigurasi'
            ];
        }

        // Find completed tryouts that contain kepribadian categories
        $completedTryouts = UserTryoutSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('tryout.blueprints.kategori', function ($query) use ($kepribadianKategoriCodes) {
                $query->whereIn('kode', $kepribadianKategoriCodes);
            })
            ->with(['tryout.blueprints.kategori'])
            ->orderBy('finished_at', 'desc')
            ->get();

        if ($completedTryouts->isEmpty()) {
            return [
                'completed' => false,
                'score' => null,
                'message' => 'Belum menyelesaikan tryout yang berisi kategori kepribadian'
            ];
        }

        // Calculate total score from all kepribadian categories across all completed tryouts
        $totalScore = 0;
        $totalQuestions = 0;
        $tryoutTitles = [];

        foreach ($completedTryouts as $session) {
            $tryoutTitles[] = $session->tryout->judul;
            
            // Get user answers for this tryout session
            $userAnswers = \App\Models\UserTryoutSoal::where('user_id', $user->id)
                ->where('tryout_id', $session->tryout_id)
                ->where('user_tryout_session_id', $session->id)
                ->with(['soal.kategori'])
                ->get();

            // Filter answers that belong to kepribadian categories
            $kepribadianAnswers = $userAnswers->filter(function ($answer) use ($kepribadianKategoriCodes) {
                $kategori = $answer->soal->kategori ?? null;
                return $kategori && in_array($kategori->kode, $kepribadianKategoriCodes);
            });

            $totalScore += $kepribadianAnswers->sum('skor');
            $totalQuestions += $kepribadianAnswers->count();
        }

        if ($totalQuestions === 0) {
            return [
                'completed' => false,
                'score' => null,
                'message' => 'Tidak ada soal kepribadian yang ditemukan'
            ];
        }

        // For kepribadian, calculate TKP final score using the service
        $scorer = app(\App\Services\TkpScoringService::class);
        $tkpFinalScore = $scorer->calculateFinalScore($totalQuestions, $totalScore);

        return [
            'completed' => true,
            'score' => $tkpFinalScore,
            'tryout_title' => implode(', ', array_unique($tryoutTitles)),
            'tanggal' => $completedTryouts->first()->finished_at,
            'message' => 'Tryout kepribadian sudah selesai'
        ];
    }

    /**
     * Calculate final score for paket lengkap
     */
    public function calculateFinalScore(User $user): ?float
    {
        $status = $this->getCompletionStatus($user);
        
        if (!$status['is_complete']) {
            return null;
        }

        $kecermatanScore = $status['kecermatan']['score'];
        $kecerdasanScore = $status['kecerdasan']['score'];
        $kepribadianScore = $status['kepribadian']['score'];

        // Kecermatan wajib
        if (!is_numeric($kecermatanScore)) {
            return null;
        }

        $scores = [$kecermatanScore];
        $count = 1;

        // Tambahkan skor kecerdasan jika ada
        if (is_numeric($kecerdasanScore)) {
            $scores[] = $kecerdasanScore;
            $count++;
        }

        // Tambahkan skor kepribadian jika ada
        if (is_numeric($kepribadianScore)) {
            $scores[] = $kepribadianScore;
            $count++;
        }

        // Calculate average of available scores
        $finalScore = array_sum($scores) / $count;
        
        return round($finalScore, 2);
    }


    /**
     * Get progress percentage for paket lengkap
     */
    public function getProgressPercentage(User $user): int
    {
        if ($user->paket_akses !== 'lengkap') {
            return 0;
        }

        // OPTIMASI: Cache progress percentage (cache 10 detik untuk hasil instant)
        return cache()->remember("paket_lengkap_progress_{$user->id}", 10, function () use ($user) {
            $status = $this->getCompletionStatus($user);
            $completedCount = 0;

            // Kecermatan wajib (1 poin)
            if ($status['kecermatan']['completed']) $completedCount++;
            
            // Tryout CBT (1 poin jika ada kecerdasan atau kepribadian)
            if ($status['kecerdasan']['completed'] || $status['kepribadian']['completed']) {
                $completedCount++;
            }

            // Total maksimal 2 poin (kecermatan + tryout CBT)
            return round(($completedCount / 2) * 100);
        });
    }

    /**
     * Get summary for paket lengkap dashboard
     */
    public function getDashboardSummary(User $user): array
    {
        try {
            $status = $this->getCompletionStatus($user);
            
            if (!isset($status['is_eligible']) || !$status['is_eligible']) {
                return [
                    'title' => 'Paket Lengkap',
                    'progress' => 0,
                    'status' => 'not_eligible',
                    'message' => $status['message'] ?? 'Anda tidak memiliki paket lengkap'
                ];
            }

            $progress = $this->getProgressPercentage($user);
            
            if (isset($status['is_complete']) && $status['is_complete']) {
                $scoringInfo = $status['scoring_info'] ?? null;
                return [
                    'title' => 'Paket Lengkap',
                    'progress' => 100,
                    'status' => 'completed',
                    'message' => 'Paket lengkap sudah selesai!',
                    'final_score' => $scoringInfo['final_score'] ?? null,
                    'passed' => $scoringInfo['passed'] ?? null,
                    'passing_grade' => $scoringInfo['passing_grade'] ?? null,
                    'details' => [
                        'kecermatan' => $status['kecermatan'] ?? [],
                        'kecerdasan' => $status['kecerdasan'] ?? [],
                        'kepribadian' => $status['kepribadian'] ?? []
                    ]
                ];
            }

            $remainingTasks = [];
            $kecermatan = $status['kecermatan'] ?? [];
            $kecerdasan = $status['kecerdasan'] ?? [];
            $kepribadian = $status['kepribadian'] ?? [];
            
            if (!isset($kecermatan['completed']) || !$kecermatan['completed']) {
                $remainingTasks[] = 'Tes Kecermatan';
            }
            if ((!isset($kecerdasan['completed']) || !$kecerdasan['completed']) && 
                (!isset($kepribadian['completed']) || !$kepribadian['completed'])) {
                $remainingTasks[] = 'Tryout CBT (Kecerdasan/Kepribadian)';
            }

            return [
                'title' => 'Paket Lengkap',
                'progress' => $progress,
                'status' => 'in_progress',
                'message' => count($remainingTasks) > 0 ? 'Selesaikan: ' . implode(', ', $remainingTasks) : 'Sedang dalam proses...',
                'details' => [
                    'kecermatan' => $kecermatan,
                    'kecerdasan' => $kecerdasan,
                    'kepribadian' => $kepribadian
                ]
            ];
        } catch (\Throwable $e) {
            \Log::error('Error in getDashboardSummary', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'user_package' => $user->package,
                'user_paket_akses' => $user->paket_akses,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            // Return safe fallback response
            return [
                'title' => 'Paket Lengkap',
                'progress' => 0,
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memuat data paket lengkap'
            ];
        }
    }

    /**
     * Clear cache for a specific user
     */
    public function clearUserCache(User $user): void
    {
        cache()->forget("paket_lengkap_status_{$user->id}");
        cache()->forget("paket_lengkap_progress_{$user->id}");
    }

    /**
     * Clear all paket lengkap caches
     */
    public function clearAllCache(): void
    {
        // Note: This is a simple approach. For production, consider using cache tags
        $users = User::where('package', 'lengkap')->get();
        foreach ($users as $user) {
            $this->clearUserCache($user);
        }
    }

    /**
     * OPTIMASI: Load semua data completion dalam 1 query besar
     */
    private function getAllCompletionDataInOneQuery(User $user): array
    {
        try {
            // 1. Ambil data kecermatan (query terpisah karena tabel berbeda)
            $kecermatanResult = HasilTes::where('user_id', $user->id)
                ->where('jenis_tes', 'kecermatan')
                ->orderBy('tanggal_tes', 'desc')
                ->first();

            $kecermatanStatus = [
                'completed' => $kecermatanResult ? true : false,
                'score' => $kecermatanResult ? $kecermatanResult->skor_akhir : null,
                'tanggal' => $kecermatanResult ? $kecermatanResult->tanggal_tes : null,
                'message' => $kecermatanResult ? 'Tes kecermatan sudah selesai' : 'Belum mengerjakan tes kecermatan'
            ];

            // 2. Ambil kategori codes untuk kecerdasan dan kepribadian
            $kecerdasanKategoriCodes = PackageCategoryMapping::getCategoriesForPackage('kecerdasan');
            $kepribadianKategoriCodes = PackageCategoryMapping::getCategoriesForPackage('kepribadian');

            // 3. OPTIMASI: Single query untuk semua tryout sessions dengan eager loading
            $completedTryouts = UserTryoutSession::where('user_id', $user->id)
                ->where('status', 'completed')
                ->with([
                    'tryout:id,judul',
                    'tryout.blueprints.kategori:id,kode'
                ])
                ->orderBy('finished_at', 'desc')
                ->get();

            // 4. OPTIMASI: Single query untuk semua user answers dengan eager loading
            $allUserAnswers = \App\Models\UserTryoutSoal::where('user_id', $user->id)
                ->whereIn('user_tryout_session_id', $completedTryouts->pluck('id'))
                ->with(['soal:id,kategori_id', 'soal.kategori:id,kode'])
                ->get();

            // 5. Group answers by session untuk efisiensi
            $answersBySession = $allUserAnswers->groupBy('user_tryout_session_id');

            // 6. Process kecerdasan status
            $kecerdasanStatus = $this->processKecerdasanStatus($completedTryouts, $answersBySession, $kecerdasanKategoriCodes, $user->id);

            // 7. Process kepribadian status  
            $kepribadianStatus = $this->processKepribadianStatus($completedTryouts, $answersBySession, $kepribadianKategoriCodes, $user->id);

            return [
                'kecermatan' => $kecermatanStatus,
                'kecerdasan' => $kecerdasanStatus,
                'kepribadian' => $kepribadianStatus
            ];
        } catch (\Throwable $e) {
            \Log::error('Error in getAllCompletionDataInOneQuery', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            // Return safe fallback
            return [
                'kecermatan' => [
                    'completed' => false,
                    'score' => null,
                    'tanggal' => null,
                    'message' => 'Error loading data'
                ],
                'kecerdasan' => [
                    'completed' => false,
                    'score' => null,
                    'message' => 'Error loading data'
                ],
                'kepribadian' => [
                    'completed' => false,
                    'score' => null,
                    'message' => 'Error loading data'
                ]
            ];
        }
    }

    /**
     * Process kecerdasan status dari data yang sudah di-load
     */
    private function processKecerdasanStatus($completedTryouts, $answersBySession, $kecerdasanKategoriCodes, $userId): array
    {
        // Ambil hasil tes kecerdasan terbaru dari tabel hasil_tes
        $kecerdasanResult = HasilTes::where('user_id', $userId)
            ->where('jenis_tes', 'kecerdasan')
            ->orderBy('tanggal_tes', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$kecerdasanResult) {
            return [
                'completed' => false,
                'score' => null,
                'message' => 'Belum mengerjakan tes kecerdasan'
            ];
        }

        // Get tryout title from the related tryout session
        $tryoutTitle = 'Tryout Kecerdasan';
        $kecerdasanSession = UserTryoutSession::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereHas('tryout.blueprints.kategori', function ($query) use ($kecerdasanKategoriCodes) {
                $query->whereIn('kode', $kecerdasanKategoriCodes);
            })
            ->with('tryout:id,judul')
            ->orderBy('finished_at', 'desc')
            ->first();
        
        if ($kecerdasanSession && $kecerdasanSession->tryout) {
            $tryoutTitle = $kecerdasanSession->tryout->judul;
        }

        return [
            'completed' => true,
            'score' => $kecerdasanResult->skor_akhir,
            'tryout_title' => $tryoutTitle,
            'tanggal' => $kecerdasanResult->tanggal_tes,
            'message' => 'Tes kecerdasan sudah selesai'
        ];
    }

    /**
     * Process kepribadian status dari data yang sudah di-load
     */
    private function processKepribadianStatus($completedTryouts, $answersBySession, $kepribadianKategoriCodes, $userId): array
    {
        // Ambil hasil tes kepribadian terbaru dari tabel hasil_tes
        $kepribadianResult = HasilTes::where('user_id', $userId)
            ->where('jenis_tes', 'kepribadian')
            ->orderBy('tanggal_tes', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$kepribadianResult) {
            return [
                'completed' => false,
                'score' => null,
                'message' => 'Belum mengerjakan tes kepribadian'
            ];
        }

        // Get tryout title from the related tryout session
        $tryoutTitle = 'Tryout Kepribadian';
        $kepribadianSession = UserTryoutSession::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereHas('tryout.blueprints.kategori', function ($query) use ($kepribadianKategoriCodes) {
                $query->whereIn('kode', $kepribadianKategoriCodes);
            })
            ->with('tryout:id,judul')
            ->orderBy('finished_at', 'desc')
            ->first();
        
        if ($kepribadianSession && $kepribadianSession->tryout) {
            $tryoutTitle = $kepribadianSession->tryout->judul;
        }

        return [
            'completed' => true,
            'score' => $kepribadianResult->skor_akhir,
            'tryout_title' => $tryoutTitle,
            'tanggal' => $kepribadianResult->tanggal_tes,
            'message' => 'Tes kepribadian sudah selesai'
        ];
    }

    /**
     * Calculate final score from pre-loaded data
     */
    private function calculateFinalScoreFromData(array $allData): float
    {
        $kecermatanScore = $allData['kecermatan']['score'];
        $kecerdasanScore = $allData['kecerdasan']['score'];
        $kepribadianScore = $allData['kepribadian']['score'];

        // Kecermatan wajib
        if (!is_numeric($kecermatanScore)) {
            return 0;
        }

        $scores = [$kecermatanScore];
        $count = 1;

        // Tambahkan skor kecerdasan jika ada
        if (is_numeric($kecerdasanScore)) {
            $scores[] = $kecerdasanScore;
            $count++;
        }

        // Tambahkan skor kepribadian jika ada
        if (is_numeric($kepribadianScore)) {
            $scores[] = $kepribadianScore;
            $count++;
        }

        // Calculate average of available scores
        $finalScore = array_sum($scores) / $count;
        
        return round($finalScore, 2);
    }

    /**
     * Get scoring information including pass/fail status
     */
    private function getScoringInfo(array $allData): array
    {
        $kecermatanScore = $allData['kecermatan']['score'];
        $kecerdasanScore = $allData['kecerdasan']['score'];
        $kepribadianScore = $allData['kepribadian']['score'];

        // Use ScoringService to calculate with proper weights and passing grade
        $scoringService = app(ScoringService::class);
        $result = $scoringService->calculateFinalScore(
            (float) $kecermatanScore,
            (float) ($kecerdasanScore ?? 0),
            (float) ($kepribadianScore ?? 0)
        );

        return [
            'final_score' => $result['score'],
            'passed' => $result['passed'],
            'passing_grade' => $result['passing_grade'],
            'weights' => $result['weights']
        ];
    }
}

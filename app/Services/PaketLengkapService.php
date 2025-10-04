<?php

namespace App\Services;

use App\Models\User;
use App\Models\HasilTes;
use App\Models\UserTryoutSession;
use App\Models\PackageCategoryMapping;
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

        // OPTIMASI: Cache hasil untuk user ini (cache 1 jam karena jarang berubah)
        return cache()->remember("paket_lengkap_status_{$user->id}", 60 * 60, function () use ($user) {
            $kecermatanStatus = $this->getKecermatanStatus($user);
            $kecerdasanStatus = $this->getKecerdasanStatus($user);
            $kepribadianStatus = $this->getKepribadianStatus($user);

            // Kecermatan wajib + minimal 1 tryout CBT yang berisi kategori yang dibutuhkan
            $isComplete = $kecermatanStatus['completed'] && 
                         ($kecerdasanStatus['completed'] || $kepribadianStatus['completed']);

            return [
                'is_eligible' => true,
                'is_complete' => $isComplete,
                'kecermatan' => $kecermatanStatus,
                'kecerdasan' => $kecerdasanStatus,
                'kepribadian' => $kepribadianStatus,
                'final_score' => $isComplete ? $this->calculateFinalScore($user) : null
            ];
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

        // OPTIMASI: Cache progress percentage (cache 1 jam)
        return cache()->remember("paket_lengkap_progress_{$user->id}", 60 * 60, function () use ($user) {
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
        $status = $this->getCompletionStatus($user);
        
        if (!$status['is_eligible']) {
            return [
                'title' => 'Paket Lengkap',
                'progress' => 0,
                'status' => 'not_eligible',
                'message' => 'Anda tidak memiliki paket lengkap'
            ];
        }

        $progress = $this->getProgressPercentage($user);
        
        if ($status['is_complete']) {
            return [
                'title' => 'Paket Lengkap',
                'progress' => 100,
                'status' => 'completed',
                'message' => 'Paket lengkap sudah selesai!',
                'final_score' => $status['final_score'],
                'details' => [
                    'kecermatan' => $status['kecermatan'],
                    'kecerdasan' => $status['kecerdasan'],
                    'kepribadian' => $status['kepribadian']
                ]
            ];
        }

        $remainingTasks = [];
        if (!$status['kecermatan']['completed']) $remainingTasks[] = 'Tes Kecermatan';
        if (!$status['kecerdasan']['completed'] && !$status['kepribadian']['completed']) {
            $remainingTasks[] = 'Tryout CBT (Kecerdasan/Kepribadian)';
        }

        return [
            'title' => 'Paket Lengkap',
            'progress' => $progress,
            'status' => 'in_progress',
            'message' => 'Selesaikan: ' . implode(', ', $remainingTasks),
            'details' => [
                'kecermatan' => $status['kecermatan'],
                'kecerdasan' => $status['kecerdasan'],
                'kepribadian' => $status['kepribadian']
            ]
        ];
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
}

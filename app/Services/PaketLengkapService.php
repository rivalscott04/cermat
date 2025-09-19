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

        $kecermatanStatus = $this->getKecermatanStatus($user);
        $kecerdasanStatus = $this->getKecerdasanStatus($user);
        $kepribadianStatus = $this->getKepribadianStatus($user);

        $isComplete = $kecermatanStatus['completed'] && 
                     $kecerdasanStatus['completed'] && 
                     $kepribadianStatus['completed'];

        return [
            'is_eligible' => true,
            'is_complete' => $isComplete,
            'kecermatan' => $kecermatanStatus,
            'kecerdasan' => $kecerdasanStatus,
            'kepribadian' => $kepribadianStatus,
            'final_score' => $isComplete ? $this->calculateFinalScore($user) : null
        ];
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

        // Parse skor akhir dari detail_jawaban
        $detailJawaban = json_decode($kecermatanResult->detail_jawaban, true);
        $skorAkhir = $detailJawaban['SKOR_AKHIR'] ?? null;

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

        $completedTryout = UserTryoutSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('tryout.blueprints.kategori', function ($query) use ($kecerdasanKategoriCodes) {
                $query->whereIn('kode', $kecerdasanKategoriCodes);
            })
            ->with(['tryout'])
            ->orderBy('finished_at', 'desc')
            ->first();

        if (!$completedTryout) {
            return [
                'completed' => false,
                'score' => null,
                'message' => 'Belum menyelesaikan tryout kecerdasan'
            ];
        }

        // Calculate score from user answers
        $score = $this->calculateTryoutScore($user, $completedTryout->tryout_id, $completedTryout->id);

        return [
            'completed' => true,
            'score' => $score,
            'tryout_title' => $completedTryout->tryout->judul,
            'tanggal' => $completedTryout->finished_at,
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

        $completedTryout = UserTryoutSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('tryout.blueprints.kategori', function ($query) use ($kepribadianKategoriCodes) {
                $query->whereIn('kode', $kepribadianKategoriCodes);
            })
            ->with(['tryout'])
            ->orderBy('finished_at', 'desc')
            ->first();

        if (!$completedTryout) {
            return [
                'completed' => false,
                'score' => null,
                'message' => 'Belum menyelesaikan tryout kepribadian'
            ];
        }

        // For kepribadian, use TKP final score if available
        $tkpFinalScore = $completedTryout->tkp_final_score;
        
        if ($tkpFinalScore === null) {
            // Fallback: calculate from hasil_tes
            $hasilTes = HasilTes::where('user_id', $user->id)
                ->where('jenis_tes', 'kepribadian')
                ->where('tanggal_tes', '>=', $completedTryout->started_at)
                ->orderBy('tanggal_tes', 'desc')
                ->first();
            
            $tkpFinalScore = $hasilTes ? $hasilTes->tkp_final_score : null;
        }

        return [
            'completed' => true,
            'score' => $tkpFinalScore,
            'tryout_title' => $completedTryout->tryout->judul,
            'tanggal' => $completedTryout->finished_at,
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

        // Ensure all scores are numeric
        if (!is_numeric($kecermatanScore) || !is_numeric($kecerdasanScore) || !is_numeric($kepribadianScore)) {
            return null;
        }

        // Calculate average of three scores
        $finalScore = ($kecermatanScore + $kecerdasanScore + $kepribadianScore) / 3;
        
        return round($finalScore, 2);
    }

    /**
     * Calculate tryout score from user answers
     */
    private function calculateTryoutScore(User $user, int $tryoutId, int $sessionId): ?float
    {
        $userAnswers = \App\Models\UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryoutId)
            ->where('user_tryout_session_id', $sessionId)
            ->get();

        if ($userAnswers->isEmpty()) {
            return null;
        }

        $totalScore = $userAnswers->sum('skor');
        $totalQuestions = $userAnswers->count();

        if ($totalQuestions === 0) {
            return null;
        }

        // Convert to percentage (0-100)
        return round(($totalScore / $totalQuestions) * 100, 2);
    }

    /**
     * Get progress percentage for paket lengkap
     */
    public function getProgressPercentage(User $user): int
    {
        if ($user->paket_akses !== 'lengkap') {
            return 0;
        }

        $status = $this->getCompletionStatus($user);
        $completedCount = 0;

        if ($status['kecermatan']['completed']) $completedCount++;
        if ($status['kecerdasan']['completed']) $completedCount++;
        if ($status['kepribadian']['completed']) $completedCount++;

        return round(($completedCount / 3) * 100);
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
        if (!$status['kecerdasan']['completed']) $remainingTasks[] = 'Tryout Kecerdasan';
        if (!$status['kepribadian']['completed']) $remainingTasks[] = 'Tryout Kepribadian';

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
}

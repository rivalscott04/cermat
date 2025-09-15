<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserTryoutSession;
use App\Models\HasilTes;
use App\Models\UserTryoutSoal;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get tryout history
        $tryoutHistory = UserTryoutSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with(['tryout'])
            ->orderBy('finished_at', 'desc')
            ->get()
            ->map(function ($session) {
                // Calculate total score for this tryout
                $totalScore = UserTryoutSoal::where('user_id', $session->user_id)
                    ->where('tryout_id', $session->tryout_id)
                    ->sum('skor');
                
                $totalQuestions = UserTryoutSoal::where('user_id', $session->user_id)
                    ->where('tryout_id', $session->tryout_id)
                    ->count();
                
                $correctAnswers = UserTryoutSoal::where('user_id', $session->user_id)
                    ->where('tryout_id', $session->tryout_id)
                    ->where('skor', '>', 0)
                    ->count();
                
                return [
                    'id' => $session->id,
                    'type' => 'tryout',
                    'title' => $session->tryout->judul ?? 'Tryout',
                    'date' => $session->finished_at,
                    'score' => $totalScore,
                    'total_questions' => $totalQuestions,
                    'correct_answers' => $correctAnswers,
                    'wrong_answers' => $totalQuestions - $correctAnswers,
                    'percentage' => $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 1) : 0,
                    'duration' => $session->elapsed_minutes,
                    'status' => $this->getTryoutStatus($correctAnswers, $totalQuestions)
                ];
            });

        // Get kecermatan history
        $kecermatanHistory = HasilTes::where('user_id', $user->id)
            ->orderBy('tanggal_tes', 'desc')
            ->get()
            ->map(function ($hasil) {
                $totalQuestions = $hasil->skor_benar + $hasil->skor_salah;
                $percentage = $totalQuestions > 0 ? round(($hasil->skor_benar / $totalQuestions) * 100, 1) : 0;
                
                return [
                    'id' => $hasil->id,
                    'type' => 'kecermatan',
                    'title' => 'Tes Kecermatan',
                    'date' => $hasil->tanggal_tes,
                    'score' => $hasil->skor_benar,
                    'total_questions' => $totalQuestions,
                    'correct_answers' => $hasil->skor_benar,
                    'wrong_answers' => $hasil->skor_salah,
                    'percentage' => $percentage,
                    'duration' => $hasil->waktu_total ?? 0,
                    'status' => $this->getKecermatanStatus($hasil->skor_benar, $totalQuestions)
                ];
            });

        // Combine and sort all history
        $allHistory = $tryoutHistory->concat($kecermatanHistory)
            ->sortByDesc('date')
            ->take(10); // Limit to 10 most recent

        return view('user.history.index', compact('allHistory'));
    }

    private function getTryoutStatus($correct, $total)
    {
        if ($total == 0) return 'unknown';
        
        $percentage = ($correct / $total) * 100;
        
        if ($percentage >= 80) return 'excellent';
        if ($percentage >= 70) return 'good';
        if ($percentage >= 60) return 'fair';
        return 'poor';
    }

    private function getKecermatanStatus($correct, $total)
    {
        if ($total == 0) return 'unknown';
        
        $percentage = ($correct / $total) * 100;
        
        if ($percentage >= 90) return 'excellent';
        if ($percentage >= 80) return 'good';
        if ($percentage >= 70) return 'fair';
        return 'poor';
    }
}

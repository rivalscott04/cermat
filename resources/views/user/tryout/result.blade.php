@extends('layouts.app')

@section('title', 'Hasil Tryout - ' . $tryout->judul)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Score Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fa fa-trophy"></i> Hasil Tryout
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="score-card text-center p-4">
                                    <div class="score-circle">
                                        <div class="score-number">{{ number_format($totalScore, 1) }}</div>
                                        <div class="score-label">Total Skor</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="score-details">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $correctAnswers }}</div>
                                                <div class="stat-label">Jawaban Benar</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $wrongAnswers }}</div>
                                                <div class="stat-label">Jawaban Salah</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $totalQuestions }}</div>
                                                <div class="stat-label">Total Soal</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <div class="stat-number">
                                                    {{ number_format(($correctAnswers / $totalQuestions) * 100, 1) }}%</div>
                                                <div class="stat-label">Akurasi</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Breakdown -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa fa-chart-bar"></i> Breakdown per Kategori
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach ($categoryScores as $category)
                            <div class="category-score mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $category['nama'] }}</h6>
                                        <small class="text-muted">{{ $category['correct'] }}/{{ $category['total'] }} soal
                                            benar</small>
                                    </div>
                                    <div class="text-right">
                                        <div class="score-badge">{{ number_format($category['score'], 1) }}</div>
                                        <div class="percentage">
                                            {{ number_format(($category['correct'] / $category['total']) * 100, 1) }}%</div>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar"
                                        style="width: {{ ($category['correct'] / $category['total']) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa fa-list"></i> Review Jawaban
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach ($userAnswers as $index => $userAnswer)
                            @php
                                // Ambil review data yang sudah diproses dari controller
                                $reviewInfo = $reviewData[$userAnswer->id] ?? null;

                                if (!$reviewInfo) {
                                    continue; // Skip jika tidak ada review data
                                }

                                $shuffledOptions = $reviewInfo['shuffledOptions'];
                                $correctAnswerShuffled = $reviewInfo['correctAnswerShuffled'];
                                $userAnswerShuffled = $reviewInfo['userAnswerShuffled'];
                                $letters = ['A', 'B', 'C', 'D', 'E'];
                            @endphp

                            <div class="answer-review mb-4 {{ $userAnswer->skor > 0 ? 'correct' : 'incorrect' }}">
                                <div class="question-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1">Soal {{ $userAnswer->urutan }}</h6>
                                        <div class="score-indicator">
                                            @if ($userAnswer->skor > 0)
                                                <span class="badge badge-success">
                                                    <i class="fa fa-check"></i> Benar ({{ $userAnswer->skor }})
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    <i class="fa fa-times"></i> Salah (0)
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $userAnswer->soal->kategori->nama }} -
                                        {{ ucfirst(str_replace('_', ' ', $userAnswer->soal->tipe)) }}</small>
                                </div>

                                <div class="question-content mt-3">
                                    <div class="question-text">
                                        {!! nl2br(e($userAnswer->soal->pertanyaan)) !!}
                                    </div>

                                    <div class="options-review mt-3">
                                        @if ($userAnswer->soal->tipe == 'benar_salah')
                                            <!-- Handle True/False Questions -->
                                            @php
                                                $correctAnswerOrig = is_array($userAnswer->soal->jawaban_benar)
                                                    ? $userAnswer->soal->jawaban_benar
                                                    : [$userAnswer->soal->jawaban_benar];

                                                $trueCorrect = in_array('benar', $correctAnswerOrig);
                                                $falseCorrect = in_array('salah', $correctAnswerOrig);
                                                $userSelectedTrue = in_array('benar', $userAnswerShuffled);
                                                $userSelectedFalse = in_array('salah', $userAnswerShuffled);
                                            @endphp

                                            <div
                                                class="option-item {{ $trueCorrect && $userSelectedTrue ? 'correct-answer' : ($trueCorrect && !$userSelectedTrue ? 'correct-not-selected' : (!$trueCorrect && $userSelectedTrue ? 'incorrect-answer' : '')) }}">
                                                <div class="option-content">
                                                    <span class="option-label">BENAR</span>
                                                </div>
                                                <div class="option-indicators">
                                                    @if ($trueCorrect)
                                                        <i class="fa fa-check text-success" title="Jawaban Benar"></i>
                                                    @endif
                                                    @if ($userSelectedTrue)
                                                        @if ($trueCorrect)
                                                            <i class="fa fa-user-check text-success"
                                                                title="Pilihan Anda - Benar"></i>
                                                        @else
                                                            <i class="fa fa-user-times text-danger"
                                                                title="Pilihan Anda - Salah"></i>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>

                                            <div
                                                class="option-item {{ $falseCorrect && $userSelectedFalse ? 'correct-answer' : ($falseCorrect && !$userSelectedFalse ? 'correct-not-selected' : (!$falseCorrect && $userSelectedFalse ? 'incorrect-answer' : '')) }}">
                                                <div class="option-content">
                                                    <span class="option-label">SALAH</span>
                                                </div>
                                                <div class="option-indicators">
                                                    @if ($falseCorrect)
                                                        <i class="fa fa-check text-success" title="Jawaban Benar"></i>
                                                    @endif
                                                    @if ($userSelectedFalse)
                                                        @if ($falseCorrect)
                                                            <i class="fa fa-user-check text-success"
                                                                title="Pilihan Anda - Benar"></i>
                                                        @else
                                                            <i class="fa fa-user-times text-danger"
                                                                title="Pilihan Anda - Salah"></i>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <!-- Handle Multiple Choice Questions dengan Shuffled Options -->
                                            @foreach ($shuffledOptions as $shuffleIndex => $opsi)
                                                @php
                                                    $shuffledLetter = $letters[$shuffleIndex];

                                                    // Check if this shuffled option is correct
                                                    $isCorrect = in_array($shuffledLetter, $correctAnswerShuffled);

                                                    // Check if user selected this shuffled option
                                                    $isUserAnswer = in_array($shuffledLetter, $userAnswerShuffled);

                                                    // PERBAIKAN: Logika untuk menentukan class CSS
                                                    $optionClass = '';

                                                    // Untuk tipe pg_pilih_2 dan pg_bobot, cek bobot dari opsi asli
                                                    if (in_array($userAnswer->soal->tipe, ['pg_pilih_2', 'pg_bobot'])) {
                                                        // Ambil bobot dari opsi asli
                                                        $opsiBobot = is_array($opsi)
                                                            ? $opsi['bobot'] ?? 0
                                                            : $opsi->bobot ?? 0;
                                                        $isCorrectOption = $opsiBobot > 0;

                                                        if ($isUserAnswer && $isCorrectOption) {
                                                            $optionClass = 'correct-answer'; // User pilih dan benar
                                                        } elseif (!$isUserAnswer && $isCorrectOption) {
                                                            $optionClass = 'correct-not-selected'; // Benar tapi tidak dipilih user
                                                        } elseif ($isUserAnswer && !$isCorrectOption) {
                                                            $optionClass = 'incorrect-answer'; // User pilih tapi salah
                                                        }
                                                        // Jika tidak dipilih user dan salah, tidak ada class khusus
                                                    } else {
                                                        // Untuk tipe pg_satu dan benar_salah (logic lama)
                                                        if ($isCorrect && $isUserAnswer) {
                                                            $optionClass = 'correct-answer'; // Benar dan dipilih user
                                                        } elseif ($isCorrect && !$isUserAnswer) {
                                                            $optionClass = 'correct-not-selected'; // Benar tapi tidak dipilih user
                                                        } elseif (!$isCorrect && $isUserAnswer) {
                                                            $optionClass = 'incorrect-answer'; // Salah tapi dipilih user
                                                        }
                                                    }
                                                @endphp

                                                <div class="option-item {{ $optionClass }}">
                                                    <div class="option-content">
                                                        <span class="option-label">{{ $shuffledLetter }}.</span>
                                                        <span
                                                            class="option-text">{{ is_array($opsi) ? $opsi['teks'] : $opsi->teks }}</span>

                                                    </div>
                                                    <div class="option-indicators">
                                                        @php
                                                            // PERBAIKAN: Indikator untuk tipe soal yang berbeda
                                                            if (
                                                                in_array($userAnswer->soal->tipe, [
                                                                    'pg_pilih_2',
                                                                    'pg_bobot',
                                                                ])
                                                            ) {
                                                                $opsiBobot = is_array($opsi)
                                                                    ? $opsi['bobot'] ?? 0
                                                                    : $opsi->bobot ?? 0;
                                                                $showCorrectIcon = $opsiBobot > 0;
                                                            } else {
                                                                $showCorrectIcon = $isCorrect;
                                                            }
                                                        @endphp

                                                        @if ($showCorrectIcon)
                                                            <i class="fa fa-check text-success" title="Jawaban Benar"></i>
                                                        @endif
                                                        @if ($isUserAnswer)
                                                            @if ($showCorrectIcon)
                                                                <i class="fa fa-user-check text-success"
                                                                    title="Pilihan Anda - Benar"></i>
                                                            @else
                                                                <i class="fa fa-user-times text-danger"
                                                                    title="Pilihan Anda - Salah"></i>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    @if ($userAnswer->soal->pembahasan)
                                        <div class="explanation mt-3">
                                            <div class="explanation-header">
                                                <i class="fa fa-lightbulb"></i> Pembahasan:
                                            </div>
                                            <div class="explanation-content">
                                                {!! nl2br(e($userAnswer->soal->pembahasan)) !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Tryout Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Tryout</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <label>Judul:</label>
                            <div>{{ $tryout->judul }}</div>
                        </div>
                        <div class="info-item">
                            <label>Durasi:</label>
                            <div>{{ $tryout->durasi_menit }} menit</div>
                        </div>
                        <div class="info-item">
                            <label>Waktu Mulai:</label>
                            <div>{{ $userAnswers->first()->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="info-item">
                            <label>Waktu Selesai:</label>
                            <div>{{ $userAnswers->first()->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Performance Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Ringkasan Performa</h5>
                    </div>
                    <div class="card-body">
                        <div class="performance-chart">
                            <canvas id="performanceChart" width="300" height="300"></canvas>
                        </div>
                        <div class="performance-stats mt-3">
                            <div class="stat-row">
                                <span>Rata-rata per soal:</span>
                                <strong>{{ number_format($totalScore / $totalQuestions, 2) }}</strong>
                            </div>
                            <div class="stat-row">
                                <span>Skor tertinggi:</span>
                                <strong>{{ number_format($userAnswers->max('skor'), 2) }}</strong>
                            </div>
                            <div class="stat-row">
                                <span>Skor terendah:</span>
                                <strong>{{ number_format($userAnswers->min('skor'), 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('user.tryout.index') }}" class="btn btn-primary btn-block mb-2">
                            <i class="fa fa-list"></i> Daftar Tryout
                        </a>
                        <a href="{{ route('user.tryout.start', $tryout->id) }}" class="btn btn-success btn-block mb-2">
                            <i class="fa fa-redo"></i> Coba Lagi
                        </a>
                        <button onclick="window.print()" class="btn btn-info btn-block">
                            <i class="fa fa-print"></i> Cetak Hasil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .score-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
        }

        .score-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .score-number {
            font-size: 2rem;
            font-weight: bold;
        }

        .score-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #495057;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .score-badge {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
        }

        .percentage {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .category-score .progress-bar {
            background: linear-gradient(90deg, #28a745, #20c997);
        }

        .answer-review {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .answer-review.correct {
            border-left: 4px solid #28a745;
            background-color: #f8fff9;
        }

        .answer-review.incorrect {
            border-left: 4px solid #dc3545;
            background-color: #fff8f8;
        }

        .option-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .option-item.correct-answer {
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .option-item.correct-not-selected {
            background-color: #fff3cd;
            border-color: #ffeaa7;
        }

        .option-item.incorrect-answer {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .option-content {
            flex: 1;
        }

        .option-label {
            font-weight: bold;
            margin-right: 0.5rem;
        }

        .option-weight {
            font-size: 0.8rem;
            color: #6c757d;
            margin-left: 0.5rem;
        }

        .explanation {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 1rem;
            border-radius: 0 6px 6px 0;
        }

        .explanation-header {
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 0.5rem;
        }

        .info-item {
            margin-bottom: 1rem;
        }

        .info-item label {
            font-weight: bold;
            color: #495057;
            display: block;
            margin-bottom: 0.25rem;
        }

        .performance-stats .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .performance-stats .stat-row:last-child {
            border-bottom: none;
        }

        @media print {

            .btn,
            .card-header {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .answer-review {
                break-inside: avoid;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Performance Chart
            const ctx = document.getElementById('performanceChart').getContext('2d');
            const categoryData = @json($categoryScores);

            const labels = categoryData.map(cat => cat.nama);
            const scores = categoryData.map(cat => cat.score);
            const percentages = categoryData.map(cat => (cat.correct / cat.total) * 100);

            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Skor',
                        data: scores,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
                    }, {
                        label: 'Persentase Benar',
                        data: percentages,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(255, 99, 132, 1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                stepSize: 20
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>
@endpush

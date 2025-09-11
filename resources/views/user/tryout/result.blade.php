@extends('layouts.app')

@section('title', 'Hasil Tryout - ' . $tryout->judul)

@section('content')
    <div class="container-fluid">
        <div class="row">
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

                <!-- Question Navigator (same style as work.blade.php) -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Navigasi Soal</h5>
                    </div>
                    <div class="card-body">
                        <div class="question-grid">
                            @for ($i = 1; $i <= $totalQuestions; $i++)
                                @php
                                    $questionStatus = $userAnswers->where('urutan', $i)->first();
                                    $statusClass = '';
                                    $statusText = '';
                                    if ($questionStatus && $questionStatus->skor > 0) {
                                        $statusClass = 'answered';
                                        $statusText = 'Benar';
                                    } elseif ($questionStatus) {
                                        $statusClass = 'unanswered';
                                        $statusText = 'Salah';
                                    }
                                @endphp
                                <a href="#review-soal-{{ $i }}" class="question-number {{ $statusClass }}" title="{{ $statusText }}">
                                    {{ $i }}
                                </a>
                            @endfor
                        </div>

                        <div class="mt-2">
                            <div class="d-flex align-items-center mb-2">
                                <span class="question-legend answered"></span>
                                <small>Benar</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="question-legend unanswered"></span>
                                <small>Salah</small>
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="fa fa-list"></i> Review Jawaban</h5>
                            <small class="text-muted">Soal {{ $currentReviewNumber }} dari {{ $totalQuestions }}</small>
                        </div>
                        <div>
                            <a href="{{ route('user.tryout.finish', ['tryout' => $tryout->id, 'review' => max(1, $currentReviewNumber - 1)]) }}" class="btn btn-sm btn-secondary {{ $currentReviewNumber <= 1 ? 'disabled' : '' }}" {{ $currentReviewNumber <= 1 ? 'aria-disabled=true' : '' }}>
                                <i class="fa fa-arrow-left"></i> Sebelumnya
                            </a>
                            <a href="{{ route('user.tryout.finish', ['tryout' => $tryout->id, 'review' => min($totalQuestions, $currentReviewNumber + 1)]) }}" class="btn btn-sm btn-primary {{ $currentReviewNumber >= $totalQuestions ? 'disabled' : '' }}" {{ $currentReviewNumber >= $totalQuestions ? 'aria-disabled=true' : '' }}>
                                Selanjutnya <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $userAnswer = $currentReviewItem;
                            // Ambil review data yang sudah diproses dari controller
                            $reviewInfo = $reviewData[$userAnswer->id] ?? null;
                            $shuffledOptions = $reviewInfo['shuffledOptions'] ?? collect();
                            $correctAnswerShuffled = $reviewInfo['correctAnswerShuffled'] ?? [];
                            $userAnswerShuffled = $reviewInfo['userAnswerShuffled'] ?? [];
                            $letters = ['A', 'B', 'C', 'D', 'E'];
                        @endphp

                        <div id="review-soal-{{ $userAnswer->urutan }}" class="answer-review mb-4 {{ $userAnswer->skor > 0 ? 'correct' : 'incorrect' }}">
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
                                <small class="text-muted">{{ $userAnswer->soal->kategori->nama }} - {{ ucfirst(str_replace('_', ' ', $userAnswer->soal->tipe)) }}</small>
                            </div>

                            <div class="question-content mt-3">
                                <div class="question-text">
                                    {!! nl2br(e($userAnswer->soal->pertanyaan)) !!}
                                </div>

                                <div class="options-review mt-3">
                                    @if ($userAnswer->soal->tipe == 'benar_salah')
                                        @php
                                            $correctAnswerOrig = is_array($userAnswer->soal->jawaban_benar)
                                                ? $userAnswer->soal->jawaban_benar
                                                : [$userAnswer->soal->jawaban_benar];
                                            $trueCorrect = in_array('benar', $correctAnswerOrig);
                                            $falseCorrect = in_array('salah', $correctAnswerOrig);
                                            $userSelectedTrue = in_array('benar', $userAnswerShuffled);
                                            $userSelectedFalse = in_array('salah', $userAnswerShuffled);
                                        @endphp

                                        <div class="option-item {{ $trueCorrect && $userSelectedTrue ? 'correct-answer' : ($trueCorrect && !$userSelectedTrue ? 'correct-not-selected' : (!$trueCorrect && $userSelectedTrue ? 'incorrect-answer' : '')) }}">
                                            <div class="option-content">
                                                <span class="option-label">BENAR</span>
                                            </div>
                                            <div class="option-indicators">
                                                @if ($trueCorrect)
                                                    <i class="fa fa-check text-success" title="Jawaban Benar"></i>
                                                @endif
                                                @if ($userSelectedTrue)
                                                    @if ($trueCorrect)
                                                        <i class="fa fa-user-check text-success" title="Pilihan Anda - Benar"></i>
                                                    @else
                                                        <i class="fa fa-user-times text-danger" title="Pilihan Anda - Salah"></i>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        <div class="option-item {{ $falseCorrect && $userSelectedFalse ? 'correct-answer' : ($falseCorrect && !$userSelectedFalse ? 'correct-not-selected' : (!$falseCorrect && $userSelectedFalse ? 'incorrect-answer' : '')) }}">
                                            <div class="option-content">
                                                <span class="option-label">SALAH</span>
                                            </div>
                                            <div class="option-indicators">
                                                @if ($falseCorrect)
                                                    <i class="fa fa-check text-success" title="Jawaban Benar"></i>
                                                @endif
                                                @if ($userSelectedFalse)
                                                    @if ($falseCorrect)
                                                        <i class="fa fa-user-check text-success" title="Pilihan Anda - Benar"></i>
                                                    @else
                                                        <i class="fa fa-user-times text-danger" title="Pilihan Anda - Salah"></i>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                        @if ($userAnswer->skor <= 0)
                                            @php
                                                $correctTexts = [];
                                                if ($trueCorrect) { $correctTexts[] = 'BENAR'; }
                                                if ($falseCorrect) { $correctTexts[] = 'SALAH'; }
                                            @endphp
                                            @if (count($correctTexts))
                                                <div class="alert alert-success py-2 px-3" role="alert">
                                                    Jawaban yang benar: {{ implode(', ', $correctTexts) }}
                                                </div>
                                            @endif
                                        @endif
                                    @else
                                        @foreach ($shuffledOptions as $shuffleIndex => $opsi)
                                            @php
                                                $shuffledLetter = $letters[$shuffleIndex];
                                                $isCorrect = in_array($shuffledLetter, $correctAnswerShuffled);
                                                $isUserAnswer = in_array($shuffledLetter, $userAnswerShuffled);
                                                $optionClass = '';

                                                if (in_array($userAnswer->soal->tipe, ['pg_pilih_2', 'pg_bobot'])) {
                                                    $opsiBobot = is_array($opsi) ? $opsi['bobot'] ?? 0 : $opsi->bobot ?? 0;
                                                    $isCorrectOption = $opsiBobot > 0;
                                                    if ($isUserAnswer && $isCorrectOption) {
                                                        $optionClass = 'correct-answer';
                                                    } elseif (!$isUserAnswer && $isCorrectOption) {
                                                        $optionClass = 'correct-not-selected';
                                                    } elseif ($isUserAnswer && !$isCorrectOption) {
                                                        $optionClass = 'incorrect-answer';
                                                    }
                                                } else {
                                                    if ($isCorrect && $isUserAnswer) {
                                                        $optionClass = 'correct-answer';
                                                    } elseif ($isCorrect && !$isUserAnswer) {
                                                        $optionClass = 'correct-not-selected';
                                                    } elseif (!$isCorrect && $isUserAnswer) {
                                                        $optionClass = 'incorrect-answer';
                                                    }
                                                }
                                            @endphp

                                            <div class="option-item {{ $optionClass }}">
                                                <div class="option-content">
                                                    <span class="option-label">{{ $shuffledLetter }}.</span>
                                                    <span class="option-text">{{ is_array($opsi) ? $opsi['teks'] : $opsi->teks }}</span>
                                                </div>
                                                <div class="option-indicators">
                                                    @php
                                                        if (in_array($userAnswer->soal->tipe, ['pg_pilih_2', 'pg_bobot'])) {
                                                            $opsiBobot = is_array($opsi) ? $opsi['bobot'] ?? 0 : $opsi->bobot ?? 0;
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
                                                            <i class="fa fa-user-check text-success" title="Pilihan Anda - Benar"></i>
                                                        @else
                                                            <i class="fa fa-user-times text-danger" title="Pilihan Anda - Salah"></i>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        @php
                                            $correctTexts = [];
                                            if (in_array($userAnswer->soal->tipe, ['pg_pilih_2', 'pg_bobot'])) {
                                                foreach ($shuffledOptions as $opsi) {
                                                    $opsiBobot = is_array($opsi) ? ($opsi['bobot'] ?? 0) : ($opsi->bobot ?? 0);
                                                    if ($opsiBobot > 0) {
                                                        $correctTexts[] = is_array($opsi) ? $opsi['teks'] : $opsi->teks;
                                                    }
                                                }
                                            } else {
                                                foreach ($shuffledOptions as $idx => $opsi) {
                                                    $letter = $letters[$idx];
                                                    if (in_array($letter, $correctAnswerShuffled)) {
                                                        $correctTexts[] = is_array($opsi) ? $opsi['teks'] : $opsi->teks;
                                                    }
                                                }
                                            }
                                        @endphp
                                        @if ($userAnswer->skor <= 0 && count($correctTexts))
                                            <div class="alert alert-success py-2 px-3" role="alert">
                                                Jawaban yang benar: {{ implode(', ', $correctTexts) }}
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                @php
                                    $pType = $userAnswer->soal->pembahasan_type ?? 'text';
                                    $hasText = !empty($userAnswer->soal->pembahasan);
                                    $hasImage = !empty($userAnswer->soal->pembahasan_image_url);
                                @endphp

                                @if ($hasText || $hasImage)
                                    <div class="mt-3">
                                        <div class="card">
                                            <div class="card-header p-2" id="heading-{{ $userAnswer->id }}">
                                                <h6 class="mb-0">
                                                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-{{ $userAnswer->id }}" aria-expanded="false" aria-controls="collapse-{{ $userAnswer->id }}">
                                                        <i class="fa fa-lightbulb-o"></i> Pembahasan Soal {{ $userAnswer->urutan }}
                                                    </button>
                                                </h6>
                                            </div>
                                            <div id="collapse-{{ $userAnswer->id }}" class="collapse" aria-labelledby="heading-{{ $userAnswer->id }}">
                                                <div class="card-body">
                                                    @if ($pType === 'text' || $pType === 'both')
                                                        <div class="explanation mb-3">
                                                            <div class="explanation-header">
                                                                <i class="fa fa-sticky-note"></i> Teks Pembahasan
                                                            </div>
                                                            <div class="explanation-content">
                                                                {!! nl2br(e($userAnswer->soal->pembahasan)) !!}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($hasImage && ($pType === 'image' || $pType === 'both'))
                                                        <div class="explanation">
                                                            <div class="explanation-header">
                                                                <i class="fa fa-image"></i> Gambar Pembahasan
                                                            </div>
                                                            <div class="text-center mt-2">
                                                                <img src="{{ $userAnswer->soal->pembahasan_image_url }}" alt="Gambar Pembahasan" class="img-fluid rounded shadow-sm" style="max-height: 400px; object-fit: contain; cursor: pointer;" onclick="showPembahasanImageModal('{{ $userAnswer->soal->pembahasan_image_url }}')">
                                                                <div class="mt-2">
                                                                    <small class="text-muted"><i class="fa fa-search-plus"></i> Klik untuk memperbesar</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
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
        .question-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            margin-bottom: 1rem;
        }

        .question-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.2s;
            background-color: #6c757d;
            color: white;
            margin-bottom: 8px;
        }

        .question-number.answered { background-color: #28a745; }
        .question-number.unanswered { background-color: #dc3545; }
        .question-number:hover { transform: scale(1.1); text-decoration: none; }

        .question-legend {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
        .question-legend.answered { background-color: #28a745; }
        .question-legend.unanswered { background-color: #dc3545; }

        .answer-review .question-text {
            font-size: 1.6rem;
            line-height: 1.8;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .answer-review .option-content {
            font-size: 1.4rem;
            line-height: 1.8;
            font-weight: 500;
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
            padding: 1.25rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            border: 2px solid #e9ecef;
            background-color: #fafafa;
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

        /* Responsive design untuk tampilan hasil */
        @media (max-width: 768px) {
            .answer-review .question-text {
                font-size: 1.4rem;
                line-height: 1.6;
            }

            .answer-review .option-content {
                font-size: 1.2rem;
                line-height: 1.6;
            }

            .option-item {
                padding: 1rem;
                margin-bottom: 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .answer-review .question-text {
                font-size: 1.2rem;
                line-height: 1.5;
            }

            .answer-review .option-content {
                font-size: 1.1rem;
                line-height: 1.5;
            }

            .option-item {
                padding: 0.75rem;
            }
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
            // Expand/Collapse all pembahasan
            const expandAllBtn = document.getElementById('expandAll');
            const collapseAllBtn = document.getElementById('collapseAll');
            if (expandAllBtn && collapseAllBtn) {
                expandAllBtn.addEventListener('click', function() {
                    document.querySelectorAll('.collapse').forEach(el => {
                        if (!el.classList.contains('show')) {
                            $(el).collapse('show');
                        }
                    });
                });
                collapseAllBtn.addEventListener('click', function() {
                    document.querySelectorAll('.collapse.show').forEach(el => {
                        $(el).collapse('hide');
                    });
                });
            }
        });

        function showPembahasanImageModal(src) {
            const img = document.getElementById('modalPembahasanImage');
            if (img) {
                img.src = src;
                $('#pembahasanImageModal').modal('show');
            }
        }
        
        // Ensure modal can always be closed via buttons/X or ESC
        $(document).ready(function() {
            // Close handlers for Tutup button and X icon
            $(document).on('click', '#pembahasanImageModal .close, #pembahasanImageModal [data-dismiss="modal"]', function() {
                $('#pembahasanImageModal').modal('hide');
            });

            // ESC key to close when modal open
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    if ($('#pembahasanImageModal').hasClass('show')) {
                        $('#pembahasanImageModal').modal('hide');
                    }
                }
            });

            // Prevent click on image from closing the modal accidentally
            $(document).on('click', '#modalPembahasanImage', function(e) {
                e.stopPropagation();
            });
        });
    </script>
    <!-- Modal Preview Pembahasan Image -->
    <div class="modal fade" id="pembahasanImageModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-image"></i> Gambar Pembahasan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center p-2" style="background-color: #f8f9fa;">
                    <img id="modalPembahasanImage" src="" alt="Gambar Pembahasan" class="img-fluid rounded" style="max-height: 80vh; max-width: 100%;">
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endpush

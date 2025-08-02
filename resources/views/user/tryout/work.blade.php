@extends('layouts.app')

@section('title', 'Pengerjaan Tryout - ' . $tryout->judul)

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="mb-0">{{ $tryout->judul }}</h4>
                            <small class="text-muted">Soal {{ $currentQuestion->urutan }} dari {{ $totalQuestions }}</small>
                        </div>
                        <div class="col-auto">
                            <div class="timer-container">
                                <div class="timer-display">
                                    <i class="fa fa-clock-o"></i>
                                    <span id="timer">--:--</span>
                                </div>
                                <div class="progress" style="height: 4px;">
                                    <div id="timer-progress" class="progress-bar bg-warning" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Question Content -->
                    <div class="question-content mb-4">
                        <div class="question-text">
                            {!! nl2br(e($currentQuestion->soal->pertanyaan)) !!}
                        </div>
                        
                        @if($currentQuestion->soal->tipe == 'benar_salah')
                            <!-- True/False Options -->
                            <div class="options-container mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jawaban" id="benar" value="benar" 
                                           {{ in_array('benar', $currentQuestion->jawaban_user ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="benar">
                                        <strong>BENAR</strong>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jawaban" id="salah" value="salah"
                                           {{ in_array('salah', $currentQuestion->jawaban_user ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="salah">
                                        <strong>SALAH</strong>
                                    </label>
                                </div>
                            </div>
                        @else
                            <!-- Multiple Choice Options -->
                            <div class="options-container mt-3">
                                @foreach($currentQuestion->soal->opsi as $opsi)
                                    <div class="form-check">
                                        @if($currentQuestion->soal->tipe == 'pg_pilih_2')
                                            <input class="form-check-input" type="checkbox" name="jawaban[]" 
                                                   id="opsi_{{ $opsi->id }}" value="{{ $opsi->opsi }}"
                                                   {{ in_array($opsi->opsi, $currentQuestion->jawaban_user ?? []) ? 'checked' : '' }}>
                                        @else
                                            <input class="form-check-input" type="radio" name="jawaban" 
                                                   id="opsi_{{ $opsi->id }}" value="{{ $opsi->opsi }}"
                                                   {{ in_array($opsi->opsi, $currentQuestion->jawaban_user ?? []) ? 'checked' : '' }}>
                                        @endif
                                        <label class="form-check-label" for="opsi_{{ $opsi->id }}">
                                            <strong>{{ strtoupper($opsi->opsi) }}.</strong> {{ $opsi->teks }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <!-- Navigation Buttons -->
                    <div class="question-navigation">
                        <div class="row">
                            <div class="col">
                                @if($currentQuestion->urutan > 1)
                                    <a href="{{ route('user.tryout.work', ['tryout' => $tryout->id, 'question' => $currentQuestion->urutan - 1]) }}" 
                                       class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Sebelumnya
                                    </a>
                                @endif
                            </div>
                            <div class="col text-center">
                                <button type="button" class="btn btn-info" onclick="saveAnswer()">
                                    <i class="fa fa-save"></i> Simpan Jawaban
                                </button>
                            </div>
                            <div class="col text-right">
                                @if($currentQuestion->urutan < $totalQuestions)
                                    <a href="{{ route('user.tryout.work', ['tryout' => $tryout->id, 'question' => $currentQuestion->urutan + 1]) }}" 
                                       class="btn btn-primary">
                                        Selanjutnya <i class="fa fa-arrow-right"></i>
                                    </a>
                                @else
                                    <a href="{{ route('user.tryout.finish', $tryout->id) }}" 
                                       class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin mengakhiri tryout?')">
                                        <i class="fa fa-check"></i> Selesai
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar - Question Navigator -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Navigasi Soal</h5>
                </div>
                <div class="card-body">
                    <div class="question-grid">
                        @for($i = 1; $i <= $totalQuestions; $i++)
                            @php
                                $questionStatus = $questions->where('urutan', $i)->first();
                                $statusClass = '';
                                $statusText = '';
                                
                                if($i == $currentQuestion->urutan) {
                                    $statusClass = 'current';
                                    $statusText = 'Sedang dikerjakan';
                                } elseif($questionStatus && $questionStatus->sudah_dijawab) {
                                    $statusClass = 'answered';
                                    $statusText = 'Sudah dijawab';
                                } else {
                                    $statusClass = 'unanswered';
                                    $statusText = 'Belum dijawab';
                                }
                            @endphp
                            
                            <a href="{{ route('user.tryout.work', ['tryout' => $tryout->id, 'question' => $i]) }}" 
                               class="question-number {{ $statusClass }}" title="{{ $statusText }}">
                                {{ $i }}
                            </a>
                        @endfor
                    </div>
                    
                    <div class="mt-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="question-legend current"></span>
                            <small>Sedang dikerjakan</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="question-legend answered"></span>
                            <small>Sudah dijawab</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="question-legend unanswered"></span>
                            <small>Belum dijawab</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Auto-save notification -->
<div id="auto-save-notification" class="position-fixed" style="top: 20px; right: 20px; z-index: 1050; display: none;">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-check"></i> Jawaban berhasil disimpan!
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
</div>

@endsection

@push('styles')
<style>
.timer-container {
    text-align: center;
}

.timer-display {
    font-size: 1.2rem;
    font-weight: bold;
    color: #495057;
}

.timer-display i {
    margin-right: 5px;
}

.question-content {
    min-height: 300px;
}

.question-text {
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.options-container .form-check {
    margin-bottom: 1rem;
    padding: 0.75rem;
    border: 1px solid #e9ecef;
    border-radius: 0.25rem;
    transition: all 0.2s;
}

.options-container .form-check:hover {
    background-color: #f8f9fa;
    border-color: #007bff;
}

.options-container .form-check-input:checked + .form-check-label {
    font-weight: bold;
    color: #007bff;
}

.question-navigation {
    border-top: 1px solid #e9ecef;
    padding-top: 1rem;
    margin-top: 2rem;
}

.question-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.question-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.2s;
}

.question-number.current {
    background-color: #007bff;
    color: white;
}

.question-number.answered {
    background-color: #28a745;
    color: white;
}

.question-number.unanswered {
    background-color: #6c757d;
    color: white;
}

.question-number:hover {
    transform: scale(1.1);
    text-decoration: none;
}

.question-legend {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    margin-right: 0.5rem;
}

.question-legend.current {
    background-color: #007bff;
}

.question-legend.answered {
    background-color: #28a745;
}

.question-legend.unanswered {
    background-color: #6c757d;
}

#auto-save-notification {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/tryout-timer.js') }}"></script>
<script>
// Initialize tryout functionality when page loads
document.addEventListener('DOMContentLoaded', function() {
    initTryout({{ $tryout->id }}, {{ $tryout->durasi_menit }}, {{ $currentQuestion->soal_id }});
});
</script>
@endpush 
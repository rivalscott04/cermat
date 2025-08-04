@extends('layouts.tryout')

@section('title', 'Pengerjaan Tryout - ' . $tryout->judul)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="card mt-2 mb-5">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="mb-0">{{ $tryout->judul }}</h4>
                                <small class="text-muted">Soal {{ $currentQuestion->urutan }} dari
                                    {{ $totalQuestions }}</small>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex align-items-center">
                                    <!-- Fullscreen Toggle Button -->
                                    <button class="btn btn-outline-primary btn-sm mr-2" id="tryoutFullscreenToggle" title="Mode Fokus (F11)">
                                        <i class="fa fa-expand"></i> Mode Fokus
                                    </button>
                                    
                                    <!-- Keyboard Shortcuts Info -->
                                    <button class="btn btn-outline-info btn-sm mr-2" id="showShortcuts" title="Keyboard Shortcuts">
                                        <i class="fa fa-keyboard-o"></i> Shortcuts
                                    </button>
                                    
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
                    </div>

                    <div class="card-body">
                        <!-- Question Content -->
                        <div class="question-content mb-4">
                            <div class="question-text">
                                {!! nl2br(e($currentQuestion->soal->pertanyaan)) !!}
                            </div>

                            {{-- Tampilkan gambar jika tipe soal adalah gambar --}}
                            @if ($currentQuestion->soal->tipe === 'gambar' && $currentQuestion->soal->gambar)
                                <div class="question-image-container mt-3 mb-4">
                                    <div class="text-center">
                                        <img src="{{ asset('storage/' . $currentQuestion->soal->gambar) }}"
                                            alt="Gambar Soal" class="img-fluid question-image rounded shadow-sm"
                                            style="max-height: 500px; max-width: 100%; object-fit: contain; cursor: pointer;"
                                            onclick="showImageModal('{{ asset('storage/' . $currentQuestion->soal->gambar) }}')"
                                            title="Klik untuk memperbesar gambar">
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fa fa-search-plus"></i> Klik gambar untuk memperbesar
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($currentQuestion->soal->tipe == 'benar_salah')
                                <!-- True/False Options -->
                                <div class="options-container mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jawaban" id="benar"
                                            value="benar"
                                            {{ in_array('benar', $currentQuestion->jawaban_user ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="benar">
                                            <strong>BENAR</strong>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jawaban" id="salah"
                                            value="salah"
                                            {{ in_array('salah', $currentQuestion->jawaban_user ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="salah">
                                            <strong>SALAH</strong>
                                        </label>
                                    </div>
                                </div>
                            @else
                                <!-- Multiple Choice Options - UPDATED: Gunakan shuffled_opsi berdasarkan session -->
                                <div class="options-container mt-3">
                                    @php
                                        // Gunakan shuffled options jika ada, atau opsi original
                                        $optionsToShow =
                                            $currentQuestion->soal->shuffled_opsi ?? $currentQuestion->soal->opsi;
                                        $letters = ['A', 'B', 'C', 'D', 'E'];
                                    @endphp

                                    @foreach ($optionsToShow as $index => $opsi)
                                        @php
                                            // Assign huruf baru berdasarkan urutan shuffled
                                            $displayLetter = $letters[$index];
                                            $opsiValue = $displayLetter; // Gunakan huruf display sebagai value
                                        @endphp
                                        <div class="form-check">
                                            @if ($currentQuestion->soal->tipe == 'pg_pilih_2')
                                                <input class="form-check-input" type="checkbox" name="jawaban[]"
                                                    id="opsi_{{ $index }}" value="{{ $opsiValue }}"
                                                    {{ in_array($opsiValue, $currentQuestion->jawaban_user ?? []) ? 'checked' : '' }}>
                                            @else
                                                <input class="form-check-input" type="radio" name="jawaban"
                                                    id="opsi_{{ $index }}" value="{{ $opsiValue }}"
                                                    {{ in_array($opsiValue, $currentQuestion->jawaban_user ?? []) ? 'checked' : '' }}>
                                            @endif
                                            <label class="form-check-label" for="opsi_{{ $index }}">
                                                <strong>{{ $displayLetter }}.</strong> {{ $opsi['teks'] ?? $opsi->teks }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Reset Answer Button -->
                            <div class="reset-answer-container mt-3">
                                <button type="button" class="btn btn-outline-warning btn-sm" id="resetAnswerBtn"
                                    onclick="resetAnswer()" title="Hapus pilihan jawaban">
                                    <i class="fa fa-eraser"></i> Reset Jawaban
                                </button>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="question-navigation">
                            <div class="row">
                                <div class="col">
                                    @if ($currentQuestion->urutan > 1)
                                        <a href="{{ route('user.tryout.work', ['tryout' => $tryout->id, 'question' => $currentQuestion->urutan - 1]) }}"
                                            class="btn btn-secondary" onclick="allowTryoutNavigation()">
                                            <i class="fa fa-arrow-left"></i> Sebelumnya
                                        </a>
                                    @endif
                                </div>
                                <div class="col text-right">
                                    @if ($currentQuestion->urutan < $totalQuestions)
                                        <a href="{{ route('user.tryout.work', ['tryout' => $tryout->id, 'question' => $currentQuestion->urutan + 1]) }}"
                                            class="btn btn-primary" onclick="allowTryoutNavigation()">
                                            Selanjutnya <i class="fa fa-arrow-right"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-success" onclick="showFinishConfirmation()">
                                            <i class="fa fa-check"></i> Selesai
                                        </button>
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
                        <!-- TAMBAHAN: Tampilkan info session -->
                        @if ($session && $session->shuffle_seed)
                            <small class="text-muted">Session: {{ substr(md5($session->shuffle_seed), 0, 8) }}</small>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="question-grid">
                            @for ($i = 1; $i <= $totalQuestions; $i++)
                                @php
                                    $questionStatus = $userSoals->where('urutan', $i)->first();
                                    $statusClass = '';
                                    $statusText = '';

                                    if ($i == $currentQuestion->urutan) {
                                        $statusClass = 'current';
                                        $statusText = 'Sedang dikerjakan';
                                    } elseif ($questionStatus && $questionStatus->sudah_dijawab) {
                                        $statusClass = 'answered';
                                        $statusText = 'Sudah dijawab';
                                    } else {
                                        $statusClass = 'unanswered';
                                        $statusText = 'Belum dijawab';
                                    }
                                @endphp

                                <a href="{{ route('user.tryout.work', ['tryout' => $tryout->id, 'question' => $i]) }}"
                                    class="question-number {{ $statusClass }}" title="{{ $statusText }}"
                                    onclick="allowTryoutNavigation()">
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

                        <!-- Session Info -->
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-muted">
                                <div class="d-flex justify-content-between">
                                    <span>Total Soal:</span>
                                    <span>{{ $totalQuestions }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Terjawab:</span>
                                    <span
                                        class="text-success">{{ $userSoals->where('sudah_dijawab', true)->count() }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Belum:</span>
                                    <span
                                        class="text-warning">{{ $userSoals->where('sudah_dijawab', false)->count() }}</span>
                                </div>
                            </small>
                        </div>

                        <!-- Finish Tryout Button in Sidebar -->
                        <div class="mt-3 pt-3 border-top">
                            <button type="button" class="btn btn-success btn-block" onclick="showFinishConfirmation()">
                                <i class="fa fa-check"></i> Selesaikan Tryout
                            </button>
                        </div>

                        <!-- TAMBAHAN: Restart Button dengan konfirmasi -->
                        <div class="mt-2">
                            <button type="button" class="btn btn-outline-danger btn-block btn-sm"
                                onclick="showRestartConfirmation()">
                                <i class="fa fa-refresh"></i> Restart Tryout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Auto-save notification -->
    <div id="auto-save-notification" class="position-fixed"
        style="top: 20px; right: 20px; z-index: 1050; display: none;">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check"></i> Jawaban berhasil disimpan!
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    </div>

    <!-- Finish confirmation modal -->
    <div class="modal fade" id="finishConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-check-circle text-success"></i>
                        Selesaikan Tryout
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold mb-3">Ringkasan Pengerjaan:</h6>
                            <div class="mb-2">
                                <span class="text-muted">Total Soal:</span>
                                <span class="font-weight-bold ml-2">{{ $totalQuestions }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted">Sudah Dijawab:</span>
                                <span class="font-weight-bold text-success ml-2" id="answered-count">
                                    {{ $userSoals->where('sudah_dijawab', true)->count() }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted">Belum Dijawab:</span>
                                <span class="font-weight-bold text-warning ml-2" id="unanswered-count">
                                    {{ $userSoals->where('sudah_dijawab', false)->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold mb-3">Informasi:</h6>
                            <div class="alert alert-info mb-3">
                                <small>
                                    <i class="fa fa-info-circle"></i>
                                    Setelah menyelesaikan tryout, Anda tidak dapat mengubah jawaban lagi.
                                </small>
                            </div>
                            <div id="unanswered-warning" class="alert alert-warning" style="display: none;">
                                <small>
                                    <i class="fa fa-exclamation-triangle"></i>
                                    Masih ada soal yang belum dijawab. Yakin ingin menyelesaikan?
                                </small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <p class="text-center mb-0">
                        <strong>Apakah Anda yakin ingin menyelesaikan tryout ini?</strong>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Batal
                    </button>
                    <a href="{{ route('user.tryout.finish', $tryout->id) }}" class="btn btn-success"
                        onclick="allowTryoutNavigation()">
                        <i class="fa fa-check"></i> Ya, Selesaikan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- TAMBAHAN: Restart confirmation modal -->
    <div class="modal fade" id="restartConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-refresh text-warning"></i>
                        Restart Tryout
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Perhatian!</strong> Restart akan:
                        <ul class="mb-0 mt-2">
                            <li>Menghapus semua jawaban yang sudah Anda buat</li>
                            <li>Mengacak ulang urutan opsi jawaban</li>
                            <li>Memulai timer dari awal</li>
                            <li>Tidak dapat dibatalkan</li>
                        </ul>
                    </div>
                    <p class="text-center mb-0">
                        <strong>Apakah Anda yakin ingin restart tryout ini?</strong>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Batal
                    </button>
                    <a href="{{ route('user.tryout.restart', $tryout->id) }}" class="btn btn-warning"
                        onclick="allowTryoutNavigation()">
                        <i class="fa fa-refresh"></i> Ya, Restart
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Time's up modal -->
    <div class="modal fade" id="timeUpModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Waktu Habis!</h5>
                </div>
                <div class="modal-body">
                    <p>Waktu tryout telah habis. Anda akan diarahkan ke halaman hasil.</p>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('user.tryout.finish', $tryout->id) }}" class="btn btn-primary">
                        Lihat Hasil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Image preview modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-image"></i> Gambar Soal
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center p-2" style="background-color: #f8f9fa;">
                    <img id="modalImage" src="" alt="Gambar Soal" class="img-fluid rounded"
                        style="max-height: 80vh; max-width: 100%;">
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Tutup
                    </button>
                    <button type="button" class="btn btn-primary" id="downloadImageBtn">
                        <i class="fa fa-download"></i> Download
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <!-- Styles tetap sama -->
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

        .timer-display.warning {
            color: #fd7e14;
        }

        .timer-display.danger {
            color: #dc3545;
            animation: blink 1s infinite;
        }

        @keyframes blink {

            0%,
            50% {
                opacity: 1;
            }

            51%,
            100% {
                opacity: 0.5;
            }
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

        .options-container .form-check-input:checked+.form-check-label {
            font-weight: bold;
            color: #007bff;
        }

        .reset-answer-container {
            border-top: 1px solid #e9ecef;
            padding-top: 1rem;
        }

        .question-navigation {
            border-top: 1px solid #e9ecef;
            padding-top: 1rem;
            margin-top: 2rem;
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

        #resetAnswerBtn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Modal styling improvements */
        .modal-header .modal-title i {
            margin-right: 8px;
        }

        .modal-body .alert {
            border-radius: 6px;
        }

        .modal-body .alert i {
            margin-right: 5px;
        }

        .modal-content {
            border-radius: 8px;
        }

        .modal-header {
            background-color: #f8f9fa;
        }

        /* Question Image Styling */
        .question-image-container {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
        }

        .question-image {
            border: 2px solid #dee2e6;
            transition: all 0.3s ease;
        }

        /* Image Modal Styling */
        .modal-xl {
            max-width: 90%;
        }

        #modalImage {
            max-width: 100%;
            height: auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        /* Responsive image container */
        @media (max-width: 768px) {
            .question-image-container {
                padding: 10px;
            }

            .question-image {
                max-height: 300px;
            }

            .modal-xl {
                max-width: 95%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/tryout-timer.js') }}"></script>
    <script>
        // Initialize tryout functionality when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Pass actual remaining time instead of full duration
            const remainingSeconds = {{ $timeLeft }};
            const totalDuration = {{ $tryout->durasi_menit * 60 }};

            // Initialize timer with remaining time
            initTryout(
                {{ $tryout->id }},
                Math.floor(remainingSeconds / 60), // remaining minutes
                {{ $currentQuestion->soal_id }},
                remainingSeconds // remaining seconds
            );

            // Auto-save when answer changes
            const answerInputs = document.querySelectorAll('input[name="jawaban"], input[name="jawaban[]"]');
            answerInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Small delay to allow multiple selections for checkboxes
                    setTimeout(saveAnswer, 100);
                });
            });

            // Warning when time is running low
            if (remainingSeconds <= 300) { // 5 minutes
                document.getElementById('timer').parentElement.classList.add('warning');
            }
            if (remainingSeconds <= 60) { // 1 minute
                document.getElementById('timer').parentElement.classList.add('danger');
            }

            // Check if there's any answer selected to show/hide reset button
            updateResetButtonVisibility();
        });

        function saveAnswer() {
            const form = document.querySelector('.options-container');
            const checkedInputs = form.querySelectorAll('input:checked');

            if (checkedInputs.length === 0) {
                return; // No answer selected
            }

            let jawaban = [];
            checkedInputs.forEach(input => {
                jawaban.push(input.value);
            });

            // AJAX call to save answer
            fetch('{{ route('user.tryout.submit-answer', $tryout->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        soal_id: {{ $currentQuestion->soal_id }},
                        jawaban: jawaban
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Jawaban berhasil disimpan!', 'success');
                        // Update question status in sidebar
                        updateQuestionStatus({{ $currentQuestion->urutan }}, 'answered');
                        // Show reset button
                        updateResetButtonVisibility();
                    } else {
                        showNotification('Gagal menyimpan jawaban: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan saat menyimpan jawaban', 'error');
                });
        }

        function resetAnswer() {
            // Check if there's any answer selected
            const checkedInputs = document.querySelectorAll('.options-container input:checked');

            if (checkedInputs.length === 0) {
                showNotification('Tidak ada jawaban yang dipilih', 'warning');
                return;
            }

            // Show loading state
            const resetBtn = document.getElementById('resetAnswerBtn');
            const originalText = resetBtn.innerHTML;
            resetBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mereset...';
            resetBtn.disabled = true;

            // Send AJAX request to remove answer from database
            fetch('{{ route('user.tryout.reset-answer', $tryout->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        soal_id: {{ $currentQuestion->soal_id }}
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Clear all selected answers after successful server response
                        const allInputs = document.querySelectorAll('.options-container input');
                        allInputs.forEach(input => {
                            input.checked = false;
                        });

                        showNotification('Jawaban berhasil direset!', 'warning');
                        // Update question status in sidebar
                        updateQuestionStatus({{ $currentQuestion->urutan }}, 'unanswered');
                        // Hide reset button
                        updateResetButtonVisibility();
                    } else {
                        showNotification('Gagal mereset jawaban: ' + (data.message || 'Error tidak diketahui'),
                            'error');
                    }
                })
                .catch(error => {
                    console.error('Reset error details:', error);
                    let errorMessage = 'Terjadi kesalahan saat mereset jawaban';
                    if (error.message) {
                        errorMessage += ': ' + error.message;
                    }
                    showNotification(errorMessage, 'error');
                })
                .finally(() => {
                    // Restore button state
                    resetBtn.innerHTML = originalText;
                    resetBtn.disabled = false;
                });
        }

        // Function to show image in modal
        function showImageModal(imageSrc) {
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageSrc;

            // Store image source for download
            modalImage.setAttribute('data-src', imageSrc);

            // Show modal
            $('#imagePreviewModal').modal('show');
        }

        // Function to download image
        function downloadImage() {
            const modalImage = document.getElementById('modalImage');
            const imageSrc = modalImage.getAttribute('data-src') || modalImage.src;

            // Create temporary link element
            const link = document.createElement('a');
            link.href = imageSrc;

            // Get filename from URL or use default
            const urlParts = imageSrc.split('/');
            const filename = urlParts[urlParts.length - 1] || 'soal-gambar.jpg';
            link.download = filename;

            // Trigger download
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Show notification
            showNotification('Gambar berhasil didownload!', 'success');
        }

        // Fungsi untuk menampilkan modal finish confirmation
        function showFinishConfirmation() {
            // Update counts in modal
            const answeredCount = document.querySelectorAll('.question-number.answered').length;
            const totalQuestions = {{ $totalQuestions }};
            const unansweredCount = totalQuestions - answeredCount;

            document.getElementById('answered-count').textContent = answeredCount;
            document.getElementById('unanswered-count').textContent = unansweredCount;

            // Show warning if there are unanswered questions
            const warningDiv = document.getElementById('unanswered-warning');
            if (unansweredCount > 0) {
                warningDiv.style.display = 'block';
            } else {
                warningDiv.style.display = 'none';
            }

            // Show modal
            $('#finishConfirmModal').modal('show');
        }

        // TAMBAHAN: Fungsi untuk menampilkan modal restart confirmation
        function showRestartConfirmation() {
            $('#restartConfirmModal').modal('show');
        }

        function updateResetButtonVisibility() {
            const resetBtn = document.getElementById('resetAnswerBtn');
            const checkedInputs = document.querySelectorAll('.options-container input:checked');

            if (checkedInputs.length > 0) {
                resetBtn.style.display = 'inline-block';
            } else {
                resetBtn.style.display = 'none';
            }
        }

        function showNotification(message, type = 'success') {
            const notification = document.getElementById('auto-save-notification');
            const alert = notification.querySelector('.alert');

            // Update message
            const messageSpan = alert.querySelector('i').nextSibling;
            messageSpan.textContent = ' ' + message;

            // Update alert type
            alert.className = 'alert alert-dismissible fade show';
            if (type === 'success') {
                alert.classList.add('alert-success');
            } else if (type === 'warning') {
                alert.classList.add('alert-warning');
            } else {
                alert.classList.add('alert-danger');
            }

            // Update icon
            const icon = alert.querySelector('i');
            if (type === 'success') {
                icon.className = 'fa fa-check';
            } else if (type === 'warning') {
                icon.className = 'fa fa-exclamation-triangle';
            } else {
                icon.className = 'fa fa-times';
            }

            // Show notification
            notification.style.display = 'block';

            // Auto hide after 3 seconds
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        function updateQuestionStatus(questionNumber, status) {
            const questionLink = document.querySelector(`.question-number[href*="question=${questionNumber}"]`);
            if (questionLink) {
                questionLink.className = `question-number ${status}`;

                // Update tooltip
                const statusText = status === 'answered' ? 'Sudah dijawab' :
                    status === 'current' ? 'Sedang dikerjakan' : 'Belum dijawab';
                questionLink.setAttribute('title', statusText);
            }
        }

        // Handle time up
        function onTimeUp() {
            // Show modal
            $('#timeUpModal').modal('show');

            // Disable all inputs
            const inputs = document.querySelectorAll('input[type="radio"], input[type="checkbox"]');
            inputs.forEach(input => input.disabled = true);

            // Disable reset button
            document.getElementById('resetAnswerBtn').disabled = true;

            // Auto redirect after 5 seconds
            setTimeout(() => {
                window.location.href = '{{ route('user.tryout.finish', $tryout->id) }}';
            }, 5000);
        }

        // Expose function globally for timer script
        window.onTimeUp = onTimeUp;

        // Auto-allow navigation for all tryout-related links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link) {
                const href = link.getAttribute('href');
                // Allow navigation for tryout-related URLs and same page navigation
                if (href && (
                        href.includes('/tryout/') ||
                        href.includes('question=') ||
                        href.includes('/finish') ||
                        href.includes('/restart') ||
                        href.startsWith('#') ||
                        href === window.location.href ||
                        isInternalTryoutNavigation(href)
                    )) {
                    allowTryoutNavigation();
                    console.log('Allowing navigation to:', href);
                }
            }
        });

        // Handle form submissions (like save answer)
        document.addEventListener('submit', function(e) {
            allowTryoutNavigation();
        });

        // Reset navigation flag when page is about to load
        window.addEventListener('beforeunload', function() {
            // This runs before the beforeunload warning
            setTimeout(() => {
                allowNavigation = false;
                isInternalNavigation = false;
            }, 10);
        });

        // Update reset button visibility when answers change
        document.addEventListener('change', function(e) {
            if (e.target.matches('.options-container input')) {
                updateResetButtonVisibility();
            }
        });

        // Event listeners untuk modal
        $(document).ready(function() {
            // Finish modal
            $('#finishConfirmModal').on('hidden.bs.modal', function() {
                console.log('Finish modal closed');
            });

            // Restart modal
            $('#restartConfirmModal').on('hidden.bs.modal', function() {
                console.log('Restart modal closed');
            });

            // Image preview modal events
            $('#imagePreviewModal').on('shown.bs.modal', function() {
                console.log('Image modal opened. Press D to download or Escape to close.');
            });

            $('#imagePreviewModal').on('hidden.bs.modal', function() {
                // Clear image source when modal is closed
                const modalImage = document.getElementById('modalImage');
                modalImage.src = '';
                modalImage.removeAttribute('data-src');
            });

            // Download button click handler
            $('#downloadImageBtn').on('click', function() {
                downloadImage();
            });

            // Handle close button clicks untuk semua modal
            $('.modal .close, .modal [data-dismiss="modal"]').on('click', function() {
                $(this).closest('.modal').modal('hide');
            });

            // Handle escape key untuk semua modal
            $(document).on('keydown', function(e) {
                if (e.keyCode === 27) { // Escape key
                    $('.modal.show').modal('hide');
                } else if (e.keyCode === 68 && $('#imagePreviewModal').hasClass(
                        'show')) { // 'D' key for download when image modal is open
                    e.preventDefault();
                    downloadImage();
                }
            });

            // Prevent image modal from closing when clicking on the image
            $('#modalImage').on('click', function(e) {
                e.stopPropagation();
            });
            
            // Tryout Fullscreen Toggle Integration
            const tryoutFullscreenToggle = document.getElementById('tryoutFullscreenToggle');
            if (tryoutFullscreenToggle) {
                tryoutFullscreenToggle.addEventListener('click', function() {
                    // Trigger the main fullscreen toggle
                    const mainFullscreenToggle = document.getElementById('fullscreenToggle');
                    if (mainFullscreenToggle) {
                        mainFullscreenToggle.click();
                    }
                });
            }
            
            // Update button text based on fullscreen state
            function updateFullscreenButtonText() {
                const body = document.body;
                const button = document.getElementById('tryoutFullscreenToggle');
                if (button) {
                    if (body.classList.contains('tryout-fullscreen')) {
                        button.innerHTML = '<i class="fa fa-compress"></i> Keluar Fokus';
                        button.className = 'btn btn-outline-danger btn-sm mr-2';
                    } else {
                        button.innerHTML = '<i class="fa fa-expand"></i> Mode Fokus';
                        button.className = 'btn btn-outline-primary btn-sm mr-2';
                    }
                }
            }
            
            // Listen for fullscreen state changes
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        updateFullscreenButtonText();
                    }
                });
            });
            
            observer.observe(document.body, {
                attributes: true,
                attributeFilter: ['class']
            });
            
            // Initial button text update
            updateFullscreenButtonText();
            
            // Simplified Keyboard Shortcuts Modal
            const showShortcutsBtn = document.getElementById('showShortcuts');
            if (showShortcutsBtn) {
                showShortcutsBtn.addEventListener('click', function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Keyboard Shortcuts',
                            html: `
                                <div class="text-left">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6><i class="fa fa-expand"></i> Mode Fokus</h6>
                                            <ul class="list-unstyled">
                                                <li><kbd>F11</kbd> - Toggle Mode Fokus</li>
                                                <li><kbd>ESC</kbd> - Keluar Mode Fokus</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6><i class="fa fa-mouse-pointer"></i> Navigasi</h6>
                                            <ul class="list-unstyled">
                                                <li><i class="fa fa-hand-pointer-o"></i> Klik nomor soal</li>
                                                <li><kbd>Ctrl+S</kbd> - Simpan Jawaban</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            `,
                            icon: 'info',
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            }
        });
    </script>
@endpush

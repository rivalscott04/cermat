@extends('layouts.app')

@push('styles')
    <style>
        .container {
            background-color: white;
            min-height: 90vh;
        }

        .timer {
            font-size: 2.5rem;
            /* Reduced from 3.5rem */
            font-weight: bold;
            text-align: center;
            margin-bottom: 1.2rem;
            color: #333;
        }

        .question-container {
            max-width: 700px;
            /* Reduced from 800px */
            margin: 0 auto;
        }

        .soal-table {
            width: 100%;
            margin-bottom: 2rem;
            border-collapse: collapse;
        }

        .soal-table th,
        .soal-table td {
            border: 2px solid #333;
            text-align: center;
            padding: 0.85rem;
            font-size: 2rem;
            /* Reduced from 2.5rem */
        }

        #kolom-merah td {
            font-size: 2.8rem !important;
            /* Reduced from 3.5rem */
            font-weight: bold;
        }

        .soal-table tr:last-child td,
        .label {
            background-color: white !important;
        }

        .soal-table td {
            background-color: white !important;
        }

        .label {
            font-size: 1.8rem;
            /* Reduced from 2.2rem */
            color: #666;
        }

        .karakter {
            font-size: 2.3rem;
            /* Reduced from 3rem */
            font-weight: bold;
            color: #444;
        }

        .answer-container {
            display: flex;
            justify-content: left;
            /* gap: 12px; */
            margin-bottom: 2rem;
            width: 40%;
            border: solid black 2px;
        }

        .answer-box {
            width: 80px;
            /* Reduced from 90px */
            height: 80px;
            /* Reduced from 90px */
            /* border: 2px solid #333; */
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2.8rem;
            /* Reduced from 3.2rem */
            font-weight: bold;
        }

        .options {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .option-btn {
            width: 120px;
            /* Reduced from 140px */
            height: 60px;
            /* Reduced from 70px */
            background-color: white;
            border: 1.5px solid #adabab !important;
            font-size: 1.8rem;
            /* Reduced from 2.2rem */
            transition: all 0.2s;
        }

        .option-btn:hover {
            background-color: #18a689;
        }

        .title-row {
            background-color: white;
            font-weight: bold;
            font-size: 2rem;
            /* Reduced from 2.5rem */
        }

        .score-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            margin: 0 2px;
        }

        .score-badge.benar {
            background-color: #28a745;
            color: white;
        }

        .score-badge.salah {
            background-color: #dc3545;
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="timer" id="timer">60</div>

        <div class="question-container">
            <table class="soal-table">
                <tr class="title-row">
                    <td colspan="5">Kolom <span id="current-set">1</span></td>
                </tr>
                <tr id="kolom-merah">
                    <!-- Will be populated dynamically -->
                </tr>
                <tr id="kolom-label">
                    <td class="label">A</td>
                    <td class="label">B</td>
                    <td class="label">C</td>
                    <td class="label">D</td>
                    <td class="label">E</td>
                </tr>
            </table>

            <div class="answer-container" id="kolom-biru">
                <!-- Will be populated dynamically -->
            </div>

            <div class="options">
                <button class="option-btn border" id="btn-A">A</button>
                <button class="option-btn border" id="btn-B">B</button>
                <button class="option-btn border" id="btn-C">C</button>
                <button class="option-btn border" id="btn-D">D</button>
                <button class="option-btn border" id="btn-E">E</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const saveResultsUrl = "{{ route('kecermatan.simpanHasil') }}";
        const userId = "{{ auth()->id() }}";
    </script>

    <script src="{{ asset('js/kecermatanSoal.js') }}"></script>
@endpush

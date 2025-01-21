@extends('layouts.app')

@push('styles')
  <style>
    .container {
      background-color: white;
      min-height: 90vh;
    }

    .timer {
      font-size: 2rem;
      font-weight: bold;
      text-align: center;
      margin-bottom: 1rem;
      color: #333;
    }

    .question-container {
      max-width: 600px;
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
      padding: 0.75rem;
      font-size: 1.8rem;
    }

    #kolom-merah td {
      font-size: 2.5rem !important;
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
      font-size: 1.6rem;
      color: #666;
    }

    .karakter {
      font-size: 2rem;
      font-weight: bold;
      color: #444;
    }

    .answer-container {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-bottom: 2rem;
    }

    .answer-box {
      width: 70px;
      height: 70px;
      border: 2px solid #333;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 2.5rem;
      font-weight: bold;
    }

    .options {
      display: flex;
      justify-content: center;
      gap: 10px;
    }

    .option-btn {
      width: 110px;
      height: 55px;
      background-color: white;
      border: 1.5px solid #adabab !important;
      font-size: 1.6rem;
      transition: all 0.2s;
    }

    .option-btn:hover {
      background-color: #18a689;
    }

    .title-row {
      background-color: white;
      font-weight: bold;
      font-size: 1.8rem;
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

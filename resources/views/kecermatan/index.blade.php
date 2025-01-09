@extends('layouts.app')

@push('styles')
  <style>
    .soal-container {
      max-width: 1200px;
      margin: 0 auto;
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .soal-row {
      margin-bottom: 20px;
    }

    .soal-group {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
    }

    .soal-input {
      flex: 1;
      margin-right: 10px;
    }

    .type-selector {
      margin-bottom: 30px;
    }

    .btn-karakter {
      background-color: #1ab394;
      color: white;
      min-width: 100px;
      border: none;
      transition: all 0.2s ease;
      padding: 8px 6px;
      border-radius: 8px;
      font-size: 15px;
      margin-top: 27px;
    }

    .btn-karakter:hover {
      background-color: #18a689;
      color: white;
      transform: translateY(-1px);
    }

    .btn-karakter:active {
      transform: translateY(1px);
    }

    .soal-label {
      color: #666;
      margin-bottom: 10px;
      font-weight: 500;
    }

    .btn-isi-otomatis {
      background-color: #0d6efd;
      color: white;
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 15px;
      transition: all 0.2s ease;
      border: none;
    }

    .btn-isi-otomatis:hover {
      background-color: #0b5ed7;
      color: white;
      transform: translateY(-1px);
    }

    .btn-mulai-tes {
      background-color: #28a745;
      color: white;
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 15px;
      transition: all 0.2s ease;
      border: none;
    }

    .btn-mulai-tes:hover {
      background-color: #218838;
      color: white;
      transform: translateY(-1px);
    }

    .instructions {
      color: #6c757d;
      font-size: 0.9rem;
      margin-bottom: 25px;
    }

    .form-control {
      border-radius: 0.375rem !important;
      height: 38px !important;
    }

    .form-control:focus {
      border-color: #4a4de7;
      box-shadow: 0 0 0 0.2rem rgba(74, 77, 231, 0.25);
    }

    .form-select:focus {
      border-color: #4a4de7;
      box-shadow: 0 0 0 0.2rem rgba(74, 77, 231, 0.25);
    }

    .header-controls {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 30px;
    }

    @media (max-width: 768px) {
      .soal-container {
        padding: 15px;
      }

      .header-controls {
        flex-direction: column;
        align-items: stretch;
      }

      .btn-karakter {
        min-width: 80px;
        font-size: 14px;
      }

      .soal-group {
        flex-direction: column;
        align-items: stretch;
      }

      .soal-input {
        margin-right: 0;
        margin-bottom: 10px;
      }
    }
  </style>
@endpush

@section('content')
  <div class="container">
    <div class="wrapper wrapper-content animated-fadeInRight">
      <div class="ibox">
        <div class="ibox-title">
          <h2 class="text-dark font-weight-bold mb-3">Tes Kecermatan</h2>
        </div>
        <div class="ibox-content">
          <form id="kecermatanForm" action="{{ route('kecermatan.soal') }}" method="GET">
            <p class="instructions">Inputkan Huruf, Angka, atau Simbol dengan maksimal 5 karakter (karakter dapat digabung)
            </p>

            <!-- Header controls with dropdown and buttons -->
            <div class="header-controls">
              <select class="form-select" name="jenis" style="max-width: 200px;">
                <option value="huruf">Huruf</option>
                <option value="angka">Angka</option>
                <option value="simbol">Simbol</option>
                <option value="acak">Acak</option>
              </select>
              
              <button type="button" class="btn btn-isi-otomatis">
                <i class="bi bi-hand-index"></i> Isi Soal Otomatis
              </button>
              
              <button type="submit" class="btn btn-mulai-tes">
                Mulai Tes
              </button>
            </div>

            <div class="row">
              <!-- Kolom 1-3 -->
              <div class="col-md-4">
                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 1</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="0"
                      placeholder=").%<-" maxlength="5">
                  </div>
                  <button type="button" class="btn btn-karakter karakter-btn" data-index="0">Huruf</button>
                </div>

                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 4</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="3"
                      placeholder="{@|>*" maxlength="5">
                  </div>
                  <button class="btn btn-karakter karakter-btn" data-index="3">Huruf</button>
                </div>

                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 7</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="6"
                      placeholder="(.@|-" maxlength="5">
                  </div>
                  <button class="btn btn-karakter karakter-btn" data-index="6">Huruf</button>
                </div>
              </div>

              <!-- Kolom 4-6 -->
              <div class="col-md-4">
                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 2</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="1"
                      placeholder="&'!_" maxlength="5">
                  </div>
                  <button class="btn btn-karakter karakter-btn" data-index="1">Huruf</button>
                </div>

                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 5</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="4"
                      placeholder="/^<{}" maxlength="5">
                  </div>
                  <button class="btn btn-karakter karakter-btn" data-index="4">Huruf</button>
                </div>

                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 8</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="7"
                      placeholder="[)_+#" maxlength="5">
                  </div>
                  <button class="btn btn-karakter karakter-btn" data-index="7">Huruf</button>
                </div>
              </div>

              <!-- Kolom 7-9 -->
              <div class="col-md-4">
                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 3</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="2"
                      placeholder="+>_)@" maxlength="5">
                  </div>
                  <button class="btn btn-karakter karakter-btn" data-index="2">Huruf</button>
                </div>

                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 6</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="5"
                      placeholder=":^%|&" maxlength="5">
                  </div>
                  <button class="btn btn-karakter karakter-btn" data-index="5">Huruf</button>
                </div>

                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 9</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="8"
                      placeholder="?^{*" maxlength="5">
                  </div>
                  <button class="btn btn-karakter karakter-btn" data-index="8">Huruf</button>
                </div>
              </div>
            </div>

            <!-- Soal 10 -->
            <div class="row">
              <div class="col-md-4">
                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 10</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="9"
                      placeholder="'*=?[" maxlength="5">
                  </div>
                  <button class="btn btn-karakter karakter-btn" data-index="9">Huruf</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // DOM Elements
      const form = document.getElementById('kecermatanForm');
      const karakterType = document.querySelector('.form-select');
      const karakterBtns = document.querySelectorAll('.karakter-btn');
      const inputs = document.querySelectorAll('.karakter-input');
      const isiOtomatisBtn = document.querySelector('.btn-isi-otomatis');

      // Cache for storing generated character sets
      const karakterCache = {
        huruf: Array(10).fill(null),
        angka: Array(10).fill(null),
        simbol: Array(10).fill(null),
        acak: Array(10).fill(null)
      };

      // Available character sets and placeholders
      const karakterSet = {
        huruf: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        angka: '0123456789',
        simbol: '!@#$%^&*()_+-=[]{}|;:",.<>?',
        get acak() {
          return this.huruf + this.angka + this.simbol;
        }
      };

      // Placeholder examples for each type
      const placeholderExamples = {
        huruf: ['ABCDE', 'FGHIJ', 'KLMNO', 'PQRST', 'UVWXY', 'ZABCD', 'EFGHI', 'JKLMN', 'OPQRS', 'TUVWX'],
        angka: ['12345', '67890', '13579', '24680', '11223', '44556', '77889', '90123', '45678', '98765'],
        simbol: ['!@#$%', '&*()_', '+=-[]', '{}|;:', '"<>?/', '.,$#@', '*&^%

      // Generate random string with specified type and length
      function generateRandomString(type, length = 5) {
        const chars = karakterSet[type] || karakterSet.huruf;
        let result = '';
        const charsLength = chars.length;

        // Ensure we don't have repeating characters
        const usedIndexes = new Set();

        while (result.length < length) {
          const randomIndex = Math.floor(Math.random() * charsLength);
          if (type === 'acak' || !usedIndexes.has(randomIndex)) {
            result += chars.charAt(randomIndex);
            usedIndexes.add(randomIndex);
          }
        }

        return result;
      }

      // Pre-generate characters for all types
      function preGenerateKarakter() {
        ['huruf', 'angka', 'simbol', 'acak'].forEach(type => {
          for (let i = 0; i < 10; i++) {
            karakterCache[type][i] = generateRandomString(type);
          }
        });
      }

      // Get character from cache or generate new one
      function getKarakter(type, index) {
        if (!karakterCache[type][index]) {
          karakterCache[type][index] = generateRandomString(type);
        }
        return karakterCache[type][index];
      }

      // Refresh cache for specific type
      function refreshCache(type) {
        for (let i = 0; i < 10; i++) {
          karakterCache[type][i] = generateRandomString(type);
        }
      }

      // Update all input fields
      function updateAllInputs(type) {
        inputs.forEach((input, index) => {
          input.value = getKarakter(type, index);
        });
      }

      // Validate form before submission
      function validateForm() {
        const emptyInputs = Array.from(inputs).filter(input => !input.value.trim());
        if (emptyInputs.length > 0) {
          alert('Harap isi semua kolom soal terlebih dahulu');
          emptyInputs[0].focus();
          return false;
        }
        return true;
      }

      // Form submission handler
      form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!validateForm()) {
          return;
        }

        // Create array to store inputs in correct order
        const orderedInputs = [];

        // Get all rows
        const rows = form.querySelectorAll('.row');

        // Process first three rows (containing 9 inputs in 3, '#@!&*', '()_+<', '>?,./'],
        acak: ['A1@B2', 'C3#D4', 'E5$F6', 'G7%H8', 'I9*J0', 'K!L@M', 'N#O$P', 'Q%R&S', 'T*U(V', 'W)X+Y']
      };

      // Generate random string with specified type and length
      function generateRandomString(type, length = 5) {
        const chars = karakterSet[type] || karakterSet.huruf;
        let result = '';
        const charsLength = chars.length;

        // Ensure we don't have repeating characters
        const usedIndexes = new Set();

        while (result.length < length) {
          const randomIndex = Math.floor(Math.random() * charsLength);
          if (type === 'acak' || !usedIndexes.has(randomIndex)) {
            result += chars.charAt(randomIndex);
            usedIndexes.add(randomIndex);
          }
        }

        return result;
      }

      // Pre-generate characters for all types
      function preGenerateKarakter() {
        ['huruf', 'angka', 'simbol', 'acak'].forEach(type => {
          for (let i = 0; i < 10; i++) {
            karakterCache[type][i] = generateRandomString(type);
          }
        });
      }

      // Get character from cache or generate new one
      function getKarakter(type, index) {
        if (!karakterCache[type][index]) {
          karakterCache[type][index] = generateRandomString(type);
        }
        return karakterCache[type][index];
      }

      // Refresh cache for specific type
      function refreshCache(type) {
        for (let i = 0; i < 10; i++) {
          karakterCache[type][i] = generateRandomString(type);
        }
      }

      // Update all input fields
      function updateAllInputs(type) {
        inputs.forEach((input, index) => {
          input.value = getKarakter(type, index);
        });
      }

      // Validate form before submission
      function validateForm() {
        const emptyInputs = Array.from(inputs).filter(input => !input.value.trim());
        if (emptyInputs.length > 0) {
          alert('Harap isi semua kolom soal terlebih dahulu');
          emptyInputs[0].focus();
          return false;
        }
        return true;
      }

      // Form submission handler
      form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!validateForm()) {
          return;
        }

        // Create array to store inputs in correct order
        const orderedInputs = [];

        // Get all rows
        const rows = form.querySelectorAll('.row');

        // Process first three rows (containing 9 inputs in 3
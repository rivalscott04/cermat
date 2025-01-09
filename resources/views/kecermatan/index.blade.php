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
                      placeholder="ABCDE" maxlength="5">
                  </div>
                  <button type="button" class="btn btn-karakter karakter-btn" data-index="0">Huruf</button>
                </div>

                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 4</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="3"
                      placeholder="FGHIJ" maxlength="5">
                  </div>
                  <button class="btn btn-karakter karakter-btn" data-index="3">Huruf</button>
                </div>

                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 7</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="6"
                      placeholder="KLMNO" maxlength="5">
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
                      placeholder="PQRST" maxlength="5">
                  </div>
                  <button class="btn btn-karakter karakter-btn" data-index="1">Huruf</button>
                </div>

                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 5</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="4"
                      placeholder="UVWXY" maxlength="5">
                  </div>
                  <button class="btn btn-karakter karakter-btn" data-index="4">Huruf</button>
                </div>

                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 8</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="7"
                      placeholder="ZABCD" maxlength="5">
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
                      placeholder="EFGHI" maxlength="5">
                  </div>
                  <button class="btn btn-karakter karakter-btn" data-index="2">Huruf</button>
                </div>

                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 6</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="5"
                      placeholder="JKLMN" maxlength="5">
                  </div>
                  <button class="btn btn-karakter karakter-btn" data-index="5">Huruf</button>
                </div>

                <div class="soal-group">
                  <div class="soal-input">
                    <label class="soal-label">Kolom / Soal 9</label>
                    <input type="text" name="questions[]" class="form-control karakter-input" data-index="8"
                      placeholder="OPQRS" maxlength="5">
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
                      placeholder="TUVWX" maxlength="5">
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
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      // Placeholder examples untuk setiap tipe
      const placeholderExamples = {
        huruf: ['ABCDE', 'FGHIJ', 'KLMNO', 'PQRST', 'UVWXY', 'ZABCD', 'EFGHI', 'JKLMN', 'OPQRS', 'TUVWX'],
        angka: ['12345', '67890', '13579', '24680', '11223', '44556', '77889', '90123', '45678', '98765'],
        simbol: ['!@#$%', '&*()_', '+=-[]', '{}|;:', '"<>?/', '.,$#@', '*&^%$', '#@!&*', '()_+<', '>?,./'],
        acak: ['A1@B2', 'C3#D4', 'E5$F6', 'G7%H8', 'I9*J0', 'K!L@M', 'N#O$P', 'Q%R&S', 'T*U(V', 'W)X+Y']
      };

      // Karakter set untuk setiap tipe
      const karakterSet = {
        huruf: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        angka: '0123456789',
        simbol: '!@#$%^&*()_+-=[]{}|;:",.<>?',
        acak: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:",.<>?'
      };

      // Generate random string dengan panjang tertentu
      function generateRandomString(type, length = 5) {
        const chars = karakterSet[type];
        const result = [];
        const charsArray = chars.split('');
        
        // Untuk tipe acak, kita bisa menggunakan karakter berulang
        if (type === 'acak') {
          for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * charsArray.length);
            result.push(charsArray[randomIndex]);
          }
        } else {
          // Untuk tipe lain, pastikan tidak ada karakter berulang
          const shuffled = charsArray.sort(() => 0.5 - Math.random());
          return shuffled.slice(0, length).join('');
        }
        
        return result.join('');
      }

      // Update placeholder berdasarkan tipe yang dipilih
      function updatePlaceholders(type) {
        $('.karakter-input').each(function(index) {
          $(this).attr('placeholder', placeholderExamples[type][index]);
        });
      }

      // Event handler untuk perubahan dropdown
      $('.form-select').on('change', function() {
        const selectedType = $(this).val();
        const buttonText = selectedType.charAt(0).toUpperCase() + selectedType.slice(1);
        
        // Update text semua tombol
        $('.karakter-btn').text(buttonText);
        
        // Update placeholders
        updatePlaceholders(selectedType);
      });

      // Event handler untuk tombol "Isi Soal Otomatis"
      $('.btn-isi-otomatis').on('click', function() {
        const selectedType = $('.form-select').val();
        $('.karakter-input').each(function() {
          $(this).val(generateRandomString(selectedType));
        });
      });

      // Event handler untuk tombol karakter individual
      $('.karakter-btn').on('click', function() {
        const selectedType = $('.form-select').val();
        const index = $(this).data('index');
        const input = $(`.karakter-input[data-index="${index}"]`);
        input.val(generateRandomString(selectedType));
      });

      // Event handler untuk validasi input
      $('.karakter-input').on('input', function() {
        const selectedType = $('.form-select').val();
        let value = this.value;

        // Konversi ke uppercase untuk tipe huruf
        if (selectedType === 'huruf') {
          value = value.toUpperCase();
        }

        // Filter karakter yang tidak valid
        if (selectedType !== 'acak') {
          const validChars = new RegExp(`[${karakterSet[selectedType]}]`, 'g');
          value = (value.match(validChars) || []).join('');
        }

        // Batasi panjang
        if (value.length > 5) {
          value = value.slice(0, 5);
        }

        this.value = value;
      });

      // Form submission handler
      $('#kecermatanForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validasi form
        const emptyInputs = $('.karakter-input').filter(function() {
          return !this.value.trim();
        });

        if (emptyInputs.length > 0) {
          alert('Harap isi semua kolom soal terlebih dahulu');
          emptyInputs.first().focus();
          return false;
        }

        // Kumpulkan input dalam urutan yang benar
        const questions = [];
        $('.karakter-input').each(function() {
          questions.push(encodeURIComponent(this.value));
        });

        // Buat query string
        const selectedType = $('.form-select').val();
        const queryString = `jenis=${selectedType}&${questions.map((q, i) => `questions[]=${q}`).join('&')}`;

        // Redirect ke halaman tes
        window.location.href = `${this.action}?${queryString}`;
      });

      // Set placeholder awal saat halaman dimuat
      updatePlaceholders($('.form-select').val());
    });
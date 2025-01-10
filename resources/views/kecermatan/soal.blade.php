@extends('layouts.app')

@push('styles')
  <style>
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
      border: 1px solid #333;
      text-align: center;
      padding: 0.75rem;
    }

    .karakter {
      font-size: 2rem;
      font-weight: bold;
      color: #444;
    }

    .label {
      font-size: 1.2rem;
      color: #666;
    }

    .answer-container {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-bottom: 2rem;
    }

    .answer-box {
      width: 60px;
      height: 60px;
      border: 1px solid #333;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 1.5rem;
      font-weight: bold;
    }

    .options {
      display: flex;
      justify-content: center;
      gap: 10px;
    }

    .option-btn {
      width: 100px;
      height: 50px;
      background-color: white;
      border: none;
      font-size: 1.2rem;
      transition: all 0.2s;
    }

    .option-btn:hover {
      background-color: #18a689;
    }

    .title-row {
      background-color: white;
      font-weight: bold;
      font-size: 1.2rem;
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
    let currentSet = 0;
    let kolomMerah = [];
    let kolomBiru = [];
    let hurufHilang;
    let waktuTersisa = 60;
    let skorBenar = 0;
    let skorSalah = 0;
    let timerInterval;
    let detailJawaban = [];
    let totalSets = 10;
    let allQuestions = []; // Will store all questions from the input form

    // Get questions from URL parameters (sent from the input form)
    function getQuestionsFromURL() {
      const urlParams = new URLSearchParams(window.location.search);
      const questions = urlParams.getAll('questions[]');
      allQuestions = questions.map((chars, index) => {
        return Array.from(chars).map((char, i) => ({
          huruf: char,
          opsi: String.fromCharCode(65 + i) // A, B, C, D, E
        }));
      });
    }

    async function getNextSoal() {
      try {
        currentSet++;
        if (currentSet > totalSets) {
          return true;
        }

        // Get the questions for current set
        kolomMerah = allQuestions[currentSet - 1] || [];
        document.getElementById('current-set').textContent = currentSet;

        return false;
      } catch (error) {
        console.error('Error getting next question:', error);
        return true;
      }
    }

    function acakArray(array) {
      const arrayBaru = [...array];
      for (let i = arrayBaru.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arrayBaru[i], arrayBaru[j]] = [arrayBaru[j], arrayBaru[i]];
      }
      return arrayBaru;
    }

    function generateKolomBiru() {
      const shuffled = acakArray(kolomMerah);
      kolomBiru = shuffled.slice(0, 4);
      hurufHilang = shuffled[4];
      updateKolomBiru();
    }

    function updateKolomMerah() {
      const row = document.getElementById('kolom-merah');
      row.innerHTML = '';
      kolomMerah.forEach(item => {
        const td = document.createElement('td');
        td.className = 'karakter';
        td.textContent = item.huruf;
        row.appendChild(td);
      });
    }

    function updateKolomBiru() {
      const container = document.getElementById('kolom-biru');
      container.innerHTML = '';
      kolomBiru.forEach(item => {
        const div = document.createElement('div');
        div.className = 'answer-box';
        div.textContent = item.huruf;
        container.appendChild(div);
      });
    }

    function mulaiTimer() {
      clearInterval(timerInterval);
      waktuTersisa = 60;
      document.getElementById('timer').textContent = waktuTersisa;

      timerInterval = setInterval(async () => {
        if (waktuTersisa > 0) {
          waktuTersisa--;
          document.getElementById('timer').textContent = waktuTersisa;
        } else {
          clearInterval(timerInterval);
          if (currentSet < totalSets) {
            const isLastSet = await transisiKeSetBerikutnya();
            if (isLastSet) {
              selesaiTes();
            }
          } else {
            selesaiTes();
          }
        }
      }, 1000);
    }

    async function transisiKeSetBerikutnya() {
      clearInterval(timerInterval);

      if (currentSet >= totalSets) {
        return true;
      }

      return new Promise((resolve) => {
        let countdownInterval;
        Swal.fire({
          title: 'Persiapan Soal Berikutnya',
          html: 'Soal berikutnya akan dimulai dalam <b></b> detik.',
          timer: 5000,
          timerProgressBar: true,
          allowOutsideClick: false,
          didOpen: () => {
            const timer = Swal.getPopup().querySelector('b');
            countdownInterval = setInterval(() => {
              timer.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
            }, 100);
          },
          willClose: () => {
            clearInterval(countdownInterval);
          }
        }).then(async () => {
          const isLast = await getNextSoal();
          if (!isLast) {
            updateKolomMerah();
            generateKolomBiru();
            mulaiTimer();
          }
          resolve(isLast);
        });
      });
    }

    function selesaiTes() {
      clearInterval(timerInterval);

      const appUrl = "{{ $appUrl }}";
      if (!appUrl) {
        console.error('APP URL not configured');
        return;
      }

      // Use relative URL
      fetch("{{ $appUrl }}/tes-kecermatan/simpan-hasil", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            // Tambahkan header Accept untuk memastikan response JSON
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            user_id: '{{ auth()->id() }}',
            skor_benar: skorBenar,
            skor_salah: skorSalah,
            waktu_total: (totalSets * 60) - waktuTersisa,
            detail_jawaban: detailJawaban
          })
        })
        .then(response => {
          // Cek status response
          if (!response.ok) {
            // Jika status bukan 2xx, throw error
            return response.text().then(text => {
              throw new Error(`HTTP error! status: ${response.status}, message: ${text}`);
            });
          }
          return response.json();
        })
        .then(result => {
          if (result.success) {
            Swal.fire({
              title: 'Tes Selesai!',
              text: `Skor Benar: ${skorBenar}, Skor Salah: ${skorSalah}`,
              icon: 'success',
              confirmButtonText: 'OK'
            }).then(() => {
              window.location.href = '/tes-kecermatan/hasil';
            });
          }
        })
        .catch(error => {
          console.error('Error saving results:', error);
          Swal.fire({
            title: 'Error!',
            text: `Gagal menyimpan hasil tes: ${error.message}`,
            icon: 'error',
            confirmButtonText: 'OK'
          });
        });
    }

    // Event listeners for buttons
    ['A', 'B', 'C', 'D', 'E'].forEach(huruf => {
      document.getElementById(`btn-${huruf}`).addEventListener('click', () => {
        const isBenar = hurufHilang.opsi === huruf;
        if (isBenar) {
          skorBenar++;
        } else {
          skorSalah++;
        }

        detailJawaban.push({
          set: currentSet,
          jawaban: huruf,
          benar: isBenar,
          waktu: 60 - waktuTersisa
        });

        generateKolomBiru();
      });
    });

    // Initialize
    getQuestionsFromURL();
    getNextSoal().then(() => {
      updateKolomMerah();
      generateKolomBiru();
      mulaiTimer();
    });
  </script>
@endpush

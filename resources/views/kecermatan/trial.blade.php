<!-- resources/views/trial-kecermatan.blade.php -->
<!DOCTYPE html>
<html>

<head>
  <title>Trial Tes Kecermatan</title>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      background-color: #f8f9fa;
      padding: 20px;
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
      padding: 20px;
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
      border: 2px solid #ddd;
      font-size: 1.2rem;
      transition: all 0.2s;
      cursor: pointer;
    }

    .option-btn:hover {
      background-color: #18a689;
      color: white;
    }

    .title-row {
      background-color: white;
      font-weight: bold;
      font-size: 1.2rem;
    }

    .score-display {
      text-align: center;
      margin-bottom: 1rem;
      font-size: 1.2rem;
    }
  </style>
</head>

<body>
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
        <button class="option-btn" id="btn-A">A</button>
        <button class="option-btn" id="btn-B">B</button>
        <button class="option-btn" id="btn-C">C</button>
        <button class="option-btn" id="btn-D">D</button>
        <button class="option-btn" id="btn-E">E</button>
      </div>
    </div>

    {{-- <div class="score-display">
      Benar: <span id="skor-benar">0</span> |
      Salah: <span id="skor-salah">0</span>
    </div> --}}
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      let currentSet = 0;
      let kolomMerah = [];
      let kolomBiru = [];
      let hurufHilang;
      let waktuTersisa = 60;
      let skorBenar = 0;
      let skorSalah = 0;
      let timerInterval;
      let totalSets = 3; // Changed to 3 for trial version

      const karakterSet = {
        huruf: "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
        angka: "0123456789",
        simbol: '!@#$%^&*()_+-=[]{}|;:",.<>?'
      };

      function generateRandomChars() {
        const chars = [];
        const usedChars = new Set(); // Keep track of used characters

        // Keep generating until we have 5 unique characters
        while (chars.length < 5) {
          const randomType = Math.floor(Math.random() * 3);
          let sourceString;

          switch (randomType) {
            case 0:
              sourceString = karakterSet.huruf;
              break;
            case 1:
              sourceString = karakterSet.angka;
              break;
            case 2:
              sourceString = karakterSet.simbol;
              break;
          }

          const randomIndex = Math.floor(Math.random() * sourceString.length);
          const newChar = sourceString[randomIndex];

          // Only add if this character hasn't been used yet
          if (!usedChars.has(newChar)) {
            usedChars.add(newChar);
            chars.push({
              huruf: newChar,
              opsi: String.fromCharCode(65 + chars.length)
            });
          }
        }
        return chars;
      }

      function getNextSoal() {
        currentSet++;
        if (currentSet > totalSets) {
          return true;
        }

        kolomMerah = generateRandomChars();
        const currentSetElement = document.getElementById("current-set");
        if (currentSetElement) {
          currentSetElement.textContent = currentSet;
        }

        return false;
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
        const row = document.getElementById("kolom-merah");
        if (!row) return;

        row.innerHTML = "";
        kolomMerah.forEach((item) => {
          const td = document.createElement("td");
          td.className = "karakter";
          td.textContent = item.huruf;
          row.appendChild(td);
        });
      }

      function updateKolomBiru() {
        const container = document.getElementById("kolom-biru");
        if (!container) return;

        container.innerHTML = "";
        kolomBiru.forEach((item) => {
          const div = document.createElement("div");
          div.className = "answer-box";
          div.textContent = item.huruf;
          container.appendChild(div);
        });
      }

      function updateScore() {
        document.getElementById("skor-benar").textContent = skorBenar;
        document.getElementById("skor-salah").textContent = skorSalah;
      }

      function mulaiTimer() {
        clearInterval(timerInterval);
        waktuTersisa = 60;
        const timerElement = document.getElementById("timer");
        if (timerElement) {
          timerElement.textContent = waktuTersisa;
        }

        timerInterval = setInterval(async () => {
          if (waktuTersisa > 0) {
            waktuTersisa--;
            if (timerElement) {
              timerElement.textContent = waktuTersisa;
            }
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
            title: "Persiapan Soal Berikutnya",
            html: "Soal berikutnya akan dimulai dalam <b></b> detik.",
            timer: 5000,
            timerProgressBar: true,
            allowOutsideClick: false,
            didOpen: () => {
              const timer = Swal.getPopup().querySelector("b");
              countdownInterval = setInterval(() => {
                timer.textContent = Math.ceil(
                  Swal.getTimerLeft() / 1000
                );
              }, 100);
            },
            willClose: () => {
              clearInterval(countdownInterval);
            },
          }).then(async () => {
            const isLast = getNextSoal();
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
        Swal.fire({
          title: "Tes Selesai!",
          html: `
                        <p>Skor Benar: ${skorBenar}</p>
                        <p>Skor Salah: ${skorSalah}</p>
                        <p>Total Skor: ${skorBenar - skorSalah}</p>
                    `,
          icon: "success",
          confirmButtonText: "OK",
        }).then(() => {
          Swal.fire({
            title: 'Trial Tes Selesai',
            html: 'Ingin melanjutkan tes? Silahkan daftarkan diri anda di <a href="{{ route('register') }}" style="color: blue; text-decoration: underline;">sini</a>',
            showCancelButton: true,
            confirmButtonText: 'Daftar',
            cancelButtonText: 'Tutup',
            reverseButtons: true
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = '{{ route('register') }}';
            }
          });
        });
      }

      function initializeButtons() {
        ["A", "B", "C", "D", "E"].forEach((huruf) => {
          const button = document.getElementById(`btn-${huruf}`);
          if (button) {
            button.addEventListener("click", () => {
              const isBenar = hurufHilang.opsi === huruf;
              if (isBenar) {
                skorBenar++;
              } else {
                skorSalah++;
              }

              updateScore();
              generateKolomBiru();
            });
          }
        });
      }

      // Initialize the game
      getNextSoal();
      updateKolomMerah();
      generateKolomBiru();
      initializeButtons();
      mulaiTimer();
    });
  </script>
</body>

</html>

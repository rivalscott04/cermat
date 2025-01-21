<!DOCTYPE html>
<html>

<head>
  <title>Trial Tes Kecermatan</title>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      background-color: #FFF;
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

    .karakter {
      font-size: 2.2rem;
      font-weight: bold;
      color: #444;
    }

    .label {
      font-size: 1.6rem;
      color: #666;
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
      font-size: 1.8rem;
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
      cursor: pointer;
      transition: all 0.2s;
    }

    .option-btn:hover {
      background-color: #18a689;
      color: white;
    }

    .title-row {
      background-color: white;
      font-weight: bold;
      font-size: 1.8rem;
    }

    .score-display {
      text-align: center;
      margin-bottom: 1rem;
      font-size: 1.8rem;
      display: none;
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
</head>

<body>
  <div class="container">
    <div class="timer" id="timer">20</div>

    <div class="question-container">
      <table class="soal-table">
        <tr class="title-row">
          <td colspan="5">Soal <span id="current-set">1</span></td>
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

    <div class="score-display">
      Benar: <span id="skor-benar">0</span> |
      Salah: <span id="skor-salah">0</span>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      let currentSet = 0;
      let kolomMerah = [];
      let kolomBiru = [];
      let hurufHilang;
      let waktuTersisa = 20;
      let skorBenar = 0;
      let skorSalah = 0;
      let timerInterval;
      let totalSets = 3;
      let buttonsEnabled = true;

      const karakterSet = {
        huruf: "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
        angka: "0123456789",
        simbol: 'αβγδεζηθικλμνξοπρςστυφχψωΓΔΘΛΣΦΨΩ'
      };

      function generateRandomChars(type) {
        const chars = [];
        const usedChars = new Set();
        let sourceString;

        switch (type) {
          case 'huruf':
            sourceString = karakterSet.huruf;
            break;
          case 'angka':
            sourceString = karakterSet.angka;
            break;
          case 'simbol':
            sourceString = karakterSet.simbol;
            break;
        }

        while (chars.length < 5) {
          const randomIndex = Math.floor(Math.random() * sourceString.length);
          const newChar = sourceString[randomIndex];

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

        let type;
        switch (currentSet) {
          case 1:
            type = 'angka';
            break;
          case 2:
            type = 'huruf';
            break;
          case 3:
            type = 'simbol';
            break;
        }

        kolomMerah = generateRandomChars(type);
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

      function generateNewQuestion() {
        kolomMerah = generateRandomChars(currentSet === 1 ? 'angka' : currentSet === 2 ? 'huruf' : 'simbol');
        updateKolomMerah();
        generateKolomBiru();
      }

      async function transisiKeSetBerikutnya() {
        clearInterval(timerInterval);
        buttonsEnabled = true;

        if (currentSet >= totalSets) {
          return true;
        }

        return new Promise((resolve) => {
          let countdownInterval;
          Swal.fire({
            title: "Persiapan Soal Berikutnya",
            html: "Soal berikutnya akan dimulai dalam <b></b> detik.",
            timer: 3000,
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

      function mulaiTimer() {
        clearInterval(timerInterval);
        waktuTersisa = 20;
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

      function selesaiTes() {
        clearInterval(timerInterval);
        buttonsEnabled = false;
        Swal.fire({
          title: "Tes Selesai!",
          html: `
            <div style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 1rem;">
              <div>
                <span class="score-badge benar">Benar: ${skorBenar}</span>
              </div>
              <div>
                <span class="score-badge salah">Salah: ${skorSalah}</span>
              </div>
            </div>
            <div style="margin-top: 1rem;">
              <strong>Total Skor: ${skorBenar - skorSalah}</strong>
            </div>
          `,
          icon: "success",
          confirmButtonText: "OK",
        }).then(() => {
          Swal.fire({
            title: 'Trial Tes Selesai',
            html: 'Ingin melanjutkan tes? Silahkan daftarkan diri anda di <a href="{{ route('register') }}" style="color: blue; text-decoration: underline;">sini</a>',
            showCancelButton: true,
            confirmButtonText: 'Beranda',
            cancelButtonText: 'Tutup',
            reverseButtons: true
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = '/';
            }
          });
        });
      }

      function initializeButtons() {
        ["A", "B", "C", "D", "E"].forEach((huruf) => {
          const button = document.getElementById(`btn-${huruf}`);
          if (button) {
            button.addEventListener("click", () => {
              if (!buttonsEnabled) return;

              const isBenar = hurufHilang.opsi === huruf;

              if (isBenar) {
                skorBenar++;
              } else {
                skorSalah++;
              }

              // Hanya mengacak ulang kolom biru
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

class KecermatanSoal {
    constructor(config) {
        this.currentSet = 0;
        this.kolomMerah = [];
        this.kolomBiru = [];
        this.hurufHilang = null;
        this.waktuTersisa = 60;
        this.skorBenar = 0;
        this.skorSalah = 0;
        this.timerInterval = null;
        this.detailJawaban = [];
        this.totalSets = 10;
        this.routes = config.routes;
        this.userId = config.userId;
        this.csrfToken = config.csrfToken;

        console.log("Game initialized with config:", config);
        this.initializeEventListeners();
    }

    async getNextSoal() {
        try {
            console.log("Fetching next soal from:", this.routes.nextSoal);
            const response = await fetch(this.routes.nextSoal, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.csrfToken,
                },
                body: JSON.stringify({
                    current_set: this.currentSet,
                }),
            });
            const data = await response.json();
            console.log("Received data:", data);

            if (data.success) {
                this.kolomMerah = data.data.soal;
                this.currentSet = data.data.set_number;
                console.log("Updated kolomMerah:", this.kolomMerah);
                return this.currentSet > this.totalSets;
            }
            return true;
        } catch (error) {
            console.error("Error fetching next soal:", error);
            return true;
        }
    }

    acakArray(array) {
        const arrayBaru = [...array];
        for (let i = arrayBaru.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [arrayBaru[i], arrayBaru[j]] = [arrayBaru[j], arrayBaru[i]];
        }
        return arrayBaru;
    }

    generateKolomBiru() {
        const shuffled = this.acakArray(this.kolomMerah);
        this.kolomBiru = shuffled.slice(0, 4);
        this.hurufHilang = shuffled[4];
        this.updateKolomBiru();
    }

    updateKolomMerah() {
        console.log("Updating kolom merah with:", this.kolomMerah);
        const kolomMerahContainer = document.getElementById("kolom-merah");
        if (!kolomMerahContainer) {
            console.error("kolom-merah container not found!");
            return;
        }
        kolomMerahContainer.innerHTML = "";
        this.kolomMerah.forEach((item) => {
            console.log("Creating element for item:", item);
            const kolom = document.createElement("div");
            kolom.className = "kolom card";
            kolom.innerHTML = `
              <div class="card-body">
                  <div class="huruf">${item.huruf}</div>
                  <div class="opsi">${item.opsi}</div>
              </div>
          `;
            kolomMerahContainer.appendChild(kolom);
        });
    }

    updateKolomBiru() {
        console.log("Updating kolom biru with:", this.kolomBiru);
        const kolomBiruContainer = document.getElementById("kolom-biru");
        if (!kolomBiruContainer) {
            console.error("kolom-biru container not found!");
            return;
        }
        kolomBiruContainer.innerHTML = "";
        this.kolomBiru.forEach((item) => {
            const kolom = document.createElement("div");
            kolom.className = "kolom card";
            kolom.innerHTML = `
              <div class="card-body">
                  <div class="huruf">${item.huruf}</div>
              </div>
          `;
            kolomBiruContainer.appendChild(kolom);
        });
    }

    resetTimer() {
        clearInterval(this.timerInterval);
        this.waktuTersisa = 60;
        document.getElementById("timer").textContent = this.waktuTersisa;
    }

    mulaiTimer() {
        this.resetTimer();
        this.timerInterval = setInterval(async () => {
            if (this.waktuTersisa > 0) {
                this.waktuTersisa--;
                document.getElementById("timer").textContent =
                    this.waktuTersisa;
            } else {
                clearInterval(this.timerInterval);
                if (this.currentSet < this.totalSets) {
                    const isLastSet = await this.transisiKeSetBerikutnya();
                    if (isLastSet) {
                        this.selesaiTes();
                    }
                } else {
                    this.selesaiTes();
                }
            }
        }, 1000);
    }

    async transisiKeSetBerikutnya() {
        clearInterval(this.timerInterval);

        if (this.currentSet >= this.totalSets) {
            return true;
        }

        return new Promise((resolve) => {
            let timerInterval;
            const popup = document.getElementById("popup");
            popup.classList.remove("d-none");
            popup.classList.add("d-block");

            let countdown = 5;
            const countdownElement = document.getElementById("countdown");

            timerInterval = setInterval(async () => {
                countdown--;
                countdownElement.textContent = countdown;

                if (countdown <= 0) {
                    clearInterval(timerInterval);
                    popup.classList.remove("d-block");
                    popup.classList.add("d-none");

                    const isLast = await this.getNextSoal();
                    if (!isLast) {
                        this.updateKolomMerah();
                        this.generateKolomBiru();
                        this.waktuTersisa = 60;
                        document.getElementById("timer").textContent =
                            this.waktuTersisa;
                        this.mulaiTimer();
                    }
                    resolve(isLast);
                }
            }, 1000);
        });
    }

    async selesaiTes() {
        clearInterval(this.timerInterval);

        const hasilElement = document.getElementById("hasil");
        hasilElement.innerHTML = `
          <div class="alert alert-success" role="alert">
              <h4 class="alert-heading">Tes Selesai!</h4>
              <p>Skor Benar: ${this.skorBenar}</p>
              <p>Skor Salah: ${this.skorSalah}</p>
          </div>
      `;

        try {
            const response = await fetch(this.routes.simpanHasil, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.csrfToken,
                },
                body: JSON.stringify({
                    user_id: this.userId,
                    skor_benar: this.skorBenar,
                    skor_salah: this.skorSalah,
                    waktu_total: this.totalSets * 60 - this.waktuTersisa,
                    detail_jawaban: this.detailJawaban,
                }),
            });

            const result = await response.json();
            if (result.success) {
                setTimeout(() => {
                    window.location.href = "/kecermatan/hasil";
                }, 2000);
            }
        } catch (error) {
            console.error("Error saving results:", error);
            hasilElement.innerHTML += `
              <div class="alert alert-danger" role="alert">
                  Gagal menyimpan hasil tes
              </div>
          `;
        }
    }

    initializeEventListeners() {
        ["A", "B", "C", "D", "E"].forEach((huruf) => {
            document
                .getElementById(`btn-${huruf}`)
                .addEventListener("click", () => {
                    const isBenar = this.hurufHilang.opsi === huruf;
                    if (isBenar) {
                        this.skorBenar++;
                    } else {
                        this.skorSalah++;
                    }

                    this.detailJawaban.push({
                        set: this.currentSet,
                        jawaban: huruf,
                        benar: isBenar,
                        waktu: 60 - this.waktuTersisa,
                    });

                    this.generateKolomBiru();
                });
        });
    }

    async init() {
        console.log("Initializing game...");
        await this.getNextSoal();
        console.log("After getNextSoal, kolomMerah:", this.kolomMerah);
        this.updateKolomMerah();
        this.generateKolomBiru();
        this.mulaiTimer();
    }
}

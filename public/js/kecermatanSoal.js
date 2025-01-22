// Wait for DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function () {
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
    let allQuestions = [];

    // Get questions from URL parameters
    function getQuestionsFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        const questions = urlParams.getAll("questions[]");
        allQuestions = questions.map((chars, index) => {
            return Array.from(chars).map((char, i) => ({
                huruf: char,
                opsi: String.fromCharCode(65 + i),
            }));
        });
    }

    async function getNextSoal() {
        try {
            currentSet++;
            if (currentSet > totalSets) {
                return true;
            }

            kolomMerah = allQuestions[currentSet - 1] || [];
            const currentSetElement = document.getElementById("current-set");
            if (currentSetElement) {
                currentSetElement.textContent = currentSet;
            }

            return false;
        } catch (error) {
            console.error("Error getting next question:", error);
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

        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        )?.content;
        const baseUrl = window.location.origin;

        fetch(saveResultsUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            body: JSON.stringify({
                user_id: userId,
                skor_benar: skorBenar,
                skor_salah: skorSalah,
                waktu_total: totalSets * 60 - waktuTersisa,
                detail_jawaban: detailJawaban,
            }),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((result) => {
                if (result.success) {
                    Swal.fire({
                        title: "Tes Selesai!",
                        icon: "success",
                        showCloseButton: true,
                        confirmButtonText: "OK",
                        width: "600px",
                        allowOutsideClick: false,
                    }).then(() => {
                        window.location.href = `${baseUrl}/tes-kecermatan/hasil`;
                    });
                }
            })
            .catch((error) => {
                console.error("Error saving results:", error);
                Swal.fire({
                    title: "Error!",
                    text: "Gagal menyimpan hasil tes",
                    icon: "error",
                    confirmButtonText: "OK",
                });
            });
    }

    // Initialize button event listeners
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

                    detailJawaban.push({
                        set: currentSet,
                        jawaban: huruf,
                        benar: isBenar,
                        waktu: 60 - waktuTersisa,
                        soal_asli: kolomMerah
                            .map((item) => item.huruf)
                            .join(""),
                        soal_acak:
                            kolomBiru.map((item) => item.huruf).join("") +
                            hurufHilang.huruf,
                        huruf_hilang: hurufHilang.huruf,
                        posisi_huruf_hilang: hurufHilang.opsi,
                    });

                    generateKolomBiru();
                });
            }
        });
    }

    // Initialize the game
    function initialize() {
        getQuestionsFromURL();
        getNextSoal().then(() => {
            updateKolomMerah();
            generateKolomBiru();
            mulaiTimer();
        });
    }

    // Set up event listeners and initialize
    initializeButtons();
    initialize();
});

document.addEventListener("DOMContentLoaded", function () {
    // DOM Elements
    const form = document.getElementById("kecermatanForm");
    const karakterType = document.querySelector(".custom-select");
    const karakterBtns = document.querySelectorAll(".karakter-btn");
    const inputs = document.querySelectorAll(".karakter-input");
    const isiOtomatisBtn = document.querySelector(".btn-isi-otomatis");

    // Placeholder examples for each type
    const placeholderExamples = {
        huruf: [
            "ABCDE",
            "FGHIJ",
            "KLMNO",
            "PQRST",
            "UVWXY",
            "ZABCD",
            "EFGHI",
            "JKLMN",
            "OPQRS",
            "TUVWX",
        ],
        angka: [
            "12345",
            "67890",
            "13579",
            "24680",
            "11223",
            "44556",
            "77889",
            "90123",
            "45678",
            "98765",
        ],
        simbol: [
            "!@#$%",
            "&*()_",
            "+=-[]",
            "{}|;:",
            '"<>?/',
            ".,$#@",
            "*&^%$",
            "#@!&*",
            "()_+<",
            ">?,./",
        ],
        acak: [
            "A1@B2",
            "C3#D4",
            "E5$F6",
            "G7%H8",
            "I9*J0",
            "K!L@M",
            "N#O$P",
            "Q%R&S",
            "T*U(V",
            "W)X+Y",
        ],
    };

    // Cache for storing generated character sets
    const karakterCache = {
        huruf: Array(10).fill(null),
        angka: Array(10).fill(null),
        simbol: Array(10).fill(null),
        acak: Array(10).fill(null),
    };

    // Available character sets
    const karakterSet = {
        huruf: "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
        angka: "0123456789",
        simbol: '!@#$%^&*()_+-=[]{}|;:",.<>?',
        get acak() {
            return this.huruf + this.angka + this.simbol;
        },
    };

    // Update placeholders based on selected type
    function updatePlaceholders(type) {
        inputs.forEach((input, index) => {
            input.placeholder = placeholderExamples[type][index];
        });
    }

    // Generate random string with specified type and length
    function generateRandomString(type, length = 5) {
        const chars = karakterSet[type] || karakterSet.huruf;
        let result = "";
        const charsLength = chars.length;

        // Ensure we don't have repeating characters
        const usedIndexes = new Set();

        while (result.length < length) {
            const randomIndex = Math.floor(Math.random() * charsLength);
            if (type === "acak" || !usedIndexes.has(randomIndex)) {
                result += chars.charAt(randomIndex);
                usedIndexes.add(randomIndex);
            }
        }

        return result;
    }

    // Pre-generate characters for all types
    function preGenerateKarakter() {
        ["huruf", "angka", "simbol", "acak"].forEach((type) => {
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
        const emptyInputs = Array.from(inputs).filter(
            (input) => !input.value.trim()
        );
        if (emptyInputs.length > 0) {
            Swal.fire({
                title: "Terjadi Kesalahan",
                text: "Harap isi semua kolom soal terlebih dahulu",
                icon: "warning",
                confirmButtonText: "OK",
            });
            emptyInputs[0].focus();
            return false;
        }
        return true;
    }

    // Form submission handler
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        if (!validateForm()) {
            return;
        }

        // Create array to store inputs in correct order
        const orderedInputs = [];

        // Get all rows
        const rows = form.querySelectorAll(".row");

        // Process first three rows (containing 9 inputs in 3x3 grid)
        const mainGrid = rows[0];
        const columns = mainGrid.querySelectorAll(".col-md-4");

        // Collect inputs horizontally (1,2,3 | 4,5,6 | 7,8,9)
        for (let rowIndex = 0; rowIndex < 3; rowIndex++) {
            for (let colIndex = 0; colIndex < 3; colIndex++) {
                const input =
                    columns[colIndex].querySelectorAll(".karakter-input")[
                        rowIndex
                    ];
                orderedInputs.push(input.value);
            }
        }

        // Add the 10th input separately
        const lastInput = rows[1].querySelector(".karakter-input");
        if (lastInput) {
            orderedInputs.push(lastInput.value);
        }

        // Create query string
        const queryString = orderedInputs
            .map((value) => `questions[]=${encodeURIComponent(value)}`)
            .join("&");
        const selectedType = karakterType.value;
        const fullQueryString = `jenis=${selectedType}&${queryString}`;

        // Redirect to the URL with query parameters
        window.location.href = `${form.action}?${fullQueryString}`;
    });

    // Character type selection handler
    karakterType.addEventListener("change", function () {
        const selectedType = this.value;
        const buttonText =
            selectedType.charAt(0).toUpperCase() + selectedType.slice(1);

        // Update button texts
        karakterBtns.forEach((btn) => {
            btn.textContent = buttonText;
        });

        // Update placeholders
        updatePlaceholders(selectedType);

        // Generate new set for selected type
        refreshCache(selectedType);
    });

    // Individual character buttons handler
    karakterBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            const index = parseInt(this.dataset.index);
            const selectedType = karakterType.value;
            const karakter = getKarakter(selectedType, index);
            const correspondingInput = inputs[index];
            if (correspondingInput) {
                correspondingInput.value = karakter;
            }
        });
    });

    // Auto-fill button handler
    isiOtomatisBtn.addEventListener("click", function () {
        const selectedType = karakterType.value;
        refreshCache(selectedType);
        updateAllInputs(selectedType);
    });

    // Input field validation handler
    inputs.forEach((input) => {
        input.addEventListener("input", function () {
            // Convert to uppercase for letter type
            if (karakterType.value === "huruf") {
                this.value = this.value.toUpperCase();
            }

            // Remove invalid characters based on type
            const selectedType = karakterType.value;
            if (selectedType !== "acak") {
                const validChars = new RegExp(
                    `[${karakterSet[selectedType]}]`,
                    "g"
                );
                this.value = (this.value.match(validChars) || []).join("");
            }

            // Enforce maximum length
            if (this.value.length > 5) {
                this.value = this.value.slice(0, 5);
            }
        });
    });

    // Initialize on page load
    preGenerateKarakter();
    updatePlaceholders("huruf"); // Set initial placeholders
});

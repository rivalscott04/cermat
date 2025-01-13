document.addEventListener("DOMContentLoaded", function () {
    // DOM Elements
    const form = document.getElementById("kecermatanForm");
    const karakterType = document.querySelector(".custom-select");
    const karakterBtns = document.querySelectorAll(".karakter-btn");
    const inputs = document.querySelectorAll(".karakter-input");
    const isiOtomatisBtn = document.querySelector(".btn-isi-otomatis");

    // Placeholder examples for each type remain the same
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

    // Character sets remain the same
    const karakterSet = {
        huruf: "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
        angka: "0123456789",
        simbol: '!@#$%^&*()_+-=[]{}|;:",.<>?',
        get acak() {
            return this.huruf + this.angka + this.simbol;
        },
    };

    // Generate random string function
    function generateRandomString(type, length = 5) {
        const chars = karakterSet[type] || karakterSet.huruf;
        let result = "";
        const charsLength = chars.length;
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

    // Update placeholders based on selected type
    function updatePlaceholders(type) {
        inputs.forEach((input, index) => {
            input.placeholder = placeholderExamples[type][index];
        });
    }

    // Validate form only on submission
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
    // Form submission handler
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        if (!validateForm()) return;

        // Get all inputs in the correct order
        const inputs = form.querySelectorAll(".karakter-input");
        const orderedInputs = Array.from(inputs).map((input) => input.value);

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

        // Automatically fill all inputs when type changes
        inputs.forEach((input) => {
            input.value = generateRandomString(selectedType);
        });
    });

    // Individual character buttons handler - FIXED
    karakterBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            const index = parseInt(this.dataset.index);
            const selectedType = karakterType.value;
            const karakter = generateRandomString(selectedType);
            const correspondingInput = inputs[index];

            if (correspondingInput) {
                correspondingInput.value = karakter;
            }
        });
    });

    // Auto-fill button handler
    isiOtomatisBtn.addEventListener("click", function () {
        const selectedType = karakterType.value;
        inputs.forEach((input) => {
            input.value = generateRandomString(selectedType);
        });
    });

    // Input field validation handler
    inputs.forEach((input) => {
        input.addEventListener("input", function () {
            const selectedType = karakterType.value;

            // Convert to uppercase for letter type
            if (selectedType === "huruf") {
                this.value = this.value.toUpperCase();
            }

            // Remove invalid characters based on type
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

    // Initialize placeholders
    updatePlaceholders("huruf");
});

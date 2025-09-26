document.addEventListener("DOMContentLoaded", function () {
    // DOM Elements
    const form = document.getElementById("kecermatanForm");
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const dropdownButton = document.getElementById("dropdownMenuButton");
    const karakterBtns = document.querySelectorAll(".karakter-btn");
    const inputs = document.querySelectorAll(".karakter-input");
    const isiOtomatisBtn = document.getElementById("isiOtomatisBtn");

    // Check if required elements exist
    if (!form || !dropdownButton || !isiOtomatisBtn) {
        console.log("Some required elements not found, skipping generateSoal.js initialization");
        return;
    }

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
            "αβγδε",
            "ζηθικ",
            "λμνξο",
            "πρςστ",
            "υφχψω",
            "ΓΔΘΛπ",
            "ΣΦΨΩα",
            "βγδεζ",
            "ηθικλ",
            "μνξοπ",
        ],
        acak: [
            "A1αB2",
            "C3βD4",
            "E5γF6",
            "G7δH8",
            "I9εJ0",
            "KζL@M",
            "NηOθP",
            "QιRκS",
            "TλUμV",
            "WνXξY",
        ],
    };

    const karakterSet = {
        huruf: "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
        angka: "0123456789",
        simbol: "αβγδεζηθικλμνξοπρςστυφχψωΓΔΘΛΣΦΨΩ",
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

    // Helper to get selected type from dropdown text with safe fallback
    function getSelectedType() {
        const text = (dropdownButton.textContent || '').trim().toLowerCase();
        const allowed = ['huruf', 'angka', 'simbol', 'acak'];
        return allowed.includes(text) ? text : 'huruf';
    }

    // Ensure form completeness: auto-generate missing inputs instead of blocking
    function ensureCompleteForm() {
        const type = getSelectedType();
        let changed = false;
        Array.from(inputs).forEach((input) => {
            if (!(input.value || '').trim()) {
                input.value = generateRandomString(type);
                changed = true;
            }
            if (input.value.length > 5) {
                input.value = input.value.slice(0, 5);
            }
        });
        return true;
    }

    let currentHoverPlaceholders = [];

    // Dropdown items click handler
    dropdownItems.forEach((item) => {
        if (!item) return; // Skip if item is null
        // Hover handlers
        item.addEventListener("mouseenter", function () {
            const selectedType = this.dataset.value;

            // Store current state before changing
            currentHoverPlaceholders = Array.from(inputs).map((input) => ({
                value: input.value,
                placeholder: input.placeholder,
            }));

            // Update button text temporarily
            const buttonText =
                selectedType.charAt(0).toUpperCase() + selectedType.slice(1);
            dropdownButton.dataset.originalText = dropdownButton.textContent;
            dropdownButton.textContent = buttonText;

            // Update karakter buttons text temporarily
            karakterBtns.forEach((btn) => {
                btn.dataset.originalText = btn.textContent;
                btn.textContent = buttonText;
            });

            // Generate new values for preview
            inputs.forEach((input) => {
                input.dataset.originalValue = input.value;
                input.value = generateRandomString(selectedType);
            });
        });

        item.addEventListener("mouseleave", function () {
            // Only restore if we haven't clicked (no current selection made)
            if (dropdownButton.textContent === "Pilih Jenis") {
                // Restore previous values and text
                inputs.forEach((input) => {
                    input.value = input.dataset.originalValue || "";
                    delete input.dataset.originalValue;
                });

                // Restore button texts
                dropdownButton.textContent =
                    dropdownButton.dataset.originalText;
                delete dropdownButton.dataset.originalText;

                karakterBtns.forEach((btn) => {
                    btn.textContent = btn.dataset.originalText;
                    delete btn.dataset.originalText;
                });
            }
        });

        // Click handler - now keeps the hover-generated values
        item.addEventListener("click", function (e) {
            e.preventDefault();
            const selectedType = this.dataset.value;
            const buttonText =
                selectedType.charAt(0).toUpperCase() + selectedType.slice(1);

            // Update text permanently
            dropdownButton.textContent = buttonText;
            karakterBtns.forEach((btn) => {
                btn.textContent = buttonText;
            });

            // The current values from hover will remain
            // Just clean up the temporary data attributes
            inputs.forEach((input) => {
                delete input.dataset.originalValue;
            });
            delete dropdownButton.dataset.originalText;
            karakterBtns.forEach((btn) => {
                delete btn.dataset.originalText;
            });
        });
    });

    // Form submission handler
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        // Auto-complete missing values and proceed
        ensureCompleteForm();

        // Get all inputs in the correct order
        const inputs = form.querySelectorAll(".karakter-input");
        const orderedInputs = Array.from(inputs).map((input) => input.value);

        // Create query string
        const queryString = orderedInputs
            .map((value) => `questions[]=${encodeURIComponent(value)}`)
            .join("&");
        const selectedType = getSelectedType();
        const fullQueryString = `jenis=${selectedType}&${queryString}`;

        // Redirect to the URL with query parameters
        window.location.href = `${form.action}?${fullQueryString}`;
    });

    // Individual character buttons handler
    karakterBtns.forEach((btn) => {
        if (!btn) return; // Skip if btn is null
        btn.addEventListener("click", function () {
            const index = parseInt(this.dataset.index);
            const selectedType = dropdownButton.textContent.toLowerCase();
            const karakter = generateRandomString(selectedType);
            const correspondingInput = inputs[index];

            if (correspondingInput) {
                correspondingInput.value = karakter;
            }
        });
    });

    // Auto-fill button handler
    isiOtomatisBtn.addEventListener("click", function () {
        const selectedType = dropdownButton.textContent.toLowerCase();
        inputs.forEach((input) => {
            input.value = generateRandomString(selectedType);
        });
    });

    // Input field validation handler
    inputs.forEach((input) => {
        if (!input) return; // Skip if input is null
        input.addEventListener("input", function () {
            const selectedType = dropdownButton.textContent.toLowerCase();

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

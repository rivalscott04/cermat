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
        console.log(
            "Some required elements not found, skipping generateSoal.js initialization"
        );
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

    // Generate random string function - IMPROVED with better randomization
    function generateRandomString(type, length = 5) {
        const chars = karakterSet[type] || karakterSet.huruf;
        let result = "";
        const charsLength = chars.length;

        // For better randomization, especially for multiple calls
        for (let i = 0; i < length; i++) {
            // Use crypto.getRandomValues for better randomness if available
            let randomIndex;
            if (window.crypto && window.crypto.getRandomValues) {
                const array = new Uint32Array(1);
                window.crypto.getRandomValues(array);
                randomIndex = array[0] % charsLength;
            } else {
                // Fallback to Math.random with better seed
                randomIndex = Math.floor(Math.random() * charsLength);
            }
            result += chars.charAt(randomIndex);
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

    // Helper: get selected canonical type from dropdown (data-type or text fallback)
    function getSelectedType() {
        const dataType =
            (dropdownButton &&
                dropdownButton.dataset &&
                dropdownButton.dataset.type) ||
            "";
        const text = ((dropdownButton && dropdownButton.textContent) || "")
            .trim()
            .toLowerCase();
        const allowed = ["huruf", "angka", "simbol", "acak"];
        if (allowed.includes(dataType)) return dataType;
        return allowed.includes(text) ? text : "huruf";
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
            dropdownButton.dataset.type = selectedType; // store canonical value
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

    function navigateToSoal() {
        if (!validateForm()) return;

        const inputs = form.querySelectorAll(".karakter-input");
        const orderedInputs = Array.from(inputs).map((input) => input.value);

        const queryString = orderedInputs
            .map((value) => `questions[]=${encodeURIComponent(value)}`)
            .join("&");
        const selectedType = getSelectedType();
        if (window.DEBUG_KECERMATAN) {
            console.log("[kecermatan] Submit clicked");
            console.log("[kecermatan] Selected type:", selectedType);
            console.log("[kecermatan] Filled inputs:", orderedInputs.length);
        }
        const fullQueryString = `jenis=${selectedType}&${queryString}`;
        const targetUrl = `${form.action}?${fullQueryString}`;
        if (window.DEBUG_KECERMATAN) {
            console.log("[kecermatan] Redirect URL:", targetUrl);
        }
        try {
            window.location.assign(targetUrl);
        } catch (err) {
            if (window.DEBUG_KECERMATAN) {
                console.warn(
                    "[kecermatan] assign() failed, fallback to href",
                    err
                );
            }
            window.location.href = targetUrl;
        }
        setTimeout(function () {
            if (window.location.href.indexOf(targetUrl) === -1) {
                window.location.href = targetUrl;
            }
        }, 0);
    }

    // Form submission handler (capture to outrank other listeners)
    form.addEventListener(
        "submit",
        function (e) {
            e.preventDefault();
            e.stopPropagation();
            navigateToSoal();
        },
        true
    );

    // Direct click handler on the submit button as a backup
    const submitBtn = document.querySelector("#kecermatanForm .btn-mulai-tes");
    if (submitBtn) {
        submitBtn.addEventListener(
            "click",
            function (e) {
                e.preventDefault();
                e.stopPropagation();
                navigateToSoal();
            },
            true
        );
    }

    // Individual character buttons handler
    karakterBtns.forEach((btn) => {
        if (!btn) return; // Skip if btn is null
        btn.addEventListener("click", function (e) {
            // Prevent any form submission or other event bubbling
            e.preventDefault();
            e.stopPropagation();

            const index = parseInt(this.dataset.index);
            const selectedType = getSelectedType();
            const karakter = generateRandomString(selectedType);
            const correspondingInput = inputs[index];

            if (correspondingInput) {
                correspondingInput.value = karakter;
            }
        });
    });

    // IMPROVED Auto-fill button handler - NO ANIMATIONS
    function handleIsiOtomatis(e) {
        // Prevent any form submission or other event bubbling
        e.preventDefault();
        e.stopPropagation();

        const selectedType = getSelectedType();

        console.log("Auto-fill clicked, type:", selectedType);

        // Generate new values for all inputs immediately
        inputs.forEach((input, index) => {
            const newValue = generateRandomString(selectedType);
            input.value = newValue;
            console.log(`Input ${index + 1}: ${newValue}`);
        });
    }

    // Remove any existing listeners first to prevent duplicates
    const newIsiOtomatisBtn = isiOtomatisBtn.cloneNode(true);
    isiOtomatisBtn.parentNode.replaceChild(newIsiOtomatisBtn, isiOtomatisBtn);

    // Add fresh event listeners with high priority
    newIsiOtomatisBtn.addEventListener("click", handleIsiOtomatis, true);

    // Update reference to the new button
    const updatedIsiOtomatisBtn = document.getElementById("isiOtomatisBtn");

    // Input field validation handler
    inputs.forEach((input) => {
        if (!input) return; // Skip if input is null
        input.addEventListener("input", function () {
            const selectedType = getSelectedType();

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

    // Debug info
    console.log("generateSoal.js loaded successfully");
    console.log("Auto-fill button found:", !!updatedIsiOtomatisBtn);
    console.log("Form found:", !!form);
    console.log("Inputs found:", inputs.length);
});

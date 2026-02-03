// validationDewan.js - Handle validation requests and results on dewan page
(function () {
    "use strict";

    // Get pertandinganId from meta tag (don't use const to avoid conflict)
    const pertandinganIdMeta = document.querySelector(
        'meta[name="pertandingan-id"]',
    );
    const pertandinganId = pertandinganIdMeta
        ? pertandinganIdMeta.getAttribute("content")
        : null;

    let listenerSetup = false;
    let retryCount = 0;
    const MAX_RETRIES = 5; // Maximum 5 retries

    /**
     * Send validation request to backend
     */
    window.requestValidation = function (type, team) {
        fetch("/dewan-tanding/request-validation", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify({
                pertandingan_id: pertandinganId,
                validation_type: type,
                team: team,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                console.log("✅ Validation request sent:", data);

                // Close modal
                const modalEl = document.getElementById("exampleModal");
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    }
                }

                // Show confirmation
                // alert(
                //     `Validation request sent for ${type} - ${team}.\nWaiting for juris to vote...`,
                // );

                // Update UI to show pending state
                updateLastValidationDisplay({
                    validation_type: type,
                    team: team,
                    result: "PENDING",
                    status: "pending",
                });
            })
            .catch((error) => {
                console.error("❌ Error sending validation request:", error);
                alert("Failed to send validation request. Please try again.");
            });
    };

    /**
     * Display validation result
     */
    function displayValidationResult(data) {
        updateLastValidationDisplay(data);
    }

    /**
     * Update last validation display UI
     */
    function updateLastValidationDisplay(data) {
        const resultDiv = document.getElementById("last-validation-result");

        if (!resultDiv) return;

        if (!data || data.status === "pending") {
            // Pending state
            resultDiv.innerHTML = `
                <div class="text-warning">
                    <strong>${data?.validation_type || "Request"}</strong> - ${data?.team || ""}
                    <br>
                    <small>⏳ Waiting for juris...</small>
                </div>
            `;
            return;
        }

        // Determine color based on result
        const resultClass =
            data.result === "INVALID"
                ? "danger"
                : data.result === "SAH"
                  ? "success"
                  : data.result === "TIDAK SAH"
                    ? "warning"
                    : "info";

        const teamIcon = data.team === "blue" ? "🟦" : "🟥";

        // Safe conversion to uppercase with default empty string
        const validationType = (data.validation_type || "").toUpperCase();
        const teamName = (data.team || "").toUpperCase();

        resultDiv.innerHTML = `
            <div class="text-${resultClass}">
                <strong>${teamIcon} ${validationType}</strong>
                <br>
                Team: <strong>${teamName}</strong>
                <br>
                Result: <strong>${data.result}</strong>
            </div>
        `;
    }

    /**
     * Load last validation on page load
     */
    function loadLastValidation() {
        if (!pertandinganId) {
            console.warn("⚠️ No pertandingan ID found");
            return;
        }

        fetch(`/dewan-tanding/last-validation/${pertandinganId}`)
            .then((response) => response.json())
            .then((data) => {
                console.log("📋 Last validation loaded:", data);
                if (data) {
                    displayValidationResult(data);
                }
            })
            .catch((error) => {
                console.error("❌ Error loading last validation:", error);
            });
    }

    /**
     * Listen for validation completion events
     */
    function setupValidationListener() {
        if (listenerSetup) {
            return; // Already setup, don't setup again
        }

        if (!window.Echo) {
            retryCount++;
            if (retryCount <= MAX_RETRIES) {
                console.warn(
                    `⏳ Echo not loaded yet, retry ${retryCount}/${MAX_RETRIES}...`,
                );
                setTimeout(setupValidationListener, 1000);
            } else {
                console.error(
                    "❌ Echo failed to load after",
                    MAX_RETRIES,
                    "retries. Real-time updates disabled.",
                );
            }
            return;
        }

        if (!pertandinganId) {
            console.warn("⚠️ No pertandingan ID found");
            return;
        }

        window.Echo.channel(`validation-completed-${pertandinganId}`).listen(
            ".ValidationCompleted",
            (event) => {
                console.log("🎉 Validation completed:", event);
                displayValidationResult(event);

                // Show notification
                // alert(`Validation completed!\nResult: ${event.result}`);
            },
        );

        listenerSetup = true;
        console.log(
            "✅ Validation listener setup on channel:",
            `validation-completed-${pertandinganId}`,
        );
    }

    // Load last validation when page loads
    document.addEventListener("DOMContentLoaded", function () {
        console.log("🚀 Validation Dewan initialized");
        loadLastValidation();
        setupValidationListener();
    });

    // Also try to setup listener immediately if Echo already exists
    if (window.Echo) {
        setupValidationListener();
    }
})();

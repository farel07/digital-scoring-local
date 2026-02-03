// validationJuri.js - Handle validation voting on juri page
(function () {
    "use strict";

    // Get pertandinganId from meta tag (use local scope to avoid conflict)
    const pertandinganIdMeta = document.querySelector(
        'meta[name="pertandingan-id"]',
    );
    const pertandinganId = pertandinganIdMeta
        ? pertandinganIdMeta.getAttribute("content")
        : null;

    let currentValidationRequestId = null;
    let currentValidationData = null;
    let listenerSetup = false;
    let retryCount = 0;
    const MAX_RETRIES = 5; // Maximum 5 retries

    /**
     * Show validation popup to juri
     */
    function showValidationPopup(data) {
        currentValidationRequestId = data.validation_request_id;
        currentValidationData = data;

        const teamIcon = data.team === "blue" ? "🟦" : "🟥";
        const description = `${teamIcon} ${data.description}\n\nPlease vote: SAH, TIDAK SAH, or NETRAL`;

        const descEl = document.getElementById("validation-description");
        if (descEl) {
            descEl.innerText = description;
        }

        // Show modal
        const modalEl = document.getElementById("validationModal");
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl, {
                backdrop: "static",
                keyboard: false,
            });
            modal.show();
        }

        console.log("📩 Validation popup shown:", data);
    }

    /**
     * Submit vote to backend
     */
    window.submitVote = function (vote) {
        if (!currentValidationRequestId) {
            alert("No active validation request");
            return;
        }

        // Disable buttons to prevent double submission
        const buttons = document.querySelectorAll("#validationModal button");
        buttons.forEach((btn) => (btn.disabled = true));

        fetch("/juri-tanding/submit-validation-vote", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify({
                validation_request_id: currentValidationRequestId,
                vote: vote,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                console.log("✅ Vote submitted:", data);

                if (data.status === "already_voted") {
                    alert(data.message);
                    buttons.forEach((btn) => (btn.disabled = false));
                    return;
                }

                // Hide modal
                const modalEl = document.getElementById("validationModal");
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    }
                }

                // Show confirmation
                // alert(`Your vote: ${vote}\n\nWaiting for other juris...`);

                // Reset state
                currentValidationRequestId = null;
                currentValidationData = null;

                // Re-enable buttons for next request
                buttons.forEach((btn) => (btn.disabled = false));
            })
            .catch((error) => {
                console.error("❌ Error submitting vote:", error);
                alert("Failed to submit vote. Please try again.");
                buttons.forEach((btn) => (btn.disabled = false));
            });
    };

    /**
     * Setup validation listener
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

        // Listen for validation requests from dewan
        window.Echo.channel(`validation-request-${pertandinganId}`).listen(
            ".ValidationRequestSent",
            (event) => {
                console.log("📬 Validation request received:", event);
                showValidationPopup(event);
            },
        );

        // Optional: Listen for validation completion to show result
        window.Echo.channel(`validation-completed-${pertandinganId}`).listen(
            ".ValidationCompleted",
            (event) => {
                console.log("🎉 Validation completed (juri received):", event);
            },
        );

        listenerSetup = true;
        console.log(
            "✅ Validation listener setup on channel:",
            `validation-request-${pertandinganId}`,
        );
    }

    // Setup listener when DOM is ready
    document.addEventListener("DOMContentLoaded", function () {
        console.log("🚀 Validation Juri initialized");
        setupValidationListener();
    });

    // Also try to setup listener immediately if Echo already exists
    if (window.Echo) {
        setupValidationListener();
    }
})();

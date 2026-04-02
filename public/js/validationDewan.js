// validationDewan.js - Handle validation requests and live juri voting on dewan page
(function () {
    "use strict";

    // ── Meta ──────────────────────────────────────────────────────────────────
    const pertandinganIdMeta = document.querySelector(
        'meta[name="pertandingan-id"]',
    );
    const pertandinganId = pertandinganIdMeta
        ? pertandinganIdMeta.getAttribute("content")
        : null;

    let listenerSetup = false;
    let retryCount = 0;
    const MAX_RETRIES = 30; // 15 seconds @ 500ms

    // ── Vote state (keyed by juri user_id) ───────────────────────────────────
    // currentVotes: { [userId]: { vote: 'SAH'|'TIDAK SAH'|'NETRAL', slot: 1|2|3 } }
    let currentVotes = {};
    let nextSlot = 1; // next available display slot (1–3)

    // ── Render a single juri card ─────────────────────────────────────────────
    function renderJuriCard(slot, vote) {
        const body = document.getElementById(`juri-vote-body-${slot}`);
        const card = document.getElementById(`juri-vote-card-${slot}`);
        if (!body || !card) {
            console.warn(`⚠️ Card elements not found for slot ${slot}`);
            return;
        }

        let label, border, bg, textColor;

        switch ((vote || "").toUpperCase()) {
            case "SAH":
                label = "SAH";
                border = "3px solid #198754";
                bg = "#d1e7dd";
                textColor = "#0a3622";
                break;
            case "TIDAK SAH":
                label = "TIDAK SAH";
                border = "3px solid #dc3545";
                bg = "#f8d7da";
                textColor = "#58151c";
                break;
            case "NETRAL":
                label = "NETRAL";
                border = "3px solid #6c757d";
                bg = "#e2e3e5";
                textColor = "#41464b";
                break;
            default:
                label = "Menunggu...";
                border = "2px solid #dee2e6";
                bg = "#ffffff";
                textColor = "#6c757d";
        }

        card.style.cssText = `border:${border};background-color:${bg};transition:all 0.3s;`;
        body.innerHTML = `
            <p class="mb-0 mt-1 fw-bold small" style="color:${textColor};">${label}</p>
        `;
    }

    // ── Update vote summary counters ──────────────────────────────────────────
    function updateVoteSummary() {
        let countSah = 0,
            countTidakSah = 0,
            countNetral = 0;

        Object.values(currentVotes).forEach(({ vote }) => {
            switch ((vote || "").toUpperCase()) {
                case "SAH":
                    countSah++;
                    break;
                case "TIDAK SAH":
                    countTidakSah++;
                    break;
                case "NETRAL":
                    countNetral++;
                    break;
            }
        });

        const el = (id) => document.getElementById(id);
        if (el("vote-count-sah")) el("vote-count-sah").textContent = countSah;
        if (el("vote-count-tidak-sah"))
            el("vote-count-tidak-sah").textContent = countTidakSah;
        if (el("vote-count-netral"))
            el("vote-count-netral").textContent = countNetral;
    }

    // ── Handle one incoming vote ──────────────────────────────────────────────
    function handleVote(juriId, vote) {
        juriId = String(juriId);

        // First time we see this juri — assign a slot
        if (!(juriId in currentVotes)) {
            if (nextSlot > 3) {
                console.warn("⚠️ More than 3 juri votes received, ignoring");
                return;
            }
            currentVotes[juriId] = { vote: null, slot: nextSlot++ };
        }

        // Update the vote
        currentVotes[juriId].vote = vote;

        const slot = currentVotes[juriId].slot;
        console.log(
            `🗳️ Juri ${juriId} voted "${vote}" → rendering slot ${slot}`,
        );

        renderJuriCard(slot, vote);
        updateVoteSummary();
    }

    // ── Reset all juri cards to waiting state ─────────────────────────────────
    function resetJuriCards() {
        currentVotes = {};
        nextSlot = 1;
        [1, 2, 3].forEach((s) => renderJuriCard(s, null));
        ["vote-count-sah", "vote-count-tidak-sah", "vote-count-netral"].forEach(
            (id) => {
                const el = document.getElementById(id);
                if (el) el.textContent = "0";
            },
        );
    }

    // ── Reset modal back to form ──────────────────────────────────────────────
    window.resetValidationModal = function () {
        resetJuriCards();

        // Hide final result
        const finalBox = document.getElementById("final-result-box");
        if (finalBox) finalBox.style.display = "none";

        // Reset badge
        const badge = document.getElementById("voting-status-badge");
        if (badge) {
            badge.style.cssText = "background:#ffc107!important;color:#000;";
            badge.textContent = "Menunggu Vote Juri...";
        }

        // Show form, hide voting panel
        const form = document.getElementById("validation-request-form");
        const panel = document.getElementById("validation-voting-panel");
        if (form) form.style.display = "";
        if (panel) panel.style.display = "none";
    };

    // ── Switch modal to voting panel ──────────────────────────────────────────
    function showVotingPanel(type, team) {
        const form = document.getElementById("validation-request-form");
        const panel = document.getElementById("validation-voting-panel");
        if (form) form.style.display = "none";
        if (panel) panel.style.display = "";

        const info = document.getElementById("voting-request-info");
        const teamIcon = team === "blue" ? "🟦" : "🟥";
        if (info)
            info.textContent = `${teamIcon} ${type.toUpperCase()} - Team ${team.toUpperCase()}`;

        // Reset everything
        resetJuriCards();
    }

    // ── Send validation request (keeps modal open) ────────────────────────────
    window.requestValidationAndWait = function (type, team) {
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
                team,
            }),
        })
            .then((r) => r.json())
            .then((data) => {
                console.log("✅ Validation request sent:", data);
                showVotingPanel(type, team);
                updateLastValidationDisplay({
                    validation_type: type,
                    team,
                    result: "PENDING",
                    status: "pending",
                });
            })
            .catch((err) => {
                console.error("❌ Error:", err);
                alert("Gagal mengirim request validasi. Silakan coba lagi.");
            });
    };

    window.requestValidation = window.requestValidationAndWait; // backward compat

    // ── Show final result inside modal ────────────────────────────────────────
    function showFinalResultInModal(data) {
        const badge = document.getElementById("voting-status-badge");
        const finalBox = document.getElementById("final-result-box");
        const finalDisp = document.getElementById("final-result-display");

        if (finalBox) finalBox.style.display = "";

        let resultText, resultBg, resultTextColor, badgeBg, badgeColor;

        switch ((data.result || "").toUpperCase()) {
            case "SAH":
                resultText = "Hasil: SAH";
                resultBg = "#d1e7dd";
                resultTextColor = "#0a3622";
                badgeBg = "#198754";
                badgeColor = "#fff";
                break;
            case "TIDAK SAH":
                resultText = "Hasil: TIDAK SAH";
                resultBg = "#f8d7da";
                resultTextColor = "#58151c";
                badgeBg = "#dc3545";
                badgeColor = "#fff";
                break;
            default:
                resultText = `Hasil: ${data.result || "SELESAI"}`;
                resultBg = "#e2e3e5";
                resultTextColor = "#41464b";
                badgeBg = "#6c757d";
                badgeColor = "#fff";
        }

        if (badge) {
            badge.style.cssText = `background:${badgeBg}!important;color:${badgeColor};`;
            badge.textContent = "Voting Selesai";
        }

        if (finalDisp) {
            finalDisp.style.cssText = `background:${resultBg};color:${resultTextColor};border-radius:8px;padding:12px;font-weight:bold;font-size:1.1rem;text-align:center;`;
            finalDisp.textContent = resultText;
        }
    }

    // ── Sidebar "last validation" display ─────────────────────────────────────
    function updateLastValidationDisplay(data) {
        const resultDiv = document.getElementById("last-validation-result");
        if (!resultDiv) return;

        if (!data || data.status === "pending") {
            resultDiv.innerHTML = `
                <div style="color:#856404;">
                    <strong>${data?.validation_type || "Request"}</strong> - ${data?.team || ""}
                    <br><small>Waiting for juris...</small>
                </div>`;
            return;
        }

        const colorMap = {
            INVALID: "#dc3545",
            SAH: "#198754",
            "TIDAK SAH": "#856404",
        };
        const color = colorMap[(data.result || "").toUpperCase()] || "#0dcaf0";
        const teamIcon = data.team === "blue" ? "🟦" : "🟥";

        resultDiv.innerHTML = `
            <div style="color:${color};">
                <strong>${teamIcon} ${(data.validation_type || "").toUpperCase()}</strong><br>
                Team: <strong>${(data.team || "").toUpperCase()}</strong><br>
                Result: <strong>${data.result}</strong>
            </div>`;
    }

    // ── Load last validation on page load ─────────────────────────────────────
    function loadLastValidation() {
        if (!pertandinganId) return;
        fetch(`/dewan-tanding/last-validation/${pertandinganId}`)
            .then((r) => r.json())
            .then((data) => {
                if (data) updateLastValidationDisplay(data);
            })
            .catch((err) => console.error("❌ loadLastValidation:", err));
    }

    // ── Echo listener setup ───────────────────────────────────────────────────
    function setupValidationListener() {
        if (listenerSetup) return;

        if (!window.Echo) {
            retryCount++;
            if (retryCount <= MAX_RETRIES) {
                console.warn(
                    `⏳ Echo not ready, retry ${retryCount}/${MAX_RETRIES}…`,
                );
                setTimeout(setupValidationListener, 500);
            } else {
                console.error("❌ Echo never loaded. Real-time disabled.");
            }
            return;
        }

        if (!pertandinganId) {
            console.warn("⚠️ No pertandingan ID found");
            return;
        }

        // Per-juri vote events
        window.Echo.channel(`validation-vote-${pertandinganId}`).listen(
            ".ValidationVoteReceived",
            (event) => {
                console.log("🗳️ Raw vote event:", event);

                // ValidationVoteReceived broadcasts as { data: { juri_id, vote, ... } }
                const payload = event.data ?? event;
                const juriId = payload.juri_id;
                const vote = payload.vote; // 'SAH' | 'TIDAK SAH' | 'NETRAL'

                console.log(`🗳️ Parsed: juri_id=${juriId}, vote=${vote}`);

                if (juriId !== undefined && juriId !== null && vote) {
                    handleVote(juriId, vote);
                } else {
                    console.warn(
                        "⚠️ Missing juri_id or vote in payload",
                        payload,
                    );
                }
            },
        );

        // Validation completed event
        window.Echo.channel(`validation-completed-${pertandinganId}`).listen(
            ".ValidationCompleted",
            (event) => {
                console.log("🎉 Validation completed:", event);
                updateLastValidationDisplay(event);

                const panel = document.getElementById(
                    "validation-voting-panel",
                );
                if (panel && panel.style.display !== "none") {
                    showFinalResultInModal(event);
                }
            },
        );

        listenerSetup = true;
        console.log(
            "✅ Validation listeners active. Pertandingan:",
            pertandinganId,
        );
    }

    // ── Init ──────────────────────────────────────────────────────────────────
    document.addEventListener("DOMContentLoaded", function () {
        console.log(
            "🚀 Validation Dewan init. pertandinganId:",
            pertandinganId,
        );
        loadLastValidation();
        setupValidationListener();

        // Reset modal state when closed
        const modalEl = document.getElementById("exampleModal");
        if (modalEl) {
            modalEl.addEventListener("hidden.bs.modal", function () {
                window.resetValidationModal();
            });
        }
    });

    // Also try immediately in case Echo is already ready
    setupValidationListener();
})();

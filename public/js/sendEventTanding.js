// sendEventTanding.js - Dewan penalty actions with DB-persisted limit enforcement

const pertandinganId =
    document
        .querySelector('meta[name="pertandingan-id"]')
        ?.getAttribute("content") || null;

// ── Batas maksimum per jenis penalti ──────────────────────────────────────────
const LIMITS = {
    bina: 2,
    teguran: 2,
    peringatan: 3,
};

// ── State counter per tim (diinisialisasi dari DB saat DOMContentLoaded) ──────
const counters = {
    blue: { bina: 1, teguran: 1, peringatan: 1 },
    red: { bina: 1, teguran: 1, peringatan: 1 },
};

// Jumlah total yang sudah tersimpan di DB (untuk batas tombol)
const dbCounts = {
    blue: { bina: 0, teguran: 0, peringatan: 0 },
    red: { bina: 0, teguran: 0, peringatan: 0 },
};

// ── Render score-value boxes dari data per-round ──────────────────────────────
function renderScoreBoxes(data) {
    ["blue", "red"].forEach((team) => {
        [1, 2, 3].forEach((round) => {
            const roundData = data[team]?.[String(round)] || {};
            ["bina", "teguran", "peringatan", "jatuhan"].forEach((type) => {
                const el = document.getElementById(
                    `penalty-${team}-${round}-${type}`,
                );
                if (!el) return;
                const val = roundData[type] ?? 0;
                el.textContent = val > 0 ? val : "-";
            });
        });
    });
}

// ── Load penalty counts per-round dari DB (untuk score boxes display) ─────────
async function loadPenaltyCountsPerRound() {
    if (!pertandinganId) return;
    try {
        const res = await fetch(
            `/dewan-tanding/penalty-counts-per-round/${pertandinganId}`,
        );
        const data = await res.json();
        renderScoreBoxes(data);
        console.log("✅ Penalty counts per round loaded:", data);
    } catch (err) {
        console.error("❌ Gagal load penalty counts per round:", err);
    }
}

// ── Helper: disable/enable tombol aksi ───────────────────────────────────────
function getActionButton(action, team) {
    const allBtns = document.querySelectorAll("button[onclick]");
    for (const btn of allBtns) {
        const attr = btn.getAttribute("onclick") || "";
        if (attr.includes(`'${action}'`) && attr.includes(`'${team}'`)) {
            return btn;
        }
    }
    return null;
}

function setButtonState(action, team, disabled) {
    const map = { BINA: "bina", TEGURAN: "teguran", PERINGATAN: "peringatan" };
    const btn = getActionButton(action, team);
    if (!btn) return;

    if (disabled) {
        btn.disabled = true;
        btn.classList.add("opacity-50");
        btn.title = `Batas maksimum ${map[action]} (${LIMITS[map[action]]}x) sudah tercapai`;
    } else {
        btn.disabled = false;
        btn.classList.remove("opacity-50");
        btn.title = "";
    }
}

// ── Sync UI setelah load atau setelah setiap aksi ────────────────────────────
function syncButtonStates() {
    const penaltyActions = [
        { action: "BINA", penaltyId: "bina" },
        { action: "TEGURAN", penaltyId: "teguran" },
        { action: "PERINGATAN", penaltyId: "peringatan" },
    ];

    ["blue", "red"].forEach((team) => {
        penaltyActions.forEach(({ action, penaltyId }) => {
            const isAtLimit = dbCounts[team][penaltyId] >= LIMITS[penaltyId];
            setButtonState(action, team, isAtLimit);
        });
    });
}

// ── Load penalty counts total dari DB (untuk limit enforcement) ───────────────
async function loadPenaltyCounts() {
    if (!pertandinganId) return;

    try {
        const response = await fetch(
            `/dewan-tanding/penalty-counts/${pertandinganId}`,
        );
        const data = await response.json();

        ["blue", "red"].forEach((team) => {
            ["bina", "teguran", "peringatan"].forEach((type) => {
                dbCounts[team][type] = data[team]?.[type] ?? 0;
                counters[team][type] = dbCounts[team][type] + 1;
            });
        });

        syncButtonStates();
        console.log("✅ Penalty counts loaded from DB:", dbCounts);
    } catch (error) {
        console.error("❌ Gagal load penalty counts:", error);
    }
}

// ── Fungsi utama kirim aksi ───────────────────────────────────────────────────
function sendAction(action, filter = "blue") {
    if (!["blue", "red"].includes(filter)) {
        console.error("Tim tidak dikenali");
        return;
    }

    let penalty_id = "";
    let value = 0;

    switch (action) {
        case "BINA":
            penalty_id = "bina";
            value = counters[filter].bina;
            break;

        case "TEGURAN":
            penalty_id = "teguran";
            value = counters[filter].teguran;
            break;

        case "PERINGATAN":
            penalty_id = "peringatan";
            value = counters[filter].peringatan;
            break;

        case "JATUH":
            penalty_id = "jatuhan";
            value = 1;
            break;

        default:
            alert("Action tidak dikenali");
            return;
    }

    // ── Frontend guard: cek limit sebelum kirim ───────────────────────────────
    if (penalty_id in LIMITS) {
        if (dbCounts[filter][penalty_id] >= LIMITS[penalty_id]) {
            alert(
                `⚠️ Batas ${action} untuk tim ${filter.toUpperCase()} sudah tercapai (${LIMITS[penalty_id]}x)!`,
            );
            return;
        }
    }

    console.log(`Sending ${action} for ${filter}: Value ${value}`);

    fetch("/dewan/kirim-penalti-tanding", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify({
            pertandingan_id: pertandinganId,
            penalty_id: penalty_id,
            filter: filter,
            value: value,
        }),
    })
        .then((response) => {
            if (response.status === 422) {
                return response.json().then((data) => {
                    console.warn("⚠️ Server limit reached:", data.message);
                    dbCounts[filter][penalty_id] = data.count;
                    syncButtonStates();
                    throw new Error("limit_reached");
                });
            }
            if (!response.ok) throw new Error("Network error");
            return response.json();
        })
        .then((data) => {
            if (!data) return;
            console.log("✅ Success:", data);

            // Update local DB count & counter
            if (penalty_id in LIMITS) {
                dbCounts[filter][penalty_id]++;
                counters[filter][penalty_id]++;
            }

            // Disable tombol jika sudah mencapai limit
            syncButtonStates();

            // Refresh score boxes dari DB (agar per-round akurat)
            loadPenaltyCountsPerRound();
        })
        .catch((error) => {
            if (error.message !== "limit_reached") {
                console.error("❌ Error:", error);
            }
        });
}

// ── Init: load state dari DB saat halaman dibuka ─────────────────────────────
document.addEventListener("DOMContentLoaded", () => {
    loadPenaltyCounts();
    loadPenaltyCountsPerRound();
});

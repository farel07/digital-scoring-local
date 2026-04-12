// PERTANDINGAN_ID is now set globally in penilaian.blade.php from database

// ========================================
// EVENT LISTENER UNTUK PENALTI (Existing)
// ========================================
window.Echo.channel(`kirim-penalti-tanding-${PERTANDINGAN_ID}`).listen(
    ".KirimPenaltiTanding",
    (event) => {
        console.log("Data Penalti Diterima:", event);

        // Destrukturisasi data dari event
        // event format: { penalty_id: "bina", value: 1, filter: "blue", point_deduction: -1, is_disqualified: false }
        const { penalty_id, value, filter, point_deduction, is_disqualified } =
            event;

        // 1. LOGIKA UNTUK MENYALAKAN GAMBAR (BINA/TEGURAN/PERINGATAN)
        // Format ID di blade: id="blue-notif-binaan-1"
        // Kita petakan penalty_id jika namanya berbeda sedikit
        let penaltyType = penalty_id;
        if (penalty_id === "bina") penaltyType = "binaan"; // Menyesuaikan id 'blue-notif-binaan-x'

        const iconId = `${filter}-notif-${penaltyType}-${value}`;
        const iconElement = document.getElementById(iconId);

        if (iconElement) {
            // Berikan filter warna kuning atau background kuning
            // Menggunakan background agar terlihat menyala di sekitar icon
            iconElement.style.backgroundColor = "#ffc107"; // Warna Kuning Bootstrap
            iconElement.style.borderRadius = "10px";
            iconElement.style.boxShadow = "0 0 15px #ffc107";
            iconElement.style.padding = "5px";
        }

        // 2. LOGIKA UNTUK MENGISI TABEL STATISTIK DI BAWAH
        // Format ID di blade: id="stat-blue-teguran1"
        let tableStatId = "";

        if (penalty_id === "jatuhan") {
            tableStatId = `stat-${filter}-jatuhan`;
        } else if (penalty_id === "teguran") {
            tableStatId = `stat-${filter}-teguran${value}`;
        } else if (penalty_id === "peringatan") {
            tableStatId = `stat-${filter}-peringatan${value}`;
        }

        const statElement = document.getElementById(tableStatId);
        if (statElement) {
            // Untuk jatuhan, increment nilai yang ada (+1)
            // Untuk penalti lainnya (Teguran/Peringatan), set ke 1
            if (penalty_id === "jatuhan") {
                const currentValue = parseInt(statElement.innerText) || 0;
                statElement.innerText = currentValue + 1;
            } else {
                statElement.innerText = 1;
            }

            // Berikan highlight sejenak agar user tahu ada perubahan
            statElement.style.transition = "background-color 0.5s";
            let originalBg = statElement.classList.contains("bg-primary")
                ? "blue"
                : "red";
            statElement.style.backgroundColor = "white";
            statElement.style.color = "black";

            setTimeout(() => {
                statElement.style.backgroundColor = "";
                statElement.style.color = "";
            }, 1000);
        }

        // 3. NEW: DEDUCT POINTS FROM TOTAL SCORE
        if (point_deduction && point_deduction !== 0) {
            updateTotalPoinPenalty(filter, point_deduction);
        }

        // 4. NEW: SHOW DISQUALIFICATION IF PERINGATAN 3
        if (is_disqualified) {
            showDisqualification(filter);
        }
    },
);

// ========================================
// EVENT LISTENER UNTUK PUKUL DAN TENDANG
// ========================================
window.Echo.channel(`kirim-poin-tanding-${PERTANDINGAN_ID}`).listen(
    ".KirimPoinTanding",
    (event) => {
        // event format dari backend: { status: "input"/"sah", juri_id: 1-3, filter: "blue"/"red", type: "PUKUL"/"TENDANG", poin: 1/2 }
        const { status, juri_id, filter, type, poin } = event;

        // Konversi type ke lowercase untuk matching dengan ID HTML (pukul/tendang)
        const teknik = type.toLowerCase();

        // --- LOGIKA 1: INPUT MASUK (NYALAKAN INDIKATOR JURI) ---
        if (status === "input") {
            nyalakanJuriIndicator(juri_id, filter, teknik);
        }

        // --- LOGIKA 2: POIN SAH (TAMBAH SKOR + UPDATE STATS) ---
        else if (status === "sah") {
            updateTotalPoin(filter, poin);
            updateStatsTeknik(filter, teknik);
            resetJuriIndicators(filter, teknik);
        }
    },
);

// Fungsi untuk menyalakan indikator juri (background kuning)
function nyalakanJuriIndicator(juriId, tim, teknik) {
    // Format ID di blade: id="blue-notif-juri-1-pukul" atau "red-notif-juri-2-tendang"
    const indicatorId = `${tim}-notif-juri-${juriId}-${teknik}`;
    const indicator = document.getElementById(indicatorId);

    if (indicator) {
        // PENTING: Remove class bg-light agar background-color terlihat
        indicator.classList.remove("bg-light");

        // Nyalakan dengan background kuning
        indicator.style.backgroundColor = "#ffc107"; // Kuning
        indicator.style.transition = "background-color 0.3s";
        indicator.style.boxShadow = "0 0 10px #ffc107";

        // Auto-reset setelah 3 detik jika tidak sah
        setTimeout(() => {
            // Cek apakah masih kuning (belum di-reset oleh poin sah)
            if (indicator.style.backgroundColor === "rgb(255, 193, 7)") {
                indicator.style.backgroundColor = "";
                indicator.style.boxShadow = "";
                // Kembalikan class bg-light
                indicator.classList.add("bg-light");
            }
        }, 3000);
    }
}

// Fungsi untuk update total poin tim
function updateTotalPoin(tim, poin) {
    // Format ID di blade: id="total-point-blue" atau "total-point-red"
    const totalPointId = `total-point-${tim}`;
    const totalElement = document.getElementById(totalPointId);

    if (totalElement) {
        const currentTotal = parseInt(totalElement.innerText) || 0;
        const newTotal = currentTotal + parseInt(poin);
        totalElement.innerText = newTotal;

        // Efek animasi flash
        totalElement.style.transition = "transform 0.3s, color 0.3s";
        totalElement.style.transform = "scale(1.1)";
        totalElement.style.color = "#ffc107"; // Flash kuning

        setTimeout(() => {
            totalElement.style.transform = "scale(1)";
            totalElement.style.color = "";
        }, 300);
    }
}

// Fungsi untuk update stats table (pukulan/tendangan)
function updateStatsTeknik(tim, teknik) {
    // Format ID di blade: id="stat-blue-pukul" atau "stat-red-tendang"
    const statsId = `stat-${tim}-${teknik}`;
    const statsElement = document.getElementById(statsId);

    if (statsElement) {
        const currentValue = parseInt(statsElement.innerText) || 0;
        statsElement.innerText = currentValue + 1;

        // Highlight sejenak
        statsElement.style.transition = "background-color 0.5s";
        statsElement.style.backgroundColor = "white";
        statsElement.style.color = "black";

        setTimeout(() => {
            statsElement.style.backgroundColor = "";
            statsElement.style.color = "";
        }, 1000);
    }
}

// Fungsi untuk reset semua indikator juri setelah poin sah
function resetJuriIndicators(tim, teknik) {
    // Reset semua juri (1, 2, 3) untuk teknik tertentu
    setTimeout(() => {
        for (let i = 1; i <= 3; i++) {
            const indicatorId = `${tim}-notif-juri-${i}-${teknik}`;
            const indicator = document.getElementById(indicatorId);
            if (indicator) {
                indicator.style.backgroundColor = "";
                indicator.style.boxShadow = "";
                // Kembalikan class bg-light
                indicator.classList.add("bg-light");
            }
        }
    }, 500); // Delay 500ms biar keliatan dulu lampunya nyala
}

// ========================================
// NEW FUNCTIONS FOR PENALTY POINT DEDUCTION
// ========================================

// Fungsi untuk mengurangi poin saat penalty diberikan
function updateTotalPoinPenalty(tim, pointDeduction) {
    const totalPointId = `total-point-${tim}`;
    const totalElement = document.getElementById(totalPointId);

    if (totalElement) {
        const currentTotal = parseInt(totalElement.innerText) || 0;
        const newTotal = currentTotal + pointDeduction; // Can be positive (jatuhan) or negative (penalty)
        totalElement.innerText = newTotal; // Allow negative scores

        // Flash effect - green for positive points (jatuhan), red for negative (penalty)
        totalElement.style.transition = "transform 0.3s, color 0.3s";
        totalElement.style.transform = "scale(1.1)";

        if (pointDeduction > 0) {
            // Positive points (jatuhan) - green flash
            totalElement.style.color = "#28a745"; // Green flash
        } else {
            // Negative points (penalties) - red flash
            totalElement.style.color = "#dc3545"; // Red flash
        }

        setTimeout(() => {
            totalElement.style.transform = "scale(1)";
            totalElement.style.color = "";
        }, 300);
    }
}

// Fungsi untuk menampilkan disqualification (Peringatan 3)
function showDisqualification(tim) {
    const totalPointId = `total-point-${tim}`;
    const totalElement = document.getElementById(totalPointId);

    if (totalElement) {
        // Set nilai menjadi 0 terlebih dahulu (karena -15)
        const currentTotal = parseInt(totalElement.innerText) || 0;
        totalElement.innerText = Math.max(0, currentTotal);

        // Tunggu sebentar lalu tampilkan DQ
        setTimeout(() => {
            totalElement.innerText = "DQ"; // Show DQ instead of score
            totalElement.style.color = "#dc3545";
            totalElement.style.fontWeight = "900";
            totalElement.style.fontSize = "clamp(80px, 18vw, 180px)"; // Slightly larger

            // Add pulsing animation for emphasis
            totalElement.style.animation = "pulse 1s ease-in-out 3";
        }, 500);
    }
}

// Add CSS animation for pulse effect
const style = document.createElement("style");
style.textContent = `
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
`;
document.head.appendChild(style);

// ========================================
// REAL-TIME TIMER SYNCHRONIZATION
// ========================================

// Global timer state
let timerInterval = null;
let currentTimerSeconds = 120; // Default 2 minutes
let totalDuration = 120;
let timerRunning = false;

// Timer display element
const timerElement = document.getElementById("timer");

// Function to format seconds to MM:SS
function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${String(mins).padStart(2, "0")}:${String(secs).padStart(2, "0")}`;
}

// Function to update timer display
function updateTimerDisplay() {
    if (timerElement) {
        timerElement.textContent = formatTime(currentTimerSeconds);
    }
}

// Function to start countdown
function startCountdown() {
    if (timerInterval) return; // Already running

    timerRunning = true;
    timerInterval = setInterval(() => {
        if (currentTimerSeconds > 0) {
            currentTimerSeconds--;
            updateTimerDisplay();
        } else {
            // Timer reached 0
            stopCountdown();
        }
    }, 1000);
}

// Function to stop countdown
function stopCountdown() {
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
    timerRunning = false;
}

// Function to reset timer
function resetTimer(seconds) {
    stopCountdown();
    currentTimerSeconds = seconds;
    totalDuration = seconds;
    updateTimerDisplay();
}

// Listen for timer updates from timer_tanding
window.Echo.channel(`timer-${PERTANDINGAN_ID}`).listen(
    ".TimerUpdated",
    (event) => {
        console.log("Timer Update Received:", event);

        const { state, current_time, total_duration, current_round } = event;

        // Handle different timer states
        if (state === "playing") {
            // Start/resume countdown
            currentTimerSeconds = current_time;
            totalDuration = total_duration;
            updateTimerDisplay();
            startCountdown();
            console.log("✅ Timer started/resumed");
        } else if (state === "paused") {
            // Pause countdown
            currentTimerSeconds = current_time;
            stopCountdown();
            updateTimerDisplay();
            console.log("⏸️ Timer paused");
        } else if (state === "reset") {
            // Reset to initial time
            resetTimer(current_time);
            console.log("🔄 Timer reset");
        } else if (state === "round_changed") {
            // Round changed - highlight the new round
            if (current_round) {
                highlightRound(current_round);
                console.log(`🔵 Round changed to ${current_round}`);
            }
        }
    },
);

// Function to highlight current round
function highlightRound(roundNumber) {
    // Gunakan MAX_RONDE dari global var (di-set oleh penilaian.blade.php),
    // fallback ke 3 jika tidak tersedia (misal di halaman lain)
    const maxRonde = (typeof MAX_RONDE !== 'undefined') ? MAX_RONDE : 3;

    // Reset all rounds to default (bg-light)
    for (let i = 1; i <= maxRonde; i++) {
        const roundBox = document.getElementById(`round-box-${i}`);
        if (roundBox) {
            roundBox.classList.remove("bg-warning");
            roundBox.classList.add("bg-light");
        }
    }

    // Highlight current round (bg-warning)
    const currentRoundBox = document.getElementById(`round-box-${roundNumber}`);
    if (currentRoundBox) {
        currentRoundBox.classList.remove("bg-light");
        currentRoundBox.classList.add("bg-warning");

        // Add pulse animation
        currentRoundBox.style.animation = "pulse 0.5s ease-in-out 2";
        setTimeout(() => {
            currentRoundBox.style.animation = "";
        }, 1000);
    }
}

console.log("✅ Timer listener initialized for pertandingan:", PERTANDINGAN_ID);

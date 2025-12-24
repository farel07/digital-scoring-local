// public/js/scoreboardListener.js

// =======================================================
// KONFIGURASI & STATE MANAGEMENT
// =======================================================
const PERTANDINGAN_ID = 1;
const juriScores = {}; // Menyimpan skor dari semua juri
const penaltyStatus = {}; // Menyimpan semua penalti aktif

// =======================================================
// FUNGSI UNTUK SKOR JURI
// =======================================================
function initializeJuriData(juriId) {
    if (!juriScores[juriId]) {
        juriScores[juriId] = { correctness: 9.90, flow: 0.00 };
    }
}

function updateJuriDisplay(juriId) {
    const juriNumber = juriId.split('-')[1];
    if (!juriNumber) return;

    const panelCells = document.querySelectorAll(`.panel-${juriNumber}`);
    if (panelCells.length < 3) return;

    const [correctnessCell, flowCell, totalCell] = panelCells;
    const latestScores = juriScores[juriId];
    const totalScore = latestScores.correctness + latestScores.flow;

    correctnessCell.textContent = latestScores.correctness.toFixed(2);
    flowCell.textContent = latestScores.flow.toFixed(2);
    totalCell.textContent = totalScore.toFixed(2);
    
    // Efek highlight kuning
    correctnessCell.classList.add('highlight-score');
    setTimeout(() => correctnessCell.classList.remove('highlight-score'), 1000);
}

// =======================================================
// FUNGSI UNTUK PENALTI
// =======================================================
function renderPenaltyDisplay() {
    const penaltyDetailsBody = document.getElementById('penalty-details');
    const totalPenaltyScoreEl = document.getElementById('total-penalty-score');
    if (!penaltyDetailsBody || !totalPenaltyScoreEl) return;

    let totalPenalty = 0;
    penaltyDetailsBody.innerHTML = '';
    const activePenalties = Object.entries(penaltyStatus);

    if (activePenalties.length === 0) {
        penaltyDetailsBody.innerHTML = `<tr><td colspan="2" class="p-4 text-center text-gray-500 italic">Belum ada penalti</td></tr>`;
    } else {
        activePenalties.forEach(([name, value]) => {
            totalPenalty += value;
            const tr = document.createElement('tr');
            const displayName = name.replace(/_/g, ' ').toUpperCase();
            tr.innerHTML = `
                <td class="border border-gray-300 p-2 text-xs text-gray-700">${displayName}</td>
                <td class="border border-gray-300 p-1 text-center text-sm text-red-600 font-bold">${value.toFixed(2)}</td>`;
            penaltyDetailsBody.appendChild(tr);
        });
    }
    totalPenaltyScoreEl.textContent = totalPenalty.toFixed(2);
}

// =======================================================
// FUNGSI UNTUK STATISTIK & SKOR AKHIR
// =======================================================
function calculateMedian(numbers) {
    if (numbers.length === 0) return 0;
    const sorted = [...numbers].sort((a, b) => a - b);
    const mid = Math.floor(sorted.length / 2);
    return sorted.length % 2 !== 0 ? sorted[mid] : (sorted[mid - 1] + sorted[mid]) / 2;
}

function calculateStdDeviation(numbers) {
    if (numbers.length < 2) return 0;
    const mean = numbers.reduce((a, v) => a + v, 0) / numbers.length;
    const variance = numbers.reduce((a, v) => a + (v - mean) ** 2, 0) / numbers.length;
    return Math.sqrt(variance);
}

function updateAndDisplayStatistics() {
    const allTotalScores = Object.values(juriScores).map(s => s.correctness + s.flow);
    
    const median = calculateMedian(allTotalScores);
    const stdDeviation = calculateStdDeviation(allTotalScores);
    const totalPenalty = Object.values(penaltyStatus).reduce((sum, val) => sum + val, 0);
    const finalScore = median + totalPenalty;

    document.getElementById('median-score').textContent = median.toFixed(2);
    document.getElementById('final-score-before-penalty').textContent = median.toFixed(2);
    document.getElementById('std-deviation-score').textContent = stdDeviation.toFixed(6);
    document.getElementById('final-score-with-penalty').textContent = finalScore.toFixed(2);
    document.getElementById('final-score-breakdown').textContent = `Skor (${median.toFixed(2)}) - Penalti (${Math.abs(totalPenalty).toFixed(2)})`;
}

// =======================================================
// LISTENER REAL-TIME
// =======================================================

// Listener untuk skor juri
window.Echo.channel(`kirim-poin-seni-tr-${PERTANDINGAN_ID}`)
    .listen('KirimPoinSeniTR', (event) => { // Menggunakan titik (.) di depan nama Event
        
        console.log("Pesan diterima:", event);

        // Pastikan event memiliki data yang dibutuhkan
        if (!event.role || !event.poin) {
            console.error("Event tidak memiliki properti 'role' atau 'poin'.", event);
            return;
        }

        const juriId = event.role;
        const poinValue = parseFloat(event.poin);

        if (isNaN(poinValue)) {
            console.error("Nilai 'poin' tidak valid:", event.poin);
            return;
        }

        // Pastikan "slot" data untuk juri ini sudah ada
        initializeJuriData(juriId);

        // Lakukan pengurangan skor di 'memori' kita
        // CATATAN: Kode ini mengasumsikan event.poin selalu untuk 'correctness'.
        // Jika ada poin untuk 'flow', Anda perlu menambahkan logika 'if' di sini.
        juriScores[juriId].correctness += poinValue;
        
        // Perbarui tampilan di kolom juri yang bersangkutan
        updateJuriDisplay(juriId);

        // Hitung ulang dan perbarui seluruh statistik
        updateAndDisplayStatistics();
    });

// Listener untuk penalti
window.Echo.channel(`kirim-penalti-${PERTANDINGAN_ID}`)
    .listen('.KirimPenalti', (event) => {
        console.log('Event Penalti Diterima:', event);
        if (!event.penalty_id) return;

        if (event.value === 0) {
            delete penaltyStatus[event.penalty_id];
        } else {
            penaltyStatus[event.penalty_id] = event.value;
        }
        
        renderPenaltyDisplay();
        updateAndDisplayStatistics(); // Update skor akhir setelah penalti berubah
    });

console.log(`Berhasil terhubung dan mendengarkan di channel pertandingan ID: ${PERTANDINGAN_ID}`);
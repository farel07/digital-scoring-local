// sendEventJuriTanding.js — Score log berbasis DB (tanpa localStorage)

const pertandinganId = document.querySelector('meta[name="pertandingan-id"]')?.getAttribute('content') || null;
const juriId         = document.querySelector('meta[name="user-id"]')?.getAttribute('content') || null;

// In-memory score log: { blue: { 1: ['1','2',...], 2: [], 3: [] }, red: {...} }
let scoreLog = {
    blue: { 1: [], 2: [], 3: [] },
    red:  { 1: [], 2: [], 3: [] },
};

let currentRound = 1;
var poin;

// ── Map technique → digit display ────────────────────────────────────────────
function techniqueToDigit(type) {
    return type === 'TENDANG' ? '2' : '1';
}

// ── Render satu round box ────────────────────────────────────────────────────
function renderRound(team, round) {
    const inner = document.getElementById(`score-log-${team}-inner-${round}`);
    if (!inner) return;
    const arr = scoreLog[team][Number(round)];
    inner.textContent = arr ? arr.join('') : '';
}

// ── Render semua 6 box ───────────────────────────────────────────────────────
function renderAllRounds() {
    ['blue', 'red'].forEach(team => {
        [1, 2, 3].forEach(r => renderRound(team, r));
    });
}

// ── Highlight round aktif ─────────────────────────────────────────────────────
function highlightActiveRound(round) {
    ['blue', 'red'].forEach(team => {
        [1, 2, 3].forEach(r => {
            const box = document.getElementById(`score-log-${team}-${r}`);
            if (!box) return;
            if (r === Number(round)) {
                box.style.border = team === 'blue' ? '3px solid #0d6efd' : '3px solid #dc3545';
                box.style.opacity = '1';
            } else {
                box.style.border = '2px solid #ced4da';
                box.style.opacity = '0.5';
            }
        });
    });
}

// ── Tambah digit ke round aktif (setelah button ditekan, langsung update UI) ──
function appendLog(team, digit) {
    const r = Number(currentRound);
    if (!scoreLog[team][r]) scoreLog[team][r] = [];
    scoreLog[team][r].push(digit);
    renderRound(team, r);
}

// ── HAPUS TERBARU ─────────────────────────────────────────────────────────────
function hapusTerakhir(team) {
    const r = Number(currentRound);
    if (scoreLog[team][r] && scoreLog[team][r].length > 0) {
        scoreLog[team][r].pop();
        renderRound(team, r);
    }
}

// ── Load score log dari DB (dipanggil saat halaman load & setelah refresh) ────
async function loadScoreLog() {
    if (!pertandinganId || !juriId) return;

    try {
        const res  = await fetch(`/juri-tanding/score-log?pertandingan_id=${pertandinganId}&juri_id=${juriId}`);
        const data = await res.json();

        // Set current round dari DB
        currentRound = Number(data.current_round) || 1;

        // Isi scoreLog dari data DB
        ['blue', 'red'].forEach(team => {
            [1, 2, 3].forEach(r => {
                const techniques = (data[team] && data[team][String(r)]) || [];
                scoreLog[team][r] = techniques.map(t => techniqueToDigit(t));
            });
        });

        renderAllRounds();
        highlightActiveRound(currentRound);

        console.log('✅ Score log loaded from DB. Round:', currentRound, data);
    } catch (err) {
        console.error('❌ Gagal load score log dari DB:', err);
    }
}

// ── Kirim poin ke server ──────────────────────────────────────────────────────
function sendPoin(type, filter = 'blue') {
    if (type === 'PUKUL')   poin = 1;
    if (type === 'TENDANG') poin = 2;

    console.log('📤 Mengirim poin:', { pertandingan_id: pertandinganId, juri_id: juriId, type, filter, poin });

    fetch('/juri-tanding/kirim-poin', {
        method: 'POST',
        headers: {
            'Content-Type':  'application/json',
            'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
            pertandingan_id: pertandinganId,
            juri_id:         juriId,
            type:            type,
            filter:          filter,
            poin:            poin,
        }),
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
    })
    .then(data => {
        console.log('📥 Response:', data);

        // Langsung tambahkan digit ke box (optimistic UI)
        appendLog(filter, techniqueToDigit(type));

        if (data.status === 'waiting')  console.log('✅ Menunggu juri lain...');
        if (data.status === 'valid')    console.log('✅ Poin SAH!');
        if (data.status === 'ignored')  console.log('⚠️ Diabaikan (juri sama tekan 2x)');
    })
    .catch(error => {
        console.error('❌ Error:', error);
        alert('Gagal mengirim poin. Silakan cek koneksi.');
    });
}

// ── Listen round change dari Echo ─────────────────────────────────────────────
function setupRoundListener() {
    if (!window.Echo || !pertandinganId) {
        setTimeout(setupRoundListener, 500);
        return;
    }
    window.Echo.channel(`timer-${pertandinganId}`).listen('.TimerUpdated', (event) => {
        if (event.current_round && Number(event.current_round) !== currentRound) {
            currentRound = Number(event.current_round);
            highlightActiveRound(currentRound);
            console.log(`🔄 Round changed to ${currentRound}`);
        }
    });
}

// ── Init ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    loadScoreLog();
    setupRoundListener();
});

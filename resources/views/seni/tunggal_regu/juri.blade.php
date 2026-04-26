<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juri - {{ $pertandingan->kelas->nama_kelas ?? 'Seni' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
<<<<<<< HEAD

    @include('components.auto-refresh')
=======
    <style>
        .btn-scale:active { transform: scale(0.95); }
        .player-btn-active-blue  { background:#2563eb; color:#fff; border-color:#1d4ed8; }
        .player-btn-active-red   { background:#dc2626; color:#fff; border-color:#b91c1c; }
        .player-btn-active-other { background:#7c3aed; color:#fff; border-color:#6d28d9; }
        .score-selected { background:#22c55e !important; color:#fff !important; }
    </style>
>>>>>>> b365bb7ec0b42f23b672d46119467ab386024779
</head>

@php
    $jenis        = $jenisPertandingan ?? 'prestasi'; // prestasi | pemasalan
    $isPrestasi   = $jenis === 'prestasi';
    $sideColors   = [1 => 'blue', 2 => 'red'];       // warna untuk prestasi
    $sideColor    = $sideColors[$currentSide] ?? 'purple';
    $firstPlayer  = $currentSidePlayers->first();
    $contingent   = $firstPlayer->player_contingent ?? '-';
    $initials     = $firstPlayer ? strtoupper(substr($firstPlayer->player_name, 0, 2)) : 'P'.$currentSide;
@endphp

<body class="bg-gradient-to-br from-slate-100 to-slate-200 min-h-screen">

    {{-- Hidden config --}}
    <input type="hidden" id="pertandingan_id"   value="{{ $pertandingan->id ?? 1 }}">
    <input type="hidden" id="user_id"           value="{{ $user->id ?? 1 }}">
    <input type="hidden" id="max_jurus"         value="{{ $maxJurus ?? 14 }}">
    <input type="hidden" id="match_type"        value="{{ $matchType ?? 'tunggal' }}">
    <input type="hidden" id="jenis_pertandingan" value="{{ $jenis }}">
    <input type="hidden" id="current_side_init" value="{{ $currentSide }}">

    {{-- All players JSON for pemasalan --}}
    <script>
        const ALL_PLAYERS = @json(
            ($allPlayers ?? collect())->map(fn($players, $side) =>
                $players->map(fn($p) => ['name' => $p->player_name, 'contingent' => $p->player_contingent])->values()
            )
        );
        const ALL_SIDES = @json(($allSides ?? collect())->values());
    </script>

    <div class="container mx-auto p-4 max-w-4xl">

        {{-- ════ HEADER ════ --}}
        <div class="bg-white rounded-2xl shadow-md p-5 mb-5">
            <div class="flex items-center justify-between flex-wrap gap-3">

                {{-- Avatar + info --}}
                <div class="flex items-center gap-4">
                    <div id="headerAvatar"
                         class="w-14 h-14 bg-gradient-to-br from-{{ $sideColor }}-500 to-{{ $sideColor }}-600 rounded-full flex items-center justify-center text-white font-bold text-xl shadow">
                        {{ $initials }}
                    </div>
                    <div>
                        <div id="headerName" class="text-lg font-bold text-gray-800">
                            @foreach($currentSidePlayers as $player)
                                {{ $player->player_name }}@if(!$loop->last), @endif
                            @endforeach
                        </div>
                        <div id="headerContingent" class="text-sm text-gray-500">
                            Kontingen {{ $contingent }}
                            @if($isPrestasi)
                                &mdash; Tim {{ $currentSide == 1 ? '🔵 Biru' : '🔴 Merah' }}
                            @else
                                &mdash; Peserta {{ $currentSide }}
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Kelas + arena --}}
                <div class="text-right">
                    <div class="text-lg font-bold text-{{ $sideColor }}-600">
                        {{ $pertandingan->kelas->nama_kelas ?? 'Kelas' }}
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ ucfirst($matchType) }} &bull; {{ $pertandingan->arena->arena_name ?? 'Arena' }}
                        &bull; <span class="font-semibold {{ $isPrestasi ? 'text-blue-600' : 'text-purple-600' }}">
                            {{ $isPrestasi ? 'Prestasi' : 'Pemasalan' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════ PLAYER SELECTOR ════ --}}
        @if($isPrestasi)
        {{-- PRESTASI: Tombol ganti tim Biru ↔ Merah --}}
        <div class="bg-white rounded-2xl shadow-md p-4 mb-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-3">Sedang menilai tim:</p>
            <div class="flex gap-3">
                <a href="?side=1"
                   class="flex-1 py-3 rounded-xl font-bold text-center transition-all
                          {{ $currentSide == 1 ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-gray-100 text-gray-600 hover:bg-blue-50' }}">
                    🔵 Sudut Biru
                </a>
                <a href="?side=2"
                   class="flex-1 py-3 rounded-xl font-bold text-center transition-all
                          {{ $currentSide == 2 ? 'bg-red-600 text-white shadow-lg shadow-red-200' : 'bg-gray-100 text-gray-600 hover:bg-red-50' }}">
                    🔴 Sudut Merah
                </a>
            </div>
        </div>

        @else
        {{-- PEMASALAN: Selector dinamis sesuai jumlah peserta --}}
        <div class="bg-white rounded-2xl shadow-md p-4 mb-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-3">
                Pilih peserta yang sedang tampil:
            </p>
            <div class="flex flex-wrap gap-2" id="playerSelector">
                @foreach(($allSides ?? []) as $sideNum)
                @php
                    $sidePlayers = ($allPlayers ?? collect())->get($sideNum, collect());
                    $label = $sidePlayers->isNotEmpty()
                        ? $sidePlayers->pluck('player_name')->implode(' / ')
                        : 'Peserta '.$sideNum;
                @endphp
                <button type="button"
                        data-side="{{ $sideNum }}"
                        onclick="switchPlayer({{ $sideNum }})"
                        class="player-selector-btn flex-1 min-w-[120px] py-3 px-4 rounded-xl border-2 font-semibold text-sm transition-all
                               {{ $currentSide == $sideNum
                                    ? 'bg-purple-600 text-white border-purple-700 shadow-lg shadow-purple-100'
                                    : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-purple-50 hover:border-purple-300' }}">
                    <div class="text-base">Peserta {{ $sideNum }}</div>
                    <div class="text-xs opacity-80 truncate">{{ Str::limit($label, 28) }}</div>
                </button>
                @endforeach
            </div>

            {{-- Reset konfirmasi --}}
            <div id="switchWarning" class="hidden mt-3 p-3 bg-amber-50 border border-amber-300 rounded-xl text-sm">
                <p class="font-semibold text-amber-800 mb-2">⚠️ Ganti ke peserta <span id="switchTargetLabel"></span>?</p>
                <p class="text-amber-700 text-xs mb-3">Skor yang sudah diinput untuk peserta saat ini akan tersimpan. Papan input akan direset ke Jurus 1.</p>
                <div class="flex gap-2">
                    <button onclick="confirmSwitch()" class="flex-1 bg-amber-600 text-white py-2 rounded-lg font-bold text-sm">Ya, Ganti</button>
                    <button onclick="cancelSwitch()"  class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg font-bold text-sm">Batal</button>
                </div>
            </div>
        </div>
        @endif

        {{-- ════ SCORING GRID ════ --}}
        <div class="grid grid-cols-3 gap-4 mb-5">

            {{-- Wrong Move --}}
            <div class="bg-red-500 rounded-2xl shadow-md p-6 flex items-center justify-center min-h-44">
                <button id="wrongMoveBtn"
                        class="btn-scale text-white font-bold text-center w-full h-full select-none">
                    <div class="text-5xl mb-2">✗</div>
                    <div class="text-xl">Wrong Move</div>
                </button>
            </div>

            {{-- Center info --}}
            <div class="bg-white rounded-2xl shadow-md p-5">
                <div class="text-center mb-4">
                    <div class="text-sm font-semibold text-gray-500 mb-1">Jurus ke</div>
                    <div class="text-5xl font-black text-red-600" id="currentMove">1</div>
                    <div class="text-sm text-red-400 mt-1">Kesalahan: <span id="currentErrors" class="font-bold">0</span></div>
                </div>

                <div class="mb-3">
                    <h4 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                        Kemantapan / Penghayatan / Stamina
                    </h4>
                    <div class="grid grid-cols-5 gap-1" id="scoreButtons"></div>
                </div>

                <div class="text-center text-xs text-gray-500">
                    Nilai Kategori: <span id="categoryScore" class="font-bold text-green-600 text-sm">0.00</span>
                </div>
            </div>

            {{-- Next Move --}}
            <div class="bg-green-500 rounded-2xl shadow-md p-6 flex items-center justify-center min-h-44">
                <button id="nextMoveBtn"
                        class="btn-scale text-white font-bold text-center w-full h-full select-none">
                    <div class="text-5xl mb-2">→</div>
                    <div class="text-xl">Next Move</div>
                </button>
            </div>
        </div>

        {{-- ════ BOTTOM INFO ════ --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white rounded-2xl shadow-md p-5">
                <h3 class="text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wide">Total Kesalahan</h3>
                <div class="text-center">
                    <div class="text-6xl font-black text-red-500" id="totalErrors">0</div>
                    <p class="text-gray-500 text-xs mt-1">dari semua jurus</p>
                </div>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-md p-5">
                <h3 class="text-sm font-semibold text-red-100 mb-3 uppercase tracking-wide text-center">Nilai Akhir</h3>
                <div class="text-center">
                    <div class="text-6xl font-black text-white" id="finalScore">9.90</div>
                    <p class="text-red-200 text-xs mt-1">Nilai sementara</p>
                </div>
            </div>
        </div>

    </div>{{-- /container --}}

    <script>
        // ─── Config ───
        const MAX_JURUS          = parseInt(document.getElementById('max_jurus').value);
        const MATCH_TYPE         = document.getElementById('match_type').value;
        const PERTANDINGAN_ID    = parseInt(document.getElementById('pertandingan_id').value);
        const USER_ID            = parseInt(document.getElementById('user_id').value);
        const JENIS_PERTANDINGAN = document.getElementById('jenis_pertandingan').value;
        const CSRF_TOKEN         = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const IS_PEMASALAN       = JENIS_PERTANDINGAN === 'pemasalan';

        // currentSide: for prestasi = from URL; for pemasalan = tracked in JS state
        let currentSide = parseInt(document.getElementById('current_side_init').value) || 1;

        // Per-player state store (keyed by side number) — so switching player preserves state
        const playerState = {};
        function getState(side) {
            if (!playerState[side]) {
                playerState[side] = { currentMove: 1, moveErrors: { 1: 0 }, totalCategoryScore: 0 };
            }
            return playerState[side];
        }

        const BASE_SCORE      = 9.90;
        const PENALTY_PER_ERR = 0.01;

        // ─── DOM refs ───
        const currentMoveEl    = document.getElementById('currentMove');
        const currentErrorsEl  = document.getElementById('currentErrors');
        const totalErrorsEl    = document.getElementById('totalErrors');
        const finalScoreEl     = document.getElementById('finalScore');
        const categoryScoreEl  = document.getElementById('categoryScore');
        const wrongMoveBtn     = document.getElementById('wrongMoveBtn');
        const nextMoveBtn      = document.getElementById('nextMoveBtn');
        const scoreBtnsEl      = document.getElementById('scoreButtons');

        // ─── UI helpers ───
        function getInitials(name) {
            return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
        }

        function updateHeaderUI(side) {
            const players = ALL_PLAYERS[side] || [];
            const avatar  = document.getElementById('headerAvatar');
            const name    = document.getElementById('headerName');
            const cont    = document.getElementById('headerContingent');

            if (!IS_PEMASALAN) return; // For prestasi, header is static (server-rendered)

            const nameStr = players.map(p => p.name).join(' / ') || 'Peserta ' + side;
            const contingStr = players[0]?.contingent || '-';

            name.textContent = nameStr;
            cont.textContent = 'Kontingen ' + contingStr + ' — Peserta ' + side;
            avatar.textContent = players[0] ? getInitials(players[0].name) : 'P' + side;
        }

        function updateSelectorUI(side) {
            document.querySelectorAll('.player-selector-btn').forEach(btn => {
                const s = parseInt(btn.dataset.side);
                if (s === side) {
                    btn.className = btn.className
                        .replace(/bg-gray-100|text-gray-600|border-gray-200|hover:[^ ]+/g, '')
                        .trim();
                    btn.classList.add('bg-purple-600','text-white','border-purple-700','shadow-lg','shadow-purple-100');
                } else {
                    btn.classList.remove('bg-purple-600','text-white','border-purple-700','shadow-lg','shadow-purple-100');
                    btn.classList.add('bg-gray-100','text-gray-600','border-gray-200');
                }
            });
        }

        // ─── Pemasalan: player switch ───
        let pendingSwitch = null;

        function switchPlayer(side) {
            if (side === currentSide) return;
            if (!IS_PEMASALAN) return;

            // Show confirmation warning
            pendingSwitch = side;
            const players = ALL_PLAYERS[side] || [];
            const label = players.map(p => p.name).join(' / ') || 'Peserta ' + side;
            document.getElementById('switchTargetLabel').textContent = label + ' (Peserta ' + side + ')';
            document.getElementById('switchWarning').classList.remove('hidden');
        }

        function confirmSwitch() {
            if (pendingSwitch === null) return;
            const newSide = pendingSwitch;
            pendingSwitch = null;
            document.getElementById('switchWarning').classList.add('hidden');

            // Save current state (already in playerState[currentSide])
            // Switch
            currentSide = newSide;
            updateHeaderUI(currentSide);
            updateSelectorUI(currentSide);
            renderFromState(getState(currentSide));
        }

        function cancelSwitch() {
            pendingSwitch = null;
            document.getElementById('switchWarning').classList.add('hidden');
        }

        // ─── Render from state ───
        function renderFromState(state) {
            currentMoveEl.textContent   = state.currentMove;
            currentErrorsEl.textContent = state.moveErrors[state.currentMove] || 0;
            const totalErr = Object.values(state.moveErrors).reduce((s, v) => s + v, 0);
            totalErrorsEl.textContent   = totalErr;
            finalScoreEl.textContent    = Math.max(0, BASE_SCORE - totalErr * PENALTY_PER_ERR + state.totalCategoryScore).toFixed(2);
            categoryScoreEl.textContent = state.totalCategoryScore.toFixed(2);
            generateScoreButtons(state);
        }

        // ─── Score buttons ───
        function generateScoreButtons(state) {
            scoreBtnsEl.innerHTML = '';
            if (state.currentMove <= MAX_JURUS) {
                for (let i = 1; i <= 10; i++) {
                    const score = parseFloat((i * 0.01).toFixed(2));
                    const btn   = document.createElement('button');
                    btn.className = 'px-2 py-2 rounded-full text-xs font-semibold transition-colors ' +
                        (state.totalCategoryScore === score
                            ? 'bg-green-500 text-white'
                            : 'bg-gray-200 hover:bg-gray-300 text-gray-700');
                    btn.textContent = score.toFixed(2);
                    btn.onclick = () => selectScore(score);
                    scoreBtnsEl.appendChild(btn);
                }
            } else {
                const msg = document.createElement('div');
                msg.className = 'text-xs text-gray-400 italic py-2 col-span-5 text-center';
                msg.textContent = `Nilai kategori hanya untuk jurus 1–${MAX_JURUS}`;
                scoreBtnsEl.appendChild(msg);
            }
        }

        // ─── Score select ───
        function selectScore(score) {
            const state = getState(currentSide);
            if (state.currentMove > MAX_JURUS) return;
            state.totalCategoryScore = score;
            sendCategoryScore(score);
            renderFromState(state);
        }

        // ─── Wrong move ───
        wrongMoveBtn.addEventListener('click', () => {
            const state = getState(currentSide);
            if (!state.moveErrors[state.currentMove]) state.moveErrors[state.currentMove] = 0;
            state.moveErrors[state.currentMove]++;
            sendMoveError(state.currentMove);
            renderFromState(state);
            wrongMoveBtn.classList.add('scale-95');
            setTimeout(() => wrongMoveBtn.classList.remove('scale-95'), 150);
        });

        // ─── Next move ───
        nextMoveBtn.addEventListener('click', () => {
            const state = getState(currentSide);
            state.currentMove++;
            if (!state.moveErrors[state.currentMove]) state.moveErrors[state.currentMove] = 0;
            renderFromState(state);
            nextMoveBtn.classList.add('scale-95');
            setTimeout(() => nextMoveBtn.classList.remove('scale-95'), 150);
        });

        // ─── API: send error ───
        function sendMoveError(jurusNumber) {
            fetch('/seni/tunggal-regu/add-error', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({
                    pertandingan_id: PERTANDINGAN_ID,
                    user_id:         USER_ID,
                    jurus_number:    jurusNumber,
                    side:            currentSide
                })
            })
            .then(r => r.json())
            .then(data => { if (data.status !== 'success') console.error('Error save failed:', data); })
            .catch(e => console.error('Network error:', e));
        }

        // ─── API: send category ───
        function sendCategoryScore(score) {
            fetch('/seni/tunggal-regu/set-category', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({
                    pertandingan_id: PERTANDINGAN_ID,
                    user_id:         USER_ID,
                    score:           score,
                    current_jurus:   getState(currentSide).currentMove,
                    side:            currentSide
                })
            })
            .then(r => r.json())
            .then(data => { if (data.status !== 'success') console.error('Category save failed:', data); })
            .catch(e => console.error('Network error:', e));
        }

        // ─── Init ───
        (function init() {
            const initState = getState(currentSide);
            renderFromState(initState);
        })();
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dewan - {{ $pertandingan->kelas->nama_kelas ?? 'Seni' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .row-active-blue { background:#eff6ff; }
        .row-active-red  { background:#fef2f2; }
        .row-active-other{ background:#f5f3ff; }
        .btn-scale:active { transform: scale(0.95); }
    </style>
</head>

@php
    $jenis      = $jenisPertandingan ?? 'prestasi';
    $isPrestasi = $jenis === 'prestasi';
    $allSides   = $allSides ?? collect([1, 2]);
    $allPlayers = $allPlayers ?? collect();
@endphp

<body class="bg-gray-50 min-h-screen">

    <input type="hidden" id="pertandingan_id" value="{{ $id }}">

    {{-- Player data for JS --}}
    <script>
        const ALL_PLAYERS_DEWAN = @json(
            $allPlayers->map(fn($players, $side) =>
                $players->map(fn($p) => ['name' => $p->player_name, 'contingent' => $p->player_contingent])->values()
            )
        );
        const ALL_SIDES_DEWAN = @json($allSides->values());
        const IS_PEMASALAN    = {{ $isPrestasi ? 'false' : 'true' }};
    </script>

    <div class="max-w-4xl mx-auto p-4">

        {{-- ════ HEADER ════ --}}
        <div class="bg-white rounded-2xl shadow-md px-6 py-4 mb-4">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">
                        Panel Dewan — {{ $pertandingan->kelas->nama_kelas ?? 'Seni' }}
                    </h1>
                    <p class="text-sm text-gray-500">
                        {{ $pertandingan->arena->arena_name ?? 'Arena' }}
                        &bull; {{ ucfirst($pertandingan->kelas->jenis_pertandingan ?? '') }}
                        &bull;
                        <span class="{{ $isPrestasi ? 'text-blue-600' : 'text-purple-600' }} font-semibold">
                            {{ $isPrestasi ? 'Prestasi' : 'Pemasalan' }}
                        </span>
                    </p>
                </div>
                <div id="sideIndicator"
                     class="px-4 py-2 rounded-xl font-bold text-sm bg-blue-100 text-blue-800 border border-blue-200">
                    Sudut Biru
                </div>
            </div>
        </div>

        {{-- ════ PLAYER / SIDE SELECTOR ════ --}}
        <div class="bg-white rounded-2xl shadow-md p-4 mb-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">
                {{ $isPrestasi ? 'Pilih sudut / tim:' : 'Pilih peserta yang sedang tampil:' }}
            </p>

            @if($isPrestasi)
            {{-- Prestasi: 2 tombol Biru & Merah --}}
            <div class="flex gap-3">
                <button onclick="switchSide('1')" id="btnSide1"
                        class="flex-1 py-3 rounded-xl font-bold text-white bg-blue-600 shadow ring-4 ring-blue-200 transition-all">
                    🔵 Sudut Biru
                </button>
                <button onclick="switchSide('2')" id="btnSide2"
                        class="flex-1 py-3 rounded-xl font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all">
                    🔴 Sudut Merah
                </button>
            </div>

            @else
            {{-- Pemasalan: tombol dinamis per peserta --}}
            <div class="flex flex-wrap gap-2" id="pemasalanSelector">
                @foreach($allSides as $sideNum)
                @php
                    $sidePlayers = $allPlayers->get($sideNum, collect());
                    $nameLabel   = $sidePlayers->isNotEmpty()
                        ? Str::limit($sidePlayers->pluck('player_name')->implode(' / '), 30)
                        : 'Peserta '.$sideNum;
                @endphp
                <button type="button"
                        data-side="{{ $sideNum }}"
                        onclick="switchSide('{{ $sideNum }}')"
                        class="pemasalan-btn flex-1 min-w-[130px] py-3 px-3 rounded-xl border-2 font-semibold text-sm transition-all
                               {{ $loop->first
                                    ? 'bg-purple-600 text-white border-purple-700 shadow ring-4 ring-purple-100'
                                    : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-purple-50 hover:border-purple-300' }}">
                    <div class="font-bold">Peserta {{ $sideNum }}</div>
                    <div class="text-xs opacity-75 truncate">{{ $nameLabel }}</div>
                </button>
                @endforeach
            </div>
            @endif

            {{-- Konfirmasi ganti peserta (pemasalan) --}}
            @if(!$isPrestasi)
            <div id="switchWarnDewan" class="hidden mt-3 p-3 bg-amber-50 border border-amber-300 rounded-xl text-sm">
                <p class="font-semibold text-amber-800 mb-1">
                    ⚠️ Ganti ke <span id="switchTargetDewan"></span>?
                </p>
                <p class="text-xs text-amber-700 mb-3">
                    Penalti yang sudah diinput tersimpan. Tampilan akan berpindah ke peserta baru.
                </p>
                <div class="flex gap-2">
                    <button onclick="confirmSwitchDewan()" class="flex-1 bg-amber-600 text-white py-2 rounded-lg font-bold text-sm">Ya, Ganti</button>
                    <button onclick="cancelSwitchDewan()"  class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg font-bold text-sm">Batal</button>
                </div>
            </div>
            @endif
        </div>

        {{-- ════ TABEL PENALTI ════ --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-4">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-700 text-sm uppercase tracking-wide">Daftar Penalti</h2>
                <span class="text-xs text-gray-400">✓ = aktif untuk peserta ini</span>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Penalti</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">Clear</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">Tambah</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Status</th>
                    </tr>
                </thead>
                <tbody id="penaltyTable" class="divide-y divide-gray-100">
                    {{-- rendered by JS --}}
                </tbody>
            </table>
            <div class="flex justify-between items-center px-5 py-3 bg-gray-50 border-t border-gray-100">
                <span class="text-sm font-bold text-gray-700">Total Penalti</span>
                <span id="grandTotal" class="text-xl font-black text-blue-600">0.00</span>
            </div>
        </div>

        {{-- Toast notif --}}
        <div id="toast"
             class="fixed bottom-6 right-6 bg-gray-800 text-white px-4 py-3 rounded-xl shadow-xl text-sm font-medium
                    opacity-0 pointer-events-none transition-opacity duration-300">
        </div>
    </div>

    <script>
        // ─── Config ───
        const PERTANDINGAN_ID = document.getElementById('pertandingan_id').value;
        const CSRF_TOKEN      = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const dbRules = @json($penaltyRules);
        const penaltyTypes = dbRules.map(r => ({ name: r.name, value: parseFloat(r.value), id: r.type }));

        // matchState[side][penaltyId] = true/false
        const matchState = {};
        ALL_SIDES_DEWAN.forEach(s => matchState[String(s)] = {});

        let currentSide  = String(ALL_SIDES_DEWAN[0] ?? '1');
        let pendingSwitch = null;

        // ─── Side switcher (prestasi: nama warna; pemasalan: nomor peserta) ───
        function switchSide(side) {
            side = String(side);
            if (side === currentSide) return;

            if (IS_PEMASALAN) {
                pendingSwitch = side;
                const players = ALL_PLAYERS_DEWAN[side] || [];
                const label   = players.map(p => p.name).join(' / ') || 'Peserta ' + side;
                document.getElementById('switchTargetDewan').textContent = label + ' (Peserta ' + side + ')';
                document.getElementById('switchWarnDewan').classList.remove('hidden');
                return;
            }

            doSwitch(side);
        }

        function confirmSwitchDewan() {
            if (!pendingSwitch) return;
            doSwitch(pendingSwitch);
            pendingSwitch = null;
            document.getElementById('switchWarnDewan').classList.add('hidden');
        }

        function cancelSwitchDewan() {
            pendingSwitch = null;
            document.getElementById('switchWarnDewan').classList.add('hidden');
        }

        function doSwitch(side) {
            currentSide = side;
            updateSelectorUI();
            updateSideIndicator();
            render();
            fetchMatchData(); // sync from server
        }

        // ─── Update top indicator badge ───
        function updateSideIndicator() {
            const el = document.getElementById('sideIndicator');
            if (!IS_PEMASALAN) {
                if (currentSide === '1') {
                    el.textContent  = 'Sudut Biru';
                    el.className    = 'px-4 py-2 rounded-xl font-bold text-sm bg-blue-100 text-blue-800 border border-blue-200';
                } else {
                    el.textContent  = 'Sudut Merah';
                    el.className    = 'px-4 py-2 rounded-xl font-bold text-sm bg-red-100 text-red-800 border border-red-200';
                }
            } else {
                const players = ALL_PLAYERS_DEWAN[currentSide] || [];
                const label   = players.map(p => p.name).join(' / ') || 'Peserta ' + currentSide;
                el.textContent = 'Peserta ' + currentSide + ' — ' + label;
                el.className   = 'px-4 py-2 rounded-xl font-bold text-sm bg-purple-100 text-purple-800 border border-purple-200';
            }
        }

        // ─── Update selector button highlights ───
        function updateSelectorUI() {
            if (!IS_PEMASALAN) {
                const btn1 = document.getElementById('btnSide1');
                const btn2 = document.getElementById('btnSide2');
                if (currentSide === '1') {
                    btn1.className = 'flex-1 py-3 rounded-xl font-bold text-white bg-blue-600 shadow ring-4 ring-blue-200 transition-all';
                    btn2.className = 'flex-1 py-3 rounded-xl font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all';
                } else {
                    btn1.className = 'flex-1 py-3 rounded-xl font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all';
                    btn2.className = 'flex-1 py-3 rounded-xl font-bold text-white bg-red-600 shadow ring-4 ring-red-200 transition-all';
                }
            } else {
                document.querySelectorAll('.pemasalan-btn').forEach(btn => {
                    const s = btn.dataset.side;
                    if (s === currentSide) {
                        btn.className = btn.className
                            .replace('bg-gray-100','').replace('text-gray-600','').replace('border-gray-200','')
                            .replace('hover:bg-purple-50','').replace('hover:border-purple-300','');
                        btn.classList.add('bg-purple-600','text-white','border-purple-700','shadow','ring-4','ring-purple-100');
                    } else {
                        btn.classList.remove('bg-purple-600','text-white','border-purple-700','shadow','ring-4','ring-purple-100');
                        btn.classList.add('bg-gray-100','text-gray-600','border-gray-200');
                    }
                });
            }
        }

        // ─── Penalty color per side ───
        function sideColor() {
            if (!IS_PEMASALAN) return currentSide === '1' ? 'blue' : 'red';
            return 'purple';
        }

        // ─── Render penalty table ───
        function render() {
            const tbody   = document.getElementById('penaltyTable');
            const totalEl = document.getElementById('grandTotal');
            if (!tbody) return;

            if (penaltyTypes.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-gray-400 italic text-sm">Tidak ada rule penalti. Hubungi operator.</td></tr>';
                return;
            }

            const col = sideColor();
            tbody.innerHTML = '';
            let total = 0;

            penaltyTypes.forEach(p => {
                const isActive = !!(matchState[currentSide] || {})[p.id];
                if (isActive) total += p.value;

                const rowBg   = isActive ? `row-active-${col}` : '';
                const valColor = isActive ? `text-${col}-600 font-bold text-lg` : 'text-gray-300';
                const btnAdd  = `bg-${col}-600 hover:bg-${col}-700 text-white`;

                const tr = document.createElement('tr');
                tr.className = rowBg;
                tr.innerHTML = `
                    <td class="px-4 py-3 text-sm text-gray-700">${p.name}</td>
                    <td class="px-3 py-3 text-center">
                        <button onclick="clearPenalty('${p.id}')"
                                class="btn-scale bg-gray-400 hover:bg-gray-500 text-white px-5 py-3 rounded-lg text-xs font-semibold transition-colors">
                            Clear
                        </button>
                    </td>
                    <td class="px-3 py-3 text-center">
                        <button onclick="addPenalty('${p.id}')"
                                class="btn-scale ${btnAdd} px-5 py-3 rounded-lg text-xs font-bold transition-colors shadow">
                            ${Math.abs(p.value).toFixed(2)}
                        </button>
                    </td>
                    <td class="px-3 py-3 text-center text-sm ${valColor}">
                        ${isActive ? Math.abs(p.value).toFixed(2) : '—'}
                    </td>
                `;
                tbody.appendChild(tr);
            });

            totalEl.textContent = Math.abs(total).toFixed(2);
            totalEl.className   = `text-xl font-black text-${col}-600`;
        }

        // ─── Add / Clear penalty ───
        function addPenalty(id) {
            const state = matchState[currentSide] = matchState[currentSide] || {};
            if (state[id]) { showToast('Penalti sudah aktif untuk peserta ini'); return; }
            state[id] = true;
            const rule = penaltyTypes.find(p => p.id === id);
            sendPenalty(id, rule.value);
            render();
        }

        function clearPenalty(id) {
            const state = matchState[currentSide] = matchState[currentSide] || {};
            if (!state[id]) { showToast('Tidak ada penalti aktif untuk dihapus'); return; }
            state[id] = false;
            sendPenalty(id, 0);
            render();
        }

        // ─── API call ───
        function sendPenalty(penaltyId, value) {
            fetch('/dewan-seni-tunggal-regu/kirim-penalti', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({
                    pertandingan_id: PERTANDINGAN_ID,
                    penalty_id:      penaltyId,
                    value:           value,
                    side:            parseInt(currentSide)
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(value === 0 ? 'Penalti dihapus ✓' : 'Penalti ditambahkan ✓');
                    setTimeout(fetchMatchData, 600);
                } else {
                    showToast('⚠️ Gagal: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(e => { console.error(e); showToast('⚠️ Network error'); });
        }

        // ─── Fetch & sync from DB ───
        function fetchMatchData() {
            fetch(`/api/seni/tunggal-regu/events/${PERTANDINGAN_ID}`)
                .then(r => r.json())
                .then(result => {
                    if (result.status === 'success' && result.data) {
                        // Reset all sides
                        ALL_SIDES_DEWAN.forEach(s => matchState[String(s)] = {});
                        // Populate from server
                        (result.data.penalties || []).forEach(p => {
                            if (p.status === 'active') {
                                const s = String(p.side || '1');
                                if (!matchState[s]) matchState[s] = {};
                                matchState[s][p.type] = true;
                            }
                        });
                        render();
                    }
                })
                .catch(e => console.error('Fetch error:', e));
        }

        // ─── Toast notification ───
        function showToast(msg) {
            const el = document.getElementById('toast');
            el.textContent = msg;
            el.classList.remove('opacity-0');
            el.classList.add('opacity-100');
            setTimeout(() => { el.classList.remove('opacity-100'); el.classList.add('opacity-0'); }, 2500);
        }

        // ─── Init ───
        document.addEventListener('DOMContentLoaded', () => {
            updateSideIndicator();
            render();
            fetchMatchData();
            setInterval(fetchMatchData, 5000);
        });
    </script>
</body>
</html>
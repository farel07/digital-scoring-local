<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penilaian Penalti Pencak Silat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('components.auto-refresh')
</head>

@php
    $jenis      = $jenisPertandingan ?? 'prestasi';
    $isPrestasi = $jenis === 'prestasi';
    $allSides   = $allSides ?? collect([1, 2]);
    $allPlayers = $allPlayers ?? collect();
@endphp
<body class="bg-gray-50 min-h-screen p-5">
    <div class="max-w-6xl mx-auto bg-white shadow-lg">
        <!-- Header Atas -->
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-{{ $isPrestasi ? 'blue' : 'purple' }}-600 text-xs font-semibold uppercase tracking-wide">
                        {{ $isPrestasi ? 'PANEL DEWAN' : 'PANEL DEWAN — PEMASALAN' }}
                    </div>
                    <div class="text-gray-700 font-semibold text-sm mt-1">
                        {{ $pertandingan->kelas->nama_kelas ?? 'Seni Ganda' }}
                        &bull; {{ $pertandingan->arena->arena_name ?? 'Arena' }}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="openWinnerModal()" class="px-4 py-2 rounded-xl font-bold text-sm bg-green-600 text-white hover:bg-green-700 transition-colors shadow">
                        🏆 Tentukan Pemenang
                    </button>
                    <div id="sideIndicator" class="px-4 py-2 rounded-xl font-bold text-sm bg-blue-100 text-blue-800 border border-blue-200">
                        Sudut Biru
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Toggle -->
        <div class="px-6 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">
                {{ $isPrestasi ? 'Pilih sudut / tim:' : 'Pilih peserta yang sedang tampil:' }}
            </p>

            @if($isPrestasi)
            <!-- Prestasi: 2 tombol Biru & Merah -->
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
            <!-- Pemasalan: tombol dinamis per peserta -->
            <div class="flex flex-wrap gap-2">
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
                        class="pemasalan-side-btn flex-1 min-w-[130px] py-3 px-3 rounded-xl border-2 font-semibold text-sm transition-all
                               {{ $loop->first
                                    ? 'bg-purple-600 text-white border-purple-700 shadow ring-4 ring-purple-100'
                                    : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-purple-50 hover:border-purple-300' }}">
                    <div class="font-bold">Peserta {{ $sideNum }}</div>
                    <div class="text-xs opacity-75 truncate">{{ $nameLabel }}</div>
                </button>
                @endforeach
            </div>

            <!-- Konfirmasi ganti peserta -->
            <div id="switchWarnDewan" class="hidden mt-3 p-3 bg-amber-50 border border-amber-300 rounded-xl text-sm">
                <p class="font-semibold text-amber-800 mb-1">⚠️ Ganti ke <span id="switchTargetDewan"></span>?</p>
                <p class="text-xs text-amber-700 mb-3">Penalti yang sudah diinput tersimpan. Tampilan akan berpindah ke peserta baru.</p>
                <div class="flex gap-2">
                    <button onclick="confirmSwitchDewan()" class="flex-1 bg-amber-600 text-white py-2 rounded-lg font-bold text-sm">Ya, Ganti</button>
                    <button onclick="cancelSwitchDewan()"  class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg font-bold text-sm">Batal</button>
                </div>
            </div>
            @endif
        </div>

        <!-- Tabel Penalti -->
        <div class="p-6">
            <div class="border border-gray-300 rounded-md shadow-sm overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200">Penalty Category</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 w-24">Undo</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 w-24">Add</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 w-24">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="penaltyTable" class="bg-white divide-y divide-gray-200">
                        <!-- 0. WAKTU -->
                        <tr class="hover:bg-gray-50 transition-colors" id="row-0">
                            <td class="px-4 py-3 text-sm text-gray-700">WAKTU</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="removeLastPenalty(0)" class="bg-blue-500 text-white w-full py-4 rounded-md hover:bg-blue-600 text-xs font-medium transition-colors active:scale-95">Clear 1</button>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="addNewPenalty(0)" class="bg-red-500 text-white w-full py-4 rounded-md hover:bg-red-600 text-xs font-bold transition-colors active:scale-95">-0.50</button>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 font-semibold text-sm" id="total-0">0</td>
                        </tr>
                        <!-- 1. KELUAR GARIS -->
                        <tr class="hover:bg-gray-50 transition-colors" id="row-1">
                            <td class="px-4 py-3 text-sm text-gray-700">SETIAP KALI KELUAR GARIS</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="removeLastPenalty(1)" class="bg-blue-500 text-white w-full py-4 rounded-md hover:bg-blue-600 text-xs font-medium transition-colors active:scale-95">Clear 1</button>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="addNewPenalty(1)" class="bg-red-500 text-white w-full py-4 rounded-md hover:bg-red-600 text-xs font-bold transition-colors active:scale-95">-0.50</button>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 font-semibold text-sm" id="total-1">0</td>
                        </tr>
                        <!-- 2. SENJATA JATUH -->
                        <tr class="hover:bg-gray-50 transition-colors" id="row-2">
                            <td class="px-4 py-3 text-sm text-gray-700">SETIAP KALI SENJATA JATUH TIDAK SESUAI DESKRIPSI</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="removeLastPenalty(2)" class="bg-blue-500 text-white w-full py-4 rounded-md hover:bg-blue-600 text-xs font-medium transition-colors active:scale-95">Clear 1</button>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="addNewPenalty(2)" class="bg-red-500 text-white w-full py-4 rounded-md hover:bg-red-600 text-xs font-bold transition-colors active:scale-95">-0.50</button>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 font-semibold text-sm" id="total-2">0</td>
                        </tr>
                        <!-- 3. SENJATA TIDAK JATUH -->
                        <tr class="hover:bg-gray-50 transition-colors" id="row-3">
                            <td class="px-4 py-3 text-sm text-gray-700">SENJATA TIDAK JATUH SESUAI DESKRIPSI</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="removeLastPenalty(3)" class="bg-blue-500 text-white w-full py-4 rounded-md hover:bg-blue-600 text-xs font-medium transition-colors active:scale-95">Clear 1</button>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="addNewPenalty(3)" class="bg-red-500 text-white w-full py-4 rounded-md hover:bg-red-600 text-xs font-bold transition-colors active:scale-95">-0.50</button>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 font-semibold text-sm" id="total-3">0</td>
                        </tr>
                        <!-- 4. TIDAK ADA SALAM / SUARA -->
                        <tr class="hover:bg-gray-50 transition-colors" id="row-4">
                            <td class="px-4 py-3 text-sm text-gray-700">TIDAK ADA SALAM & SETIAP KALI MENGELUARKAN SUARA</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="removeLastPenalty(4)" class="bg-blue-500 text-white w-full py-4 rounded-md hover:bg-blue-600 text-xs font-medium transition-colors active:scale-95">Clear 1</button>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="addNewPenalty(4)" class="bg-red-500 text-white w-full py-4 rounded-md hover:bg-red-600 text-xs font-bold transition-colors active:scale-95">-0.50</button>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 font-semibold text-sm" id="total-4">0</td>
                        </tr>
                        <!-- 5. BAJU / SENJATA PATAH -->
                        <tr class="hover:bg-gray-50 transition-colors" id="row-5">
                            <td class="px-4 py-3 text-sm text-gray-700">BAJU / SENJATA TIDAK SESUAI (PATAH)</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="removeLastPenalty(5)" class="bg-blue-500 text-white w-full py-4 rounded-md hover:bg-blue-600 text-xs font-medium transition-colors active:scale-95">Clear 1</button>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="addNewPenalty(5)" class="bg-red-500 text-white w-full py-4 rounded-md hover:bg-red-600 text-xs font-bold transition-colors active:scale-95">-0.50</button>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 font-semibold text-sm" id="total-5">0</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Bagian Total -->
            <div class="mt-4 flex justify-between items-center bg-gray-50 px-4 py-3 rounded-md border">
                <div class="text-lg font-bold text-gray-700">Total Deduction</div>
                <div class="text-red-600 text-xl font-bold" id="grandTotal">0.00</div>
            </div>
        </div>

        </div>

        {{-- Winner Modal --}}
        <div id="winnerModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="winnerModalContent">
                <div class="bg-green-600 text-white px-6 py-4 flex justify-between items-center">
                    <h3 class="font-bold text-lg">TENTUKAN PEMENANG</h3>
                    <button onclick="closeWinnerModal()" class="text-white hover:text-green-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4 text-center text-sm font-medium">Pilih peserta pemenang untuk pertandingan ini:</p>
                    <div class="space-y-3">
                        @foreach($allSides as $sideNum)
                            @php
                                $sidePlayers = $allPlayers->get($sideNum, collect());
                                $firstPlayer = $sidePlayers->first();
                                $pId = $firstPlayer ? $firstPlayer->id : null;
                                $pName = $sidePlayers->isNotEmpty() ? $sidePlayers->pluck('player_name')->implode(' / ') : 'Peserta '.$sideNum;
                            @endphp
                            @if($pId)
                                <button onclick="selectWinner({{ $pId }}, '{{ addslashes($pName) }}')" class="w-full py-3 px-4 rounded-xl font-bold border-2 transition-all {{ $sideNum == 1 ? 'border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white' : ($sideNum == 2 ? 'border-red-200 bg-red-50 text-red-700 hover:bg-red-600 hover:text-white' : 'border-purple-200 bg-purple-50 text-purple-700 hover:bg-purple-600 hover:text-white') }}">
                                    @if($isPrestasi && $sideNum == 1) 🟦 SUDUT BIRU @elseif($isPrestasi && $sideNum == 2) 🟥 SUDUT MERAH @else Peserta {{ $sideNum }} @endif
                                    <div class="text-xs font-normal mt-1 opacity-80 truncate">{{ $pName }}</div>
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button onclick="closeWinnerModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors">Batal</button>
                </div>
            </div>
        </div>

    </div>

    <script>
        const ALL_PLAYERS_GANDA = @json(
            $allPlayers->map(fn($players, $side) =>
                $players->map(fn($p) => ['name' => $p->player_name, 'contingent' => $p->player_contingent])->values()
            )
        );
        const ALL_SIDES_GANDA = @json($allSides->values());
        const IS_PEMASALAN    = {{ $isPrestasi ? 'false' : 'true' }};

        // State for all sides — initialized dynamically
        const penaltyTypes = [
            { type: 'WAKTU', value: -0.50 },
            { type: 'KELUAR_GARIS', value: -0.50 },
            { type: 'SENJATA_JATUH', value: -0.50 },
            { type: 'SENJATA_TIDAK_JATUH', value: -0.50 },
            { type: 'TIDAK_ADA_SALAM_SUARA', value: -0.50 },
            { type: 'BAJU_SENJATA_PATAH', value: -0.50 }
        ];

        // Initialize state dynamically for all sides
        function createInitialState() {
            return penaltyTypes.map(p => ({ ...p, stack: [] }));
        }

        const appState = {};
        // Ensure at minimum side 1 and 2 always exist
        appState['1'] = createInitialState();
        appState['2'] = createInitialState();
        // Add any additional sides from pemasalan
        ALL_SIDES_GANDA.forEach(s => {
            if (!appState[String(s)]) appState[String(s)] = createInitialState();
        });

        let currentSide  = String(ALL_SIDES_GANDA[0] ?? '1');
        let pendingSwitch = null;

        // Penalty color per side
        function sideColor() {
            if (!IS_PEMASALAN) return currentSide === '1' ? 'blue' : 'red';
            return 'purple';
        }

        // Switch Side Function
        function switchSide(side) {
            side = String(side);
            if (side === currentSide) return;

            if (IS_PEMASALAN) {
                pendingSwitch = side;
                const players = ALL_PLAYERS_GANDA[side] || [];
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
            if (!appState[currentSide]) appState[currentSide] = createInitialState();
            updateSideIndicator();
            updateSelectorUI();
            updateAllRows();
            updateGrandTotal();
            fetchActivePenalties();

            // Notify server so others can sync (juri, penonton, dewanOperator)
            fetch('/dewan-seni-ganda/switch-side', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    pertandingan_id: MATCH_ID,
                    side: side
                })
            }).catch(e => console.error('Failed to broadcast side switch', e));
        }

        // Update top indicator badge
        function updateSideIndicator() {
            const el = document.getElementById('sideIndicator');
            if (!IS_PEMASALAN) {
                if (currentSide === '1') {
                    el.textContent = 'Sudut Biru';
                    el.className   = 'px-4 py-2 rounded-xl font-bold text-sm bg-blue-100 text-blue-800 border border-blue-200';
                } else {
                    el.textContent = 'Sudut Merah';
                    el.className   = 'px-4 py-2 rounded-xl font-bold text-sm bg-red-100 text-red-800 border border-red-200';
                }
            } else {
                const players = ALL_PLAYERS_GANDA[currentSide] || [];
                const label   = players.map(p => p.name).join(' / ') || 'Peserta ' + currentSide;
                el.textContent = 'Peserta ' + currentSide + ' — ' + label;
                el.className   = 'px-4 py-2 rounded-xl font-bold text-sm bg-purple-100 text-purple-800 border border-purple-200';
            }
        }

        // Update selector button highlights
        function updateSelectorUI() {
            if (!IS_PEMASALAN) {
                const btn1 = document.getElementById('btnSide1');
                const btn2 = document.getElementById('btnSide2');
                if (btn1 && btn2) {
                    if (currentSide === '1') {
                        btn1.className = 'flex-1 py-3 rounded-xl font-bold text-white bg-blue-600 shadow ring-4 ring-blue-200 transition-all';
                        btn2.className = 'flex-1 py-3 rounded-xl font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all';
                    } else {
                        btn1.className = 'flex-1 py-3 rounded-xl font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all';
                        btn2.className = 'flex-1 py-3 rounded-xl font-bold text-white bg-red-600 shadow ring-4 ring-red-200 transition-all';
                    }
                }
            } else {
                document.querySelectorAll('.pemasalan-side-btn').forEach(btn => {
                    const s = btn.dataset.side;
                    if (s === currentSide) {
                        btn.className = 'pemasalan-side-btn flex-1 min-w-[130px] py-3 px-3 rounded-xl border-2 font-semibold text-sm transition-all bg-purple-600 text-white border-purple-700 shadow ring-4 ring-purple-100';
                    } else {
                        btn.className = 'pemasalan-side-btn flex-1 min-w-[130px] py-3 px-3 rounded-xl border-2 font-semibold text-sm transition-all bg-gray-100 text-gray-600 border-gray-200 hover:bg-purple-50 hover:border-purple-300';
                    }
                });
            }
        }

        // ---------------------------------------------------------
        // LOGIC UTAMA (ADD & CLEAR INCREMENTAL)
        // ---------------------------------------------------------

        function addNewPenalty(index) {
            const categories = appState[currentSide];
            const category = categories[index];
            
            // 1. Buat ID unik untuk penalti spesifik ini
            const uniqueId = `p_${index}_${Date.now()}_${Math.random().toString(36).substr(2, 5)}`;
            
            // 2. Masukkan ke stack lokal
            category.stack.push(uniqueId);
            
            // 3. Update Tampilan Lokal (Agar instan)
            updateRowUI(index);
            updateGrandTotal();
            
            // 4. Kirim ke Server
            sendPenalty(uniqueId, category.type, category.value, 'add');
        }

        function removeLastPenalty(index) {
            const categories = appState[currentSide];
            const category = categories[index];
            
            if (category.stack.length === 0) { return; }

            const idToRemove = category.stack.pop();
            updateRowUI(index);
            updateGrandTotal();
            sendPenalty(idToRemove, category.type, category.value, 'clear');
        }

        function updateAllRows() {
            const categories = appState[currentSide];
            categories.forEach((_, index) => updateRowUI(index));
        }

        // Fungsi Update Baris Tabel (Visual)
        function updateRowUI(index) {
            const categories = appState[currentSide];
            const category = categories[index];
            const count = category.stack.length;
            const currentTotal = count * category.value;
            const col = sideColor();

            const rowElement = document.getElementById(`row-${index}`);
            const totalCell = document.getElementById(`total-${index}`);

            totalCell.textContent = count === 0 ? "0" : currentTotal.toFixed(2);

            if (count > 0) {
                rowElement.classList.add('bg-red-50');
                totalCell.classList.add('text-red-600', 'font-bold');
                totalCell.classList.remove('text-gray-700');
            } else {
                rowElement.classList.remove('bg-red-50');
                totalCell.classList.remove('text-red-600', 'font-bold');
                totalCell.classList.add('text-gray-700');
            }
        }

        // Fungsi Hitung Total Keseluruhan
        function updateGrandTotal() {
            const categories = appState[currentSide];
            let total = 0;
            categories.forEach(cat => {
                total += (cat.stack.length * cat.value);
            });
            document.getElementById('grandTotal').textContent = total.toFixed(2);
        }

        // ---------------------------------------------------------
        // KOMUNIKASI SERVER
        // ---------------------------------------------------------

        const MATCH_ID = {{ $pertandingan->id }};

        function getMatchId() {
            return MATCH_ID;
        }

        function sendPenalty(penaltyId, type, value, action) {
            const matchId = getMatchId();
            
            const data = {
                pertandingan_id: parseInt(matchId),
                penalty_id: penaltyId,
                type: type,
                value: value,
                action: action, // 'add' or 'clear'
                side: currentSide // Include side
            };

            // Ganti URL di bawah sesuai route Laravel Anda
            fetch('/dewan-seni-ganda/kirim-penalti', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.status !== 'success') {
                    console.error('Server Error:', result.message);
                } else {
                    console.log(`Success: ${action} ${type} Side: ${currentSide}`);
                    // Refresh list bawah segera
                    fetchActivePenalties(); 
                }
            })
            .catch(error => {
                console.error('Network Error:', error);
            });
        }

        // ---------------------------------------------------------
        // SYNC DATA DARI SERVER (MONITORING)
        // ---------------------------------------------------------

        // ---------------------------------------------------------
        // SYNC DATA DARI SERVER
        // ---------------------------------------------------------

        function fetchActivePenalties() {
            const matchId = getMatchId();
            
            fetch(`/api/seni/ganda/events/${matchId}`)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success' && result.data) {
                        syncStateWithServer(result.data.penalties || []);
                    } else {
                        console.error('Unexpected API response:', result);
                    }
                })
                .catch(error => console.error('Error fetching penalties:', error));
        }

        function syncStateWithServer(serverPenalties) {
            // Reset current side stack only
            const categories = appState[currentSide];
            if (categories) categories.forEach(cat => { cat.stack = []; });

            // Filter active penalties for current side
            const activePenalties = serverPenalties.filter(p => 
                p.status === 'active' && (String(p.side) === String(currentSide) || (!p.side && currentSide === '1'))
            );

            // Sort by timestamp ascending
            activePenalties.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));

            // Populate stacks
            activePenalties.forEach(p => {
                const catIndex = penaltyTypes.findIndex(pt => pt.type === p.type);
                if (catIndex !== -1 && appState[currentSide]) {
                    appState[currentSide][catIndex].stack.push(p.penalty_id);
                }
            });

            updateAllRows();
            updateGrandTotal();
        }



        // Inisialisasi
        document.addEventListener('DOMContentLoaded', function() {
            updateSideIndicator();
            updateAllRows();
            updateGrandTotal();
            fetchActivePenalties();
            setInterval(fetchActivePenalties, 5000);
        });

        // ─── Winner Modal Logic ───
        const winnerModal = document.getElementById('winnerModal');
        const winnerModalContent = document.getElementById('winnerModalContent');

        function openWinnerModal() {
            winnerModal.classList.remove('hidden');
            setTimeout(() => {
                winnerModalContent.classList.remove('scale-95', 'opacity-0');
                winnerModalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeWinnerModal() {
            winnerModalContent.classList.remove('scale-100', 'opacity-100');
            winnerModalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                winnerModal.classList.add('hidden');
            }, 300);
        }

        function selectWinner(playerId, playerName) {
            if (confirm(`Apakah Anda yakin ingin menentukan ${playerName} sebagai pemenang?`)) {
                fetch('/dewan-tanding/set-winner', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        pertandingan_id: MATCH_ID,
                        winner_id: playerId
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        alert('Gagal: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan: ' + (error.message || 'Kesalahan jaringan.'));
                });
            }
        }
    </script>
</body>
</html>
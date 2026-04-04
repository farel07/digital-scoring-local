<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penilaian Penalti Pencak Silat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-5">
    <div class="max-w-6xl mx-auto bg-white shadow-lg">
        <!-- Header Atas -->
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-blue-600 text-xs font-semibold uppercase tracking-wide">PELATIH</div>
                    <div class="text-blue-700 font-semibold text-sm mt-1">RISKA HERMAWAN, RIRIN RINASIH</div>
                </div>
                <div class="text-right">
                    <div class="text-gray-700 font-semibold text-sm">Arena A, Match 1</div>
                    <div class="text-gray-600 text-sm mt-1">GANDA</div>
                </div>
            </div>
        </div>

        <!-- Side Toggle -->
        <div class="flex justify-center my-4 space-x-4">
            <button onclick="switchSide('1')" id="btnSide1" class="px-6 py-3 rounded-lg font-bold text-white bg-blue-600 shadow-md transform scale-105 ring-4 ring-blue-300 transition-all">
                SUDUT BIRU
            </button>
            <button onclick="switchSide('2')" id="btnSide2" class="px-6 py-3 rounded-lg font-bold text-gray-500 bg-gray-200 hover:bg-gray-300 transition-all">
                SUDUT MERAH
            </button>
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

    <script>
        // State for both sides
        const penaltyTypes = [
            { type: 'WAKTU', value: -0.50 },
            { type: 'KELUAR_GARIS', value: -0.50 },
            { type: 'SENJATA_JATUH', value: -0.50 },
            { type: 'SENJATA_TIDAK_JATUH', value: -0.50 },
            { type: 'TIDAK_ADA_SALAM_SUARA', value: -0.50 },
            { type: 'BAJU_SENJATA_PATAH', value: -0.50 }
        ];

        // Initialize state for side 1 and 2
        function createInitialState() {
            return penaltyTypes.map(p => ({ ...p, stack: [] }));
        }

        let appState = {
            '1': createInitialState(),
            '2': createInitialState()
        };

        let currentSide = '1'; // Default Side

        function getCategories() {
            return appState[currentSide];
        }

        // Switch Side Function
        function switchSide(side) {
            currentSide = side;
            
            // Update Buttons
            const btn1 = document.getElementById('btnSide1');
            const btn2 = document.getElementById('btnSide2');
            
            if (side === '1') {
                btn1.className = "px-6 py-3 rounded-lg font-bold text-white bg-blue-600 shadow-md transform scale-105 ring-4 ring-blue-300 transition-all";
                btn2.className = "px-6 py-3 rounded-lg font-bold text-gray-500 bg-gray-200 hover:bg-gray-300 transition-all";
            } else {
                btn1.className = "px-6 py-3 rounded-lg font-bold text-gray-500 bg-gray-200 hover:bg-gray-300 transition-all";
                btn2.className = "px-6 py-3 rounded-lg font-bold text-white bg-red-600 shadow-md transform scale-105 ring-4 ring-red-300 transition-all";
            }

            // Update UI
            updateAllRows();
            updateGrandTotal();
            fetchActivePenalties();
        }

        // ---------------------------------------------------------
        // LOGIC UTAMA (ADD & CLEAR INCREMENTAL)
        // ---------------------------------------------------------

        function addNewPenalty(index) {
            const categories = getCategories();
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
            const categories = getCategories();
            const category = categories[index];
            
            // Cek apakah ada penalti untuk dihapus?
            if (category.stack.length === 0) {
                return; 
            }

            // 1. Ambil ID terakhir (LIFO - Last In First Out)
            const idToRemove = category.stack.pop();

            // 2. Update Tampilan Lokal
            updateRowUI(index);
            updateGrandTotal();

            // 3. Kirim perintah hapus ke Server untuk ID spesifik tersebut
            sendPenalty(idToRemove, category.type, category.value, 'clear');
        }

        function updateAllRows() {
            const categories = getCategories();
            categories.forEach((_, index) => updateRowUI(index));
        }

        // Fungsi Update Baris Tabel (Visual)
        function updateRowUI(index) {
            const categories = getCategories();
            const category = categories[index];
            const count = category.stack.length;
            const currentTotal = count * category.value;
            
            const rowElement = document.getElementById(`row-${index}`);
            const totalCell = document.getElementById(`total-${index}`);
            
            // Tampilkan angka. Jika 0 tampilkan "0", jika tidak tampilkan decimal (misal -1.00)
            totalCell.textContent = count === 0 ? "0" : currentTotal.toFixed(2);
            
            // Styling jika ada penalti
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
            const categories = getCategories();
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
            // 1. Reset current side stack
            const categories = getCategories();
            categories.forEach(cat => {
                cat.stack = [];
            });

            // 2. Filter active penalties for current side
            const activePenalties = serverPenalties.filter(p => 
                p.status === 'active' && (p.side == currentSide || !p.side)
            );

            // 3. Sort by timestamp ascending (Oldest first) so we push to stack in order
            activePenalties.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));

            // 4. Populate stacks
            activePenalties.forEach(p => {
                // Find matching category index
                const catIndex = penaltyTypes.findIndex(pt => pt.type === p.type);
                if (catIndex !== -1) {
                    categories[catIndex].stack.push(p.penalty_id);
                }
            });

            // 5. Update UI
            updateAllRows();
            updateGrandTotal();
        }



        // Inisialisasi
        document.addEventListener('DOMContentLoaded', function() {
            updateAllRows();
            updateGrandTotal();
            fetchActivePenalties(); 
        });
    </script>
</body>
</html>
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

        <!-- Active Penalties List (Server Sync) -->
        <div class="w-full max-w-7xl mx-auto px-6 pb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">Riwayat Penalti Aktif</h2>
                    <span class="text-lg font-semibold text-red-600">Total Server: <span id="activePenaltiesTotal">0.00</span></span>
                </div>
                <div id="activePenaltiesList" class="space-y-3 max-h-60 overflow-y-auto">
                    <div class="text-center text-gray-500 py-8" id="noPenaltiesMsg">
                        Belum ada penalti aktif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Struktur data baru: Menggunakan Array 'stack' untuk menampung ID penalti
        // value selalu -0.50 per kejadian
        let categories = [
            { type: 'WAKTU', value: -0.50, stack: [] },
            { type: 'KELUAR_GARIS', value: -0.50, stack: [] },
            { type: 'SENJATA_JATUH', value: -0.50, stack: [] },
            { type: 'SENJATA_TIDAK_JATUH', value: -0.50, stack: [] },
            { type: 'TIDAK_ADA_SALAM_SUARA', value: -0.50, stack: [] }, // Index 4
            { type: 'BAJU_SENJATA_PATAH', value: -0.50, stack: [] }    // Index 5
        ];

        // ---------------------------------------------------------
        // LOGIC UTAMA (ADD & CLEAR INCREMENTAL)
        // ---------------------------------------------------------

        function addNewPenalty(index) {
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
            const category = categories[index];
            
            // Cek apakah ada penalti untuk dihapus?
            if (category.stack.length === 0) {
                // Bisa tambahkan alert kecil atau abaikan
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

        // Fungsi Update Baris Tabel (Visual)
        function updateRowUI(index) {
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
            let total = 0;
            categories.forEach(cat => {
                total += (cat.stack.length * cat.value);
            });
            document.getElementById('grandTotal').textContent = total.toFixed(2);
        }


        // ---------------------------------------------------------
        // KOMUNIKASI SERVER
        // ---------------------------------------------------------

        function getMatchId() {
            const pathParts = window.location.pathname.split('/');
            return pathParts[pathParts.length - 1] || 1;
        }

        function sendPenalty(penaltyId, type, value, action) {
            const matchId = getMatchId();
            
            const data = {
                pertandingan_id: parseInt(matchId),
                penalty_id: penaltyId,
                type: type,
                value: value,
                action: action // 'add' or 'clear'
            };

            // Ganti URL di bawah sesuai route Laravel Anda
            fetch('/dewan-seni-ganda/kirim-penalti', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Pastikan ini dirender oleh Blade
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.status !== 'success') {
                    console.error('Server Error:', result.message);
                    // Optional: Revert local changes if server fails
                } else {
                    console.log(`Success: ${action} ${type}`);
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

        function fetchActivePenalties() {
            const matchId = getMatchId();
            
            // Ganti URL API sesuai route Anda
            fetch(`/api/seni/ganda/events/${matchId}`)
                .then(response => response.json())
                .then(result => {
                    // API returns {status: 'success', data: {...}}
                    if (result.status === 'success' && result.data) {
                        renderActivePenalties(result.data.penalties || [], result.data.total_penalties || 0);
                    } else {
                        console.error('Unexpected API response:', result);
                    }
                })
                .catch(error => console.error('Error fetching penalties:', error));
        }

        function renderActivePenalties(penalties, totalPenalties) {
            const listDiv = document.getElementById('activePenaltiesList');
            const totalSpan = document.getElementById('activePenaltiesTotal');
            
            const activePenalties = penalties.filter(p => p.status === 'active');
            
            totalSpan.textContent = totalPenalties.toFixed(2);

            if (activePenalties.length === 0) {
                listDiv.innerHTML = '<div class="text-center text-gray-500 py-8">Belum ada penalti aktif</div>';
                return;
            }
            
            // Urutkan dari yang terbaru
            activePenalties.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

            listDiv.innerHTML = activePenalties.map(penalty => `
                <div class="bg-red-50 border border-red-200 rounded px-4 py-2 flex justify-between items-center text-sm">
                    <div>
                        <div class="font-bold text-red-700">${penalty.type.replace(/_/g, ' ')}</div>
                        <div class="text-xs text-gray-500">${new Date(penalty.timestamp).toLocaleTimeString('id-ID')}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-red-600 font-bold">${penalty.value.toFixed(2)}</span>
                        <!-- Tombol hapus spesifik dari list bawah -->
                        <button 
                            onclick="forceClearPenalty('${penalty.penalty_id}')" 
                            class="text-gray-400 hover:text-red-500 transition-colors" title="Hapus item ini saja">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Fungsi untuk menghapus penalti spesifik dari list bawah (jika diperlukan)
        function forceClearPenalty(penaltyId) {
            sendPenalty(penaltyId, 'MANUAL_CLEAR', -0.5, 'clear');
            // Catatan: Ini mungkin tidak mengupdate "stack" lokal di tabel atas secara otomatis
            // kecuali kita me-reload halaman atau membuat logika sinkronisasi yang lebih kompleks.
            // Namun secara data di server akan benar.
        }

        // Inisialisasi
        document.addEventListener('DOMContentLoaded', function() {
            updateGrandTotal();
            fetchActivePenalties(); // Initial fetch
            // Removed setInterval - will be triggered by WebSocket events
        });
    </script>
</body>
</html>
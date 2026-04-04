<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penilaian Penalti</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- PENTING: Tambahkan CSRF Token untuk keamanan --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 min-h-screen p-5">

    {{-- Data Tersembunyi untuk dikirim via API --}}
    <input type="hidden" id="pertandingan_id" value="{{ $id }}">

    <div class="max-w-6xl mx-auto bg-white shadow-lg">
        <!-- ... (Header tidak berubah) ... -->
        <div class="p-6">
            <!-- Side Toggle -->
            <div class="flex justify-center mb-6 space-x-4">
                <button onclick="switchSide('1')" id="btnSide1" class="px-6 py-3 rounded-lg font-bold text-white bg-blue-600 shadow-md transform scale-105 ring-4 ring-blue-300 transition-all">
                    SUDUT BIRU
                </button>
                <button onclick="switchSide('2')" id="btnSide2" class="px-6 py-3 rounded-lg font-bold text-gray-500 bg-gray-200 hover:bg-gray-300 transition-all">
                    SUDUT MERAH
                </button>
            </div>

            <div class="border border-gray-300 rounded-md shadow-sm overflow-hidden">
                <table class="w-full">
                    <!-- ... (thead tidak berubah) ... -->
                    <tbody id="penaltyTable" class="bg-white divide-y divide-gray-200">
                        {{-- Data penalti akan digenerate oleh JavaScript --}}
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-between items-center bg-gray-50 px-4 py-3 rounded-md border">
                <div class="text-lg font-bold text-gray-700">Total Penalti</div>
                <div class="text-red-600 text-xl font-bold" id="grandTotal">0.00</div>
            </div>
        </div>
    </div>

    <script>
        // =======================================================
        // KONFIGURASI & STATE
        // =======================================================

        // Ambil data penalti dari database (via controller)
        const dbRules = @json($penaltyRules);
        
        const penaltyTypes = dbRules.map(rule => ({
            name: rule.name,
            value: parseFloat(rule.value),
            id: rule.type
        }));

        // State untuk kedua sisi
        const matchState = {
            '1': {}, // Side 1 active penalties map (id -> true/false)
            '2': {}  // Side 2 active penalties map
        };

        let currentSide = '1'; // Default Side 1

        // Initialize DOM Elements
        let penaltyTableBody, grandTotalEl, pertandinganId, btnSide1, btnSide2;

        function initElements() {
             penaltyTableBody = document.getElementById('penaltyTable');
             grandTotalEl = document.getElementById('grandTotal');
             pertandinganId = document.getElementById('pertandingan_id').value;
             btnSide1 = document.getElementById('btnSide1');
             btnSide2 = document.getElementById('btnSide2');
             
             console.log('Elements initialized:', {
                 table: !!penaltyTableBody,
                 total: !!grandTotalEl,
                 id: pertandinganId,
                 btn1: !!btnSide1,
                 btn2: !!btnSide2
             });
        }

        // ... (existing functions matchState, etc) ...

        // Switch Side Function
        function switchSide(side) {
            currentSide = side;
            
            // Update UI Buttons
            if (side === '1') {
                btnSide1.className = "px-6 py-3 rounded-lg font-bold text-white bg-blue-600 shadow-md transform scale-105 ring-4 ring-blue-300 transition-all";
                btnSide2.className = "px-6 py-3 rounded-lg font-bold text-gray-500 bg-gray-200 hover:bg-gray-300 transition-all";
            } else {
                btnSide1.className = "px-6 py-3 rounded-lg font-bold text-gray-500 bg-gray-200 hover:bg-gray-300 transition-all";
                btnSide2.className = "px-6 py-3 rounded-lg font-bold text-white bg-red-600 shadow-md transform scale-105 ring-4 ring-red-300 transition-all";
            }
            
            render();
            // Re-fetch to ensure sync
            fetchMatchData(); 
        }

        // Fetch initial data
        function fetchMatchData() {
            if (!pertandinganId) return;
            
            fetch(`/api/seni/tunggal-regu/events/${pertandinganId}`)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success' && result.data) {
                        // Reset state
                        matchState['1'] = {};
                        matchState['2'] = {};
                        
                        // Populate active penalties
                        result.data.penalties.forEach(p => {
                            if (p.status === 'active') {
                                const side = p.side || '1';
                                matchState[side][p.type] = true;
                            }
                        });
                        
                        render();
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // Kirim event ke server
        function sendPenaltyEvent(penaltyId, value) {
            console.log(`Mengirim event: side=${currentSide}, penaltyId=${penaltyId}, value=${value}`);
            fetch('/dewan-seni-tunggal-regu/kirim-penalti', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    pertandingan_id: pertandinganId,
                    penalty_id: penaltyId,
                    value: value,
                    side: currentSide
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Respon Server:', data);
                // Kita gunakan local state update agar instan + fetch untuk sync
                setTimeout(fetchMatchData, 500); 
            })
            .catch(error => console.error('Error:', error));
        }

        function addPenalty(penaltyId) {
            // Cek apakah sudah aktif untuk side ini
            if (matchState[currentSide][penaltyId]) return;
            
            // Update local state optimistic
            matchState[currentSide][penaltyId] = true;
            
            // Find value
            const penalty = penaltyTypes.find(p => p.id === penaltyId);
            sendPenaltyEvent(penalty.id, penalty.value);
            render();
        }

        function clearPenalty(penaltyId) {
            // Cek apakah memang aktif
            if (!matchState[currentSide][penaltyId]) return;
            
            // Update local state optimistic
            matchState[currentSide][penaltyId] = false;
            
            sendPenaltyEvent(penaltyId, 0);
            render();
        }

        function calculateTotal(side) {
            let total = 0;
            penaltyTypes.forEach(p => {
                if (matchState[side][p.id]) {
                    total += p.value;
                }
            });
            return total;
        }

        function render() {
            if (!penaltyTableBody) return;
            
            penaltyTableBody.innerHTML = '';
            
            console.log('Rendering penalties:', penaltyTypes.length, 'rules');
            
            if (penaltyTypes.length === 0) {
                penaltyTableBody.innerHTML = '<tr><td colspan="4" class="text-center p-4">Tidak ada data penalti. Hubungi operator.</td></tr>';
                return;
            }
            
            penaltyTypes.forEach((p) => {
                const isActive = matchState[currentSide][p.id];
                
                const tr = document.createElement('tr');
                tr.className = isActive 
                    ? (currentSide === '1' ? 'bg-blue-50' : 'bg-red-50') 
                    : 'hover:bg-gray-50';
                
                const activeColorClass = currentSide === '1' ? 'text-blue-600' : 'text-red-600';
                const btnAddClass = currentSide === '1' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-red-600 hover:bg-red-700';
                
                tr.innerHTML = `
                    <td class="px-4 py-3 text-sm text-gray-700">${p.name}</td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="clearPenalty('${p.id}')" class="bg-gray-500 text-white px-9 py-5 rounded-md hover:bg-gray-600 text-xs font-medium transition-colors">Clear</button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="addPenalty('${p.id}')" class="${btnAddClass} text-white px-9 py-5 rounded-md text-xs font-bold transition-colors shadow-sm">${p.value.toFixed(2)}</button>
                    </td>
                    <td class="px-4 py-3 text-center text-sm ${isActive ? activeColorClass + ' font-bold text-lg' : 'text-gray-400'}">
                        ${isActive ? p.value.toFixed(2) : '0.00'}
                    </td>
                `;
                penaltyTableBody.appendChild(tr);
            });
            
            const total = calculateTotal(currentSide);
            if (grandTotalEl) {
                grandTotalEl.textContent = total.toFixed(2);
                grandTotalEl.className = `text-2xl font-bold ${currentSide === '1' ? 'text-blue-600' : 'text-red-600'}`;
            }
        }

        // Init
        document.addEventListener('DOMContentLoaded', () => {
            console.log('DB Rules:', dbRules);
            initElements();
            render(); // Initial render (static)
            fetchMatchData(); // Fetch state
            
            // Polling every 5 seconds
            setInterval(fetchMatchData, 5000);
        });
    </script>
</body>
</html>
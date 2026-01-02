<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencak Silat Scoreboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .highlight-score {
            background-color: #fef08a; /* Tailwind's yellow-200 */
            transition: background-color 0.2s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen p-2">
    
    <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-red-600 to-red-800 text-white p-3">
            <div class="flex justify-between items-center">
                <div class="text-left"><div class="text-xs font-semibold">KONTINGEN</div><div class="text-sm font-semibold">Nama Atlit</div></div>
                <div class="text-center flex-1"><h1 class="text-xl font-bold">PERTANDINGAN SENI</h1><p class="text-blue-200 text-sm">Arena A</p></div>
                <div class="text-right"><div class="text-sm font-semibold">Arena A</div><div class="text-xs text-blue-200">TUNGGAL</div></div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-3">
            <!-- Penilaian Juri -->
            <div class="mb-4">
                <h2 class="text-lg font-bold text-gray-800 mb-2">Penilaian Juri</h2>
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 p-2 text-left font-semibold text-gray-700 text-sm">Kriteria</th>
                            @for ($i = 1; $i <= ($jumlahJuri ?? 10); $i++)
                            <th class="border border-gray-300 p-2 text-center font-semibold text-gray-700 text-sm">Judge {{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-gray-300 p-2 font-medium text-gray-700 text-sm">Correctness Score</td>
                            @for ($i = 1; $i <= ($jumlahJuri ?? 10); $i++)
                            <td class="border border-gray-300 p-1 text-center text-sm panel-{{ $i }}">9.90</td>
                            @endfor
                        </tr>
                        <tr>
                            <td class="border border-gray-300 p-2 font-medium text-gray-700 text-sm">Flow / Stamina</td>
                            @for ($i = 1; $i <= ($jumlahJuri ?? 10); $i++)
                            <td class="border border-gray-300 p-1 text-center text-sm panel-{{ $i }}">0.00</td>
                            @endfor
                        </tr>
                        <tr class="bg-blue-50">
                            <td class="border border-gray-300 p-2 font-bold text-gray-800 text-sm">Total Score</td>
                            @for ($i = 1; $i <= ($jumlahJuri ?? 10); $i++)
                            <td class="border border-gray-300 p-1 text-center text-base font-bold text-blue-600 panel-{{ $i }}">9.90</td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Statistik & Penalti -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <!-- Waktu & Statistik -->
                <div>
                    <h3 class="text-base font-bold text-gray-800 mb-2">Waktu & Statistik</h3>
                    <table class="w-full border-collapse border border-gray-300">
                        <tr><td class="border border-gray-300 p-2 font-medium text-gray-700 bg-gray-50 text-sm">Median</td><td id="median-score" class="border border-gray-300 p-1 text-center text-base font-bold text-green-600">0.00</td></tr>
                        <tr class="bg-green-50"><td class="border border-gray-300 p-2 font-bold text-gray-800 text-sm">Final Score (Before Penalty)</td><td id="final-score-before-penalty" class="border border-gray-300 p-1 text-center text-lg font-bold text-green-600">0.00</td></tr>
                        <tr><td class="border border-gray-300 p-2 font-medium text-gray-700 bg-gray-50 text-sm">Standard Deviation</td><td id="std-deviation-score" class="border border-gray-300 p-1 text-center text-sm">0.00</td></tr>
                    </table>
                </div>

                <!-- Penalties Section -->
                <div>
                    <h3 class="text-base font-bold text-gray-800 mb-2">Penalti</h3>
                    <table class="w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100"><tr><th class="border border-gray-300 p-2 text-left font-semibold text-gray-700 text-sm">Jenis Penalti</th><th class="border border-gray-300 p-2 text-center font-semibold text-gray-700 text-sm">Nilai</th></tr></thead>
                        <tbody id="penalty-details"><tr><td colspan="2" class="p-4 text-center text-gray-500 italic">Belum ada penalti</td></tr></tbody>
                    </table>
                    <div class="bg-red-50 mt-2 p-2 flex justify-between items-center"><span class="font-bold text-gray-800 text-sm">Total Penalti</span><span id="total-penalty-score" class="text-base font-bold text-red-600">0.00</span></div>
                </div>
            </div>

            <!-- Final Result -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-4 rounded-lg">
                <div class="text-center">
                    <h2 class="text-lg font-bold mb-1">SKOR AKHIR</h2>
                    <div id="final-score-with-penalty" class="text-3xl font-bold mb-1">0.00</div>
                    <p id="final-score-breakdown" class="text-green-100 text-sm">Skor (0.00) - Penalti (0.00)</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Vite for Laravel Echo --}}
    @vite(['resources/js/app.js'])
    
    <script>
        // Configuration
        const MATCH_ID = {{ $id }};
        const NUM_JUDGES = {{ $jumlahJuri }};
        const MAX_JURUS = {{ $maxJurus ?? 14 }};
        const MATCH_TYPE = '{{ $matchType ?? 'tunggal' }}';

        // Data storage
        const judgeScores = {};
        const penaltyStatus = {};

        // Fetch match data from database API
        function fetchMatchData() {
            fetch(`/api/seni/tunggal-regu/events/${MATCH_ID}`)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success' && result.data) {
                        updateJudgeScores(result.data.judges);
                        updatePenalties(result.data.penalties);
                        updateStatistics(result.data.statistics);
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // Update judge scores display
        function updateJudgeScores(judges) {
            Object.entries(judges).forEach(([judgeId, data]) => {
                const juriNumber = data.judge_id;
                const panelCells = document.querySelectorAll(`.panel-${juriNumber}`);
                
                if (panelCells.length >= 3) {
                    panelCells[0].textContent = data.correctness_score.toFixed(2);
                    panelCells[1].textContent = data.category_score.toFixed(2);
                    panelCells[2].textContent = data.total_score.toFixed(2);
                    
                    // Flash animation
                    panelCells[0].classList.add('highlight-score');
                    setTimeout(() => panelCells[0].classList.remove('highlight-score'), 1000);
                }
                
                judgeScores[juriNumber] = data;
            });
        }

        // Update penalties display
        function updatePenalties(penalties) {
            const penaltyDetailsBody = document.getElementById('penalty-details');
            const totalPenaltyScoreEl = document.getElementById('total-penalty-score');
            
            if (!penaltyDetailsBody || !totalPenaltyScoreEl) return;

            let totalPenalty = 0;
            penaltyDetailsBody.innerHTML = '';

            const activePenalties = penalties.filter(p => p.status === 'active');

            if (activePenalties.length === 0) {
                penaltyDetailsBody.innerHTML = '<tr><td colspan="2" class="p-4 text-center text-gray-500 italic">Belum ada penalti</td></tr>';
            } else {
                activePenalties.forEach(penalty => {
                    totalPenalty += penalty.value;
                    const tr = document.createElement('tr');
                    const displayName = penalty.type.replace(/_/g, ' ').toUpperCase();
                    tr.innerHTML = `
                        <td class="border border-gray-300 p-2 text-xs text-gray-700">${displayName}</td>
                        <td class="border border-gray-300 p-1 text-center text-sm text-red-600 font-bold">${penalty.value.toFixed(2)}</td>`;
                    penaltyDetailsBody.appendChild(tr);
                });
            }
            
            totalPenaltyScoreEl.textContent = totalPenalty.toFixed(2);
        }

        // Update statistics display
        function updateStatistics(stats) {
            document.getElementById('median-score').textContent = stats.median.toFixed(2);
            document.getElementById('final-score-before-penalty').textContent = stats.median.toFixed(2);
            document.getElementById('std-deviation-score').textContent = stats.std_dev.toFixed(6);
            document.getElementById('total-penalty-score').textContent = stats.total_penalties.toFixed(2);
            document.getElementById('final-score-with-penalty').textContent = stats.final_score.toFixed(2);
            document.getElementById('final-score-breakdown').textContent = 
                `Skor (${stats.median.toFixed(2)}) - Penalti (${Math.abs(stats.total_penalties).toFixed(2)})`;
        }

        // Setup WebSocket for realtime updates
        function setupWebSocket() {
            // Initial fetch
            fetchMatchData();
            
            // Subscribe to WebSocket channel if available
            if (window.Echo) {
                window.Echo.channel(`pertandingan.${MATCH_ID}`)
                    .listen('.tunggal.score.updated', (e) => {
                        console.log('Score updated:', e);
                        fetchMatchData(); // Refresh all data
                    })
                    .listen('.penalty.updated', (e) => {
                        console.log('Penalty updated:', e);
                        fetchMatchData(); // Refresh all data
                    });
                    
                console.log(`Subscribed to WebSocket: pertandingan.${MATCH_ID}`);
            } else {
                console.warn('Laravel Echo not available, using polling only');
                // Fallback to polling every 2 seconds
                setInterval(fetchMatchData, 2000);
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            console.log(`Match: ${MATCH_ID}, Type: ${MATCH_TYPE}, Max Jurus: ${MAX_JURUS}`);
            setupWebSocket();
        });
    </script>
</body>
</html>
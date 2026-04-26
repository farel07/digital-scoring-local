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
    @include('components.auto-refresh')
</head>
<body class="bg-gray-50 min-h-screen p-2">
    
    <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-red-600 to-red-800 text-white p-3">
            <div class="flex justify-between items-center">
                <div class="text-left"><div class="text-xs font-semibold">KONTINGEN</div><div class="text-sm font-semibold">Nama Atlit</div></div>
                <div class="text-center flex-1">
                    <h1 class="text-xl font-bold">PERTANDINGAN SENI</h1>
                    <p class="text-blue-200 text-sm">Arena A</p>
                    
                    <!-- Side Monitoring Toggle -->
                    <div class="mt-2 flex items-center justify-center gap-3">
                        <button id="btnMonitorSide1" onclick="switchMonitoringSide('1')" 
                                class="px-5 py-2 rounded-lg font-bold text-white transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 bg-blue-600 border-2 border-blue-700 text-sm">
                            Sudut Biru
                        </button>
                        <span id="monitoringSideIndicator" class="px-3 py-1 rounded-md bg-blue-100 text-blue-800 font-semibold text-xs">
                            Monitor: Sudut Biru
                        </span>
                        <button id="btnMonitorSide2" onclick="switchMonitoringSide('2')" 
                                class="px-5 py-2 rounded-lg font-bold text-gray-600 bg-gray-200 border-2 border-gray-300 transition-all duration-200 hover:bg-gray-300 text-sm">
                            Sudut Merah
                        </button>
                    </div>
                </div>
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
                            <td id="judge-{{ $i }}-correctness" class="border border-gray-300 p-1 text-center text-sm panel-{{ $i }}">9.90</td>
                            @endfor
                        </tr>
                        <tr>
                            <td class="border border-gray-300 p-2 font-medium text-gray-700 text-sm">Flow / Stamina</td>
                            @for ($i = 1; $i <= ($jumlahJuri ?? 10); $i++)
                            <td id="judge-{{ $i }}-category" class="border border-gray-300 p-1 text-center text-sm panel-{{ $i }}">0.00</td>
                            @endfor
                        </tr>
                        <tr class="bg-blue-50">
                            <td class="border border-gray-300 p-2 font-bold text-gray-800 text-sm">Total Score</td>
                            @for ($i = 1; $i <= ($jumlahJuri ?? 10); $i++)
                            <td id="judge-{{ $i }}-total" class="border border-gray-300 p-1 text-center text-base font-bold text-blue-600 panel-{{ $i }}">9.90</td>
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
                        <tr><td class="border border-gray-300 p-2 font-medium text-gray-700 bg-gray-50 text-sm">Standard Deviation</td><td id="std-deviation-score" class="border border-gray-300 p-1 text-center text-sm">0.00</td></tr>
                    </table>
                </div>

                <!-- Penalties Section -->
                <div>
                    <h3 class="text-base font-bold text-gray-800 mb-2">Penalti</h3>
                    <table class="w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100"><tr><th class="border border-gray-300 p-2 text-left font-semibold text-gray-700 text-sm">Jenis Penalti</th><th class="border border-gray-300 p-2 text-center font-semibold text-gray-700 text-sm">Nilai</th></tr></thead>
                        <tbody id="penalty-details">
                            <!-- Dynamic Content -->
                        </tbody>
                    </table>
                    <div class="bg-red-50 mt-2 p-2 flex justify-between items-center"><span class="font-bold text-gray-800 text-sm">Total Penalti</span><span id="total-penalty-score" class="text-base font-bold text-red-600">0.00</span></div>
                </div>
            </div>

            <!-- Final Result -->
            <div id="final-score-container" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4 rounded-lg transition-colors duration-300">
                <div class="text-center">
                    <h2 class="text-lg font-bold mb-1">SKOR AKHIR</h2>
                    <div id="final-score-with-penalty" class="text-3xl font-bold mb-1">0.00</div>
                    <p id="final-score-breakdown" class="text-blue-100 text-sm">Score (0.00) - Penalty (0.00)</p>
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

        // Dynamic Penalty Rules
        const dbRules = @json($penaltyRules);
        const penaltyTypes = dbRules.map(rule => ({
            name: rule.name,
            value: parseFloat(rule.value),
            id: rule.type
        }));

        // Side monitoring
        let monitoringSide = '1'; // Default: monitoring Side 1 (Sudut Biru)

        // Data storage
        const judgeScores = {};
        const penaltyStatus = {};

        // Side Toggle Function
        // Side Toggle Function
        function switchMonitoringSide(side) {
            monitoringSide = side;
            
            const btnSide1 = document.getElementById('btnMonitorSide1');
            const btnSide2 = document.getElementById('btnMonitorSide2');
            const indicator = document.getElementById('monitoringSideIndicator');
            const finalContainer = document.getElementById('final-score-container');
            const breakdownText = document.getElementById('final-score-breakdown');
            
            if (side === '1') {
                btnSide1.className = 'px-5 py-2 rounded-lg font-bold text-white transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 bg-blue-600 border-2 border-blue-700 text-sm';
                btnSide2.className = 'px-5 py-2 rounded-lg font-bold text-gray-600 bg-gray-200 border-2 border-gray-300 transition-all duration-200 hover:bg-gray-300 text-sm';
                indicator.className = 'px-3 py-1 rounded-md bg-blue-100 text-blue-800 font-semibold text-xs';
                indicator.textContent = 'Monitor: Sudut Biru';
                
                if (finalContainer) {
                    finalContainer.classList.remove('from-red-500', 'to-red-600');
                    finalContainer.classList.add('from-blue-500', 'to-blue-600');
                }
                if (breakdownText) breakdownText.className = 'text-blue-100 text-sm';
            } else {
                btnSide1.className = 'px-5 py-2 rounded-lg font-bold text-gray-600 bg-gray-200 border-2 border-gray-300 transition-all duration-200 hover:bg-gray-300 text-sm';
                btnSide2.className = 'px-5 py-2 rounded-lg font-bold text-white transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 bg-red-600 border-2 border-red-700 text-sm';
                indicator.className = 'px-3 py-1 rounded-md bg-red-100 text-red-800 font-semibold text-xs';
                indicator.textContent = 'Monitor: Sudut Merah';
                
                if (finalContainer) {
                    finalContainer.classList.remove('from-blue-500', 'to-blue-600');
                    finalContainer.classList.add('from-red-500', 'to-red-600');
                }
                if (breakdownText) breakdownText.className = 'text-red-100 text-sm';
            }
            
            // Re-fetch and filter data
            fetchMatchData();
        }

        // Fetch match data from database API
        function fetchMatchData() {
            fetch(`/api/seni/tunggal-regu/events/${MATCH_ID}`)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success' && result.data) {
                        updateJudgeScores(result.data.judges);
                        
                        // Calculate stats for CURRENT monitored side only
                        calculateSideStatistics(result.data.judges, monitoringSide);
                        
                        updatePenalties(result.data.penalties, monitoringSide);
                        
                        calculateFinalScore(result.data.judges, result.data.penalties, monitoringSide);
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
        }


        // Update judge scores display - FILTER BY MONITORING SIDE
        function updateJudgeScores(judges) {
            // First, reset ALL judge panels to default (9.90, 0.00, 9.90)
            for (let juriNum = 1; juriNum <= NUM_JUDGES; juriNum++) {
                const panelCells = document.querySelectorAll(`.panel-${juriNum}`);
                if (panelCells.length >= 3) {
                    panelCells[0].textContent = '9.90';  // correctness_score default
                    panelCells[1].textContent = '0.00';  // category_score default
                    panelCells[2].textContent = '9.90';  // total_score default
                }
            }
            
            // FILTER: Only show judges for the monitoring side
            const filteredJudges = Object.entries(judges).filter(([judgeId, data]) => {
                return data.side === monitoringSide;
            });
            
            console.log(`[Tunggal/Regu] Filtering judges for side ${monitoringSide}:`, filteredJudges.length, 'matches');
            
            // Update filtered judges
            filteredJudges.forEach(([judgeId, data]) => {
                const juriNumber = data.judge_id;
                const correctnessCell = document.getElementById(`judge-${juriNumber}-correctness`);
                const categoryCell = document.getElementById(`judge-${juriNumber}-category`);
                const totalCell = document.getElementById(`judge-${juriNumber}-total`);
                
                console.log(`[DEBUG] Updating judge ${juriNumber}`);
                console.log(`[DEBUG] Cells: correctness=${!!correctnessCell}, category=${!!categoryCell}, total=${!!totalCell}`);
                console.log(`[DEBUG] Data:`, data);
                
                if (correctnessCell && categoryCell && totalCell) {
                    correctnessCell.textContent = data.correctness_score.toFixed(2);
                    categoryCell.textContent = data.category_score.toFixed(2);
                    totalCell.textContent = data.total_score.toFixed(2);
                    
                    console.log(`[DEBUG] Updated: correctness=${correctnessCell.textContent}, category=${categoryCell.textContent}, total=${totalCell.textContent}`);
                    
                    const previousData = judgeScores[juriNumber];
                    const hasChanged = !previousData || 
                        previousData.correctness_score !== data.correctness_score ||
                        previousData.category_score !== data.category_score;
                    
                    console.log(`[DEBUG] hasChanged: ${hasChanged}`);
                    
                    if (hasChanged) {
                        correctnessCell.classList.add('highlight-score');
                        totalCell.classList.add('highlight-score');
                        setTimeout(() => {
                            correctnessCell.classList.remove('highlight-score');
                            totalCell.classList.remove('highlight-score');
                        }, 1000);
                    }
                    
                    judgeScores[juriNumber] = data;
                } else {
                    console.error(`[ERROR] Cells not found for judge ${juriNumber}`);
                }
            });
        }

        // Update penalties display - FILTER BY SIDE & DYNAMIC RULES
        function updatePenalties(penalties, side) {
            const penaltyDetailsBody = document.getElementById('penalty-details');
            const totalPenaltyScoreEl = document.getElementById('total-penalty-score');
            
            if (!penaltyDetailsBody || !totalPenaltyScoreEl) return;

            penaltyDetailsBody.innerHTML = '';
            
            // Filter active penalties for this side
            const sidePenalties = penalties.filter(p => 
                p.status === 'active' && (p.side === side || (!p.side && side === '1'))
            );
            
            // Map active penalties for easy lookup
            const activeMap = {};
            sidePenalties.forEach(p => {
                activeMap[p.type] = true;
            });

            let totalPenalty = 0;
            let hasActivePenalty = false;
            
            if (penaltyTypes.length === 0) {
                 penaltyDetailsBody.innerHTML = '<tr><td colspan="2" class="p-2 text-center text-gray-400 italic text-xs">Rule tidak ditemukan</td></tr>';
            } else {
                penaltyTypes.forEach(rule => {
                    const isActive = activeMap[rule.id];
                    if (isActive) {
                        totalPenalty += rule.value;
                        hasActivePenalty = true;
                        
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="border border-gray-300 p-2 text-sm text-gray-700 font-medium border-b hover:bg-gray-50">${rule.name}</td>
                            <td class="border border-gray-300 p-2 text-center text-sm text-red-600 font-bold border-b hover:bg-gray-50">${Math.abs(rule.value).toFixed(2)}</td>`;
                        penaltyDetailsBody.appendChild(tr);
                    }
                });
            }
            
            if (!hasActivePenalty) {
                 penaltyDetailsBody.innerHTML = '<tr><td colspan="2" class="p-4 text-center text-gray-400 italic text-sm">Tidak ada penalti aktif</td></tr>';
            }
            
            totalPenaltyScoreEl.textContent = Math.abs(totalPenalty).toFixed(2);
        }

        // Calculate statistics from filtered judge scores
        function calculateSideStatistics(judges, side) {
            // Filter judges by side
            const filteredJudges = Object.values(judges).filter(data => data.side === side);
            
            // Build scores array (use defaults 9.90 for judges who haven't submitted)
            const scores = [];
            for (let i = 1; i <= NUM_JUDGES; i++) {
                const judgeData = filteredJudges.find(j => j.judge_id === i);
                scores.push(judgeData ? judgeData.total_score : 9.90);
            }
            
            scores.sort((a, b) => a - b);
            
            // Calculate median
            const median = scores.length % 2 === 0
                ? (scores[scores.length / 2 - 1] + scores[scores.length / 2]) / 2
                : scores[Math.floor(scores.length / 2)];
            
            // Calculate mean
            const mean = scores.reduce((sum, s) => sum + s, 0) / scores.length;
            
            // Calculate standard deviation
            const variance = scores.reduce((sum, s) => sum + Math.pow(s - mean, 2), 0) / scores.length;
            const stdDev = Math.sqrt(variance);
            
            // Update DOM
            const medianEl = document.getElementById('median-score');
            const stdDevEl = document.getElementById('std-deviation-score');
            
            if (medianEl) medianEl.textContent = median.toFixed(2);
            if (stdDevEl) stdDevEl.textContent = stdDev.toFixed(6);
        }

        // Calculate final score
        function calculateFinalScore(judges, penalties, side) {
            // Filter judges by side
            const filteredJudges = Object.values(judges).filter(data => data.side === side);
            
            // Build scores array
            const scores = [];
            for (let i = 1; i <= NUM_JUDGES; i++) {
                const judgeData = filteredJudges.find(j => j.judge_id === i);
                scores.push(judgeData ? judgeData.total_score : 9.90);
            }
            
            scores.sort((a, b) => a - b);
            
            // Calculate median
            const median = scores.length % 2 === 0
                ? (scores[scores.length / 2 - 1] + scores[scores.length / 2]) / 2
                : scores[Math.floor(scores.length / 2)];
            
            // Filter active penalties for this side
            const sidePenalties = penalties.filter(p => p.status === 'active' && (p.side === side || (!p.side && side === '1')));
            
            let totalPenalties = 0;
            // Iterate active penalties and sum their values
            sidePenalties.forEach(p => {
                 totalPenalties += parseFloat(p.value);
            });
            
            // Final score = median + penalties (assuming penalties are negative or correctly signed)
            // Note: In typical Silat rules, penalties are deductions, so if 'value' is negative, we add it. 
            // If value is positive (e.g. 0.5 deduction), we subtract. 
            // Based on earlier code: `median + totalPenalties`, implying `totalPenalties` contains negative values?
            // Checking `PenaltyRuleSeeder`: values are like -0.5, -5. So adding is correct.
            
            const finalScore = median + totalPenalties;
            
            // Update DOM
            const finalEl = document.getElementById('final-score-with-penalty');
            const breakdownEl = document.getElementById('final-score-breakdown');
            
            if (finalEl) finalEl.textContent = finalScore.toFixed(2);
            if (breakdownEl) breakdownEl.textContent = `Score (${median.toFixed(2)}) - Penalty (${Math.abs(totalPenalties).toFixed(2)})`;
        }

        // Remove old updateStatistics function - replaced by calculateSideStatistics + calculateFinalScore

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
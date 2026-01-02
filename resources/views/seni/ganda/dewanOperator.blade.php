<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Perhitungan Skor Pencak Silat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/js/app.js'])
    <style>
        @keyframes flashYellow {
            0%, 100% { background-color: transparent; }
            50% { background-color: rgb(254 240 138); }
        }
        .flash-animation {
            animation: flashYellow 1.5s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen overflow-hidden">
    <div class="w-full h-screen p-4 overflow-y-auto">
        <!-- Header -->
        <div class="mb-4 text-center bg-white rounded-xl shadow-lg p-4 border border-gray-200">
            <h1 class="text-5xl font-bold text-gray-800 mb-2">
                Sistem Perhitungan Skor Pencak Silat
            </h1>
            <p class="text-2xl text-gray-700">
                <span class="font-semibold text-gray-800">Panel Penilaian Juri - <span id="jumlahJuri">{{ $jumlahJuri ?? 4 }} Juri</span></span>
                <span class="mx-3 text-gray-400">|</span>
                <span class="font-semibold">Pertandingan ID: <span class="text-blue-600">#{{ $id }}</span></span>
                <span class="text-lg bg-green-500 text-white px-4 py-1 rounded-full font-medium ml-3" id="statusIndicator">
                    🔴 Connecting...
                </span>
                <span class="text-base text-gray-500 ml-2">Last update: <span id="lastUpdate">-</span></span>
            </p>
        </div>

        <!-- Judge Panel Table -->
        <div class="mb-4 overflow-x-auto bg-white rounded-xl shadow-lg p-4 border border-gray-200">
            <table class="w-full border-collapse text-center">
                <thead id="tableHeader">
                    <!-- Will be dynamically generated -->
                </thead>
                <tbody id="tableBody">
                    <!-- Will be dynamically generated -->
                </tbody>
            </table>
        </div>

        <!-- Sorted Judge Card -->
        <div id="sortedSection" class="mb-4 bg-blue-50 rounded-xl shadow-lg p-4 border border-blue-200 hidden">
            <h3 class="text-3xl font-bold text-blue-800 mb-3 text-center">
                Sorted Judge Scores
            </h3>
            <div id="sortedScores" class="flex justify-center gap-4 mb-3 flex-wrap">
                <!-- Sorted scores will be rendered here -->
            </div>
            <div class="text-center text-xl text-blue-700 font-medium">
                <span class="bg-yellow-200 px-5 py-2 rounded-full font-semibold">Median Values</span> - Nilai tengah dari juri
            </div>
        </div>

        <!-- Statistics and Penalties Grid -->
        <div class="grid grid-cols-2 gap-4">
            <!-- Left Column: Statistics -->
            <div class="space-y-3">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">
                    📊 Statistics
                </h2>
                
                <div id="resultsSection" class="space-y-3">
                    <!-- Median -->
                    <div class="bg-blue-50 border-2 border-blue-300 rounded-xl p-4 text-center shadow-md">
                        <h4 class="text-2xl font-semibold text-blue-800 mb-2">Median</h4>
                        <div class="text-6xl font-mono text-blue-600 font-bold" id="medianValue">-</div>
                    </div>
                    
                    <!-- Final Score -->
                    <div class="bg-green-50 border-2 border-green-300 rounded-xl p-4 text-center shadow-md">
                        <h4 class="text-2xl font-semibold text-green-800 mb-2">Final Score</h4>
                        <div class="text-6xl font-mono text-green-600 font-bold" id="finalScore">-</div>
                    </div>
                    
                    <!-- Standard Deviation -->
                    <div class="bg-orange-50 border-2 border-orange-300 rounded-xl p-4 text-center shadow-md">
                        <h4 class="text-2xl font-semibold text-orange-800 mb-2">Standard Deviation</h4>
                        <div class="text-4xl font-mono text-orange-600 font-bold" id="stdDevValue">-</div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Penalties Table -->
            <div>
                <h2 class="text-3xl font-bold text-gray-800 mb-2">
                    ⚠️ Penalties
                </h2>
                <div class="bg-red-50 border-2 border-red-300 rounded-xl p-4 shadow-md">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-2xl font-semibold text-red-800">Penalty Summary</h3>
                        <span class="text-xl font-bold text-red-700 bg-red-200 px-3 py-1 rounded-full">
                            Total: <span id="totalPenalties">0.00</span>
                        </span>
                    </div>
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b-2 border-red-200">
                                <th class="py-2 text-lg font-semibold text-gray-700">Kategori</th>
                                <th class="py-2 text-lg font-semibold text-gray-700 text-center">Jumlah</th>
                                <th class="py-2 text-lg font-semibold text-gray-700 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody id="penaltiesTableBody">
                            <!-- Will be rendered dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const NUM_JUDGES = {{ $jumlahJuri ?? 4 }};
        const MATCH_ID = {{ $id }};
        let lastEventData = null;
        let previousJudgeData = {};
        let previousPenalties = [];

        // Get match_id from passed parameter
        function getMatchId() {
            return MATCH_ID;
        }

        // Initialize default judges (empty data)
        function initializeDefaultJudges() {
            const defaultJudges = [];
            for (let i = 1; i <= NUM_JUDGES; i++) {
                defaultJudges.push({
                    judge_id: i,
                    judge_name: `Juri ${i}`,
                    scores: {
                        teknik: 0,
                        kekuatan: 0,
                        penampilan: 0
                    },
                    total: 9.10,
                    last_update: null
                });
            }
            return defaultJudges;
        }

        // Fetch events from API
        async function fetchEvents() {
            const matchId = getMatchId();
            try {
                const response = await fetch(`/api/seni/ganda/events/${matchId}`);
                const result = await response.json();
                
                if (result.status === 'success') {
                    document.getElementById('statusIndicator').textContent = '🟢 Connected';
                    document.getElementById('statusIndicator').className = 'text-sm bg-green-100 text-green-700 px-3 py-1 rounded-full';
                    
                    // Check for changes and trigger flash animation
                    checkForChanges(result.data);
                    
                    lastEventData = result.data;
                    renderDashboard(result.data);
                } else {
                    console.error('Failed to fetch events:', result.message);
                    document.getElementById('statusIndicator').textContent = '🔴 Error';
                    document.getElementById('statusIndicator').className = 'text-sm bg-red-100 text-red-700 px-3 py-1 rounded-full';
                }
            } catch (error) {
                console.error('Error fetching events:', error);
                document.getElementById('statusIndicator').textContent = '🔴 Disconnected';
                document.getElementById('statusIndicator').className = 'text-sm bg-red-100 text-red-700 px-3 py-1 rounded-full';
            }
        }

        // Check for changes and trigger flash animation
        function checkForChanges(newData) {
            const currentJudges = newData.judges || {};
            const currentPenalties = newData.penalties || [];
            
            // Check for judge updates
            Object.keys(currentJudges).forEach(judgeId => {
                const currentJudge = currentJudges[judgeId];
                const previousJudge = previousJudgeData[judgeId];
                
                if (previousJudge) {
                    // Check if scores changed
                    if (JSON.stringify(currentJudge.scores) !== JSON.stringify(previousJudge.scores) ||
                        currentJudge.total !== previousJudge.total) {
                        flashJudgeCell(judgeId);
                    }
                } else {
                    // New judge submitted
                    flashJudgeCell(judgeId);
                }
            });
            
            // Check for penalty updates
            if (currentPenalties.length !== previousPenalties.length) {
                flashPenaltiesSection();
            }
            
            // Update previous data
            previousJudgeData = JSON.parse(JSON.stringify(currentJudges));
            previousPenalties = JSON.parse(JSON.stringify(currentPenalties));
        }

        // Flash specific judge column with yellow background
        function flashJudgeCell(judgeId) {
            setTimeout(() => {
                const cells = document.querySelectorAll(`[data-judge-id="${judgeId}"]`);
                cells.forEach(cell => {
                    cell.classList.add('flash-animation');
                    setTimeout(() => {
                        cell.classList.remove('flash-animation');
                    }, 1500);
                });
            }, 100);
        }

        // Flash penalties section
        function flashPenaltiesSection() {
            const section = document.getElementById('penaltiesSection');
            if (section && !section.classList.contains('hidden')) {
                section.classList.add('flash-animation');
                setTimeout(() => {
                    section.classList.remove('flash-animation');
                }, 1500);
            }
        }

        // Render the entire dashboard
        function renderDashboard(data) {
            const judges = data.judges || {};
            const penalties = data.penalties || [];
            
            // Merge server data with default judges
            const defaultJudges = initializeDefaultJudges();
            const judgeArray = defaultJudges.map(defaultJudge => {
                return judges[defaultJudge.judge_id] || defaultJudge;
            });

            // Update header info
            document.getElementById('lastUpdate').textContent = data.last_update ? new Date(data.last_update).toLocaleTimeString('id-ID') : '-';

            // Render penalties
            renderPenalties(penalties, data.total_penalties);

            // Always render table with fixed number of judges
            renderTable(judgeArray);

            // Always calculate statistics with ALL judges (including default 9.10)
            // This ensures judges who haven't submitted are counted as 9.10
            renderSortedScores(judgeArray);
            calculateStatistics(judgeArray, data.total_penalties || 0);
        }

        // Render penalties section as table
        function renderPenalties(penalties, totalPenalties) {
            const tableBody = document.getElementById('penaltiesTableBody');
            const totalPenaltiesSpan = document.getElementById('totalPenalties');

            // Define all possible penalty categories (matching dewan.blade.php)
            const penaltyCategories = [
                { type: 'WAKTU', label: 'WAKTU' },
                { type: 'KELUAR_GARIS', label: 'KELUAR GARIS' },
                { type: 'SENJATA_JATUH', label: 'SENJATA JATUH TIDAK SESUAI' },
                { type: 'SENJATA_TIDAK_JATUH', label: 'SENJATA TIDAK JATUH SESUAI' },
                { type: 'TIDAK_ADA_SALAM_SUARA', label: 'TIDAK ADA SALAM / SUARA' },
                { type: 'BAJU_SENJATA_PATAH', label: 'BAJU / SENJATA PATAH' }
            ];

            // Count penalties by type
            const penaltyCounts = {};
            const activePenalties = penalties.filter(p => p.status === 'active');
            
            activePenalties.forEach(penalty => {
                if (!penaltyCounts[penalty.type]) {
                    penaltyCounts[penalty.type] = {
                        count: 0,
                        value: penalty.value
                    };
                }
                penaltyCounts[penalty.type].count++;
            });

            // Render table rows
            tableBody.innerHTML = penaltyCategories.map(category => {
                const data = penaltyCounts[category.type];
                const count = data ? data.count : 0;
                const total = data ? (data.count * data.value) : 0;
                
                const rowClass = count > 0 ? 'bg-red-100 font-semibold' : '';
                const countColor = count > 0 ? 'text-red-700' : 'text-gray-500';
                const totalColor = count > 0 ? 'text-red-600 font-bold' : 'text-gray-500';
                
                return `
                    <tr class="border-b border-red-100 ${rowClass}">
                        <td class="py-2 text-base text-gray-700">${category.label}</td>
                        <td class="py-2 text-center text-2xl ${countColor}">${count}</td>
                        <td class="py-2 text-right text-2xl font-mono ${totalColor}">${total.toFixed(2)}</td>
                    </tr>
                `;
            }).join('');

            // Update total
            totalPenaltiesSpan.textContent = totalPenalties.toFixed(2);
        }

        // Render sorted scores
        function renderSortedScores(judgeArray) {
            const sortedSection = document.getElementById('sortedSection');
            const sortedScoresDiv = document.getElementById('sortedScores');

            const scores = judgeArray.map(j => j.total).sort((a, b) => a - b);
            const median = calculateMedian(scores);

            sortedSection.classList.remove('hidden');

            sortedScoresDiv.innerHTML = scores.map((score, index) => {
                const isMedian = (scores.length % 2 === 0) ? 
                    (score === scores[Math.floor(scores.length / 2) - 1] || score === scores[Math.floor(scores.length / 2)]) :
                    (score === scores[Math.floor(scores.length / 2)]);
                
                // Mark judges who haven't submitted with lighter style
                const judgeWithThisScore = judgeArray.find(j => j.total === score);
                const hasSubmitted = judgeWithThisScore && judgeWithThisScore.last_update !== null;
                const opacity = hasSubmitted ? '' : 'opacity-60';
                
                const bgColor = isMedian ? 'bg-yellow-200 border-yellow-400' : 'bg-white border-blue-200';
                
                return `<span class="px-6 py-3 ${bgColor} ${opacity} border-2 rounded-lg font-mono text-2xl font-bold shadow-md">${score.toFixed(2)}</span>`;
            }).join ('');
        }

        // Render judge table
        function renderTable(judgeArray) {
            const tableHeader = document.getElementById('tableHeader');
            const tableBody = document.getElementById('tableBody');

            // Generate header
            let headerHTML = '<tr class="bg-gray-100"><th class="px-6 py-3 border-2 border-gray-300 text-3xl font-bold text-gray-800">Judge</th>';
            judgeArray.forEach((judge, idx) => {
                headerHTML += `<th class="px-4 py-3 border-2 border-gray-300 text-3xl font-bold text-gray-800" data-judge-id="${judge.judge_id}">${idx + 1}</th>`;
            });
            headerHTML += '</tr>';
            tableHeader.innerHTML = headerHTML;

            // Generate body rows
            const categories = [
                { key: 'teknik', label: 'TEKNIK DASAR', range: '(0.01-0.30)' },
                { key: 'kekuatan', label: 'KEKUATAN & KECEPATAN', range: '(0.01-0.30)' },
                { key: 'penampilan', label: 'PENAMPILAN & GAYA', range: '(0.01-0.30)' }
            ];

            let bodyHTML = '';
            categories.forEach(category => {
                bodyHTML += `<tr>
                    <td class="px-6 py-3 border-2 border-gray-300 bg-gray-50 font-semibold text-left text-2xl text-gray-800">
                        ${category.label}<br><span class="text-lg text-gray-500 font-normal">${category.range}</span>
                    </td>`;
                judgeArray.forEach(judge => {
                    const score = judge.scores[category.key] || 0;
                    const hasData = judge.last_update !== null;
                    const bgClass = hasData ? 'bg-white' : 'bg-gray-100';
                    bodyHTML += `<td class="px-4 py-3 border-2 border-gray-300 text-3xl font-medium ${bgClass}" data-judge-id="${judge.judge_id}">${score.toFixed(2)}</td>`;
                });
                bodyHTML += '</tr>';
            });

            // Total Score row
            bodyHTML += '<tr class="bg-blue-100"><td class="px-6 py-4 border-2 border-gray-300 font-bold text-2xl text-blue-800">Total Score</td>';
            judgeArray.forEach(judge => {
                const hasData = judge.last_update !== null;
                const bgClass = hasData ? 'bg-blue-50' : 'bg-gray-100';
                bodyHTML += `<td class="px-4 py-4 border-2 border-gray-300 font-mono text-4xl font-bold ${bgClass} text-blue-700" data-judge-id="${judge.judge_id}">${judge.total.toFixed(2)}</td>`;
            });
            bodyHTML += '</tr>';

            tableBody.innerHTML = bodyHTML;
        }

        // Render sorted scores
        function renderSortedScores(judgeArray) {
            const sortedSection = document.getElementById('sortedSection');
            const sortedScoresDiv = document.getElementById('sortedScores');

            const scores = judgeArray.map(j => j.total).sort((a, b) => a - b);
            const median = calculateMedian(scores);

            sortedSection.classList.remove('hidden');

            sortedScoresDiv.innerHTML = scores.map((score, index) => {
                const isMedian = (scores.length % 2 === 0) ? 
                    (score === scores[Math.floor(scores.length / 2) - 1] || score === scores[Math.floor(scores.length / 2)]) :
                    (score === scores[Math.floor(scores.length / 2)]);
                
                // Mark judges who haven't submitted with lighter style
                const judgeWithThisScore = judgeArray.find(j => j.total === score);
                const hasSubmitted = judgeWithThisScore && judgeWithThisScore.last_update !== null;
                const opacity = hasSubmitted ? '' : 'opacity-60';
                
                return `<span class="px-6 py-3 ${isMedian ? 'bg-yellow-200 border-yellow-400 font-bold' : 'bg-white border-gray-300'} ${opacity} border-2 rounded-xl font-mono text-2xl shadow-md">${score.toFixed(2)}</span>`;
            }).join('');
        }

        // Calculate median
        function calculateMedian(sortedArray) {
            const len = sortedArray.length;
            if (len === 0) return 0;
            if (len % 2 === 0) {
                return (sortedArray[len / 2 - 1] + sortedArray[len / 2]) / 2;
            } else {
                return sortedArray[Math.floor(len / 2)];
            }
        }

        // Calculate standard deviation
        function calculateStdDev(values, mean) {
            if (values.length === 0) return 0;
            const squaredDiffs = values.map(val => Math.pow(val - mean, 2));
            const variance = squaredDiffs.reduce((a, b) => a + b, 0) / values.length;
            return Math.sqrt(variance);
        }

        // Calculate and display statistics
        function calculateStatistics(judgeArray, totalPenalties) {
            const scores = judgeArray.map(j => j.total).sort((a, b) => a - b);

            const median = calculateMedian(scores);
            const mean = scores.reduce((a, b) => a + b, 0) / scores.length;
            const stdDev = calculateStdDev(scores, mean);

            // Final Score = Median - Total Penalties
            const finalScore = median + totalPenalties; // totalPenalties is already negative

            document.getElementById('medianValue').textContent = median.toFixed(3);
            document.getElementById('finalScore').textContent = finalScore.toFixed(3);
            document.getElementById('stdDevValue').textContent = stdDev.toFixed(6);
        }

        // Setup WebSocket connection for realtime updates
        function setupWebSocket() {
            const matchId = getMatchId();
            
            // Initial fetch to load existing data
            fetchEvents();
            
            // Subscribe to pertandingan channel
            if (window.Echo) {
                window.Echo.channel(`pertandingan.${matchId}`)
                    .listen('.judge.score.updated', (e) => {
                        console.log('Judge score updated:', e);
                        // Re-fetch all data to update display
                        fetchEvents();
                    })
                    .listen('.penalty.updated', (e) => {
                        console.log('Penalty updated:', e);
                        // Re-fetch all data to update display
                        fetchEvents();
                    });
                    
                console.log(`Subscribed to WebSocket channel: pertandingan.${matchId}`);
            } else {
                console.warn('Laravel Echo not available, falling back to polling');
                // Fallback to polling if Echo not available
                setInterval(fetchEvents, 2000);
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Match ID:', getMatchId());
            console.log('Number of Judges:', NUM_JUDGES);
            
            // Initial render with default judges
            renderDashboard({ judges: {}, penalties: [], total_penalties: 0 });
            
            // Setup WebSocket or polling
            setupWebSocket();
        });
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9741b1cf8379ea87',t:'MTc1NjAyNjM5Ni4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>

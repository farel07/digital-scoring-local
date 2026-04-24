<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoreboard Pencak Silat - Tunggal/Regu</title>
    @vite(['resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'display': ['Inter', 'system-ui', 'sans-serif'],
                        'mono': ['JetBrains Mono', 'Courier New', 'monospace']
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .highlight-score {
            animation: scoreFlash 0.8s ease-in-out;
        }
        @keyframes scoreFlash {
            0%   { opacity: 1; transform: scale(1); }
            30%  { opacity: 0.5; transform: scale(1.08); }
            60%  { opacity: 1; transform: scale(1.04); }
            100% { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body class="h-screen bg-gradient-to-br from-slate-300 via-gray-300 to-slate-100 p-3 font-display overflow-hidden">
    <div class="h-full max-w-6xl mx-auto flex flex-col">
        
        <!-- Main Scoreboard Container -->
        <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl overflow-hidden border border-white/20 h-full flex flex-col">
            
            <!-- Header Section -->
            <div class="relative bg-gradient-to-r from-gray-100 via-gray-50 to-gray-100 px-6 py-4 border-b-2 border-red-600">
                <!-- Logo Kiri -->
                <div class="absolute left-4 top-3 w-10 h-10 bg-gradient-to-br from-red-600 to-red-700 rounded-full flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-sm">PS</span>
                </div>
                
                <!-- Logo Kanan -->
                <div class="absolute right-4 top-3 w-10 h-10 bg-gradient-to-br from-red-600 to-red-700 rounded-full flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-sm">PS</span>
                </div>
                
                <!-- Main Title -->
                <div class="text-center">
                    <h1 class="text-3xl font-black text-gray-800 tracking-wider mb-2">
                        PENCAK SILAT
                    </h1>
                    
                    <!-- Sub Header -->
                    <div class="flex justify-between items-center text-sm font-bold text-gray-700">
                        <span class="bg-red-100 px-3 py-1 rounded-lg border border-red-200" id="arenaInfo">Loading...</span>
                        <span class="bg-red-100 px-4 py-1 rounded-lg border border-red-200 text-red-700" id="matchStatus">LOADING</span>
                        <span class="bg-green-100 px-3 py-1 rounded-lg border border-green-200 text-green-700">TUNGGAL/REGU</span>
                    </div>

                    <!-- Side Monitoring Toggle -->
                    <div class="mt-2 flex items-center justify-center gap-2 flex-wrap">
                        @if(($jenisPertandingan ?? 'prestasi') === 'prestasi')
                            <button id="btnMonitorSide_1" onclick="switchMonitoringSide('1')"
                                    class="monitor-btn px-4 py-2 rounded-lg font-bold text-white transition-all duration-200 shadow hover:shadow-lg transform hover:scale-105 bg-blue-600 border-2 border-blue-700 text-sm">
                                Sudut Biru
                            </button>
                            <span id="monitoringSideIndicator" class="px-3 py-1 rounded-md bg-blue-100 text-blue-800 font-semibold text-xs whitespace-nowrap">
                                Monitor: Sudut Biru
                            </span>
                            <button id="btnMonitorSide_2" onclick="switchMonitoringSide('2')"
                                    class="monitor-btn px-4 py-2 rounded-lg font-bold text-gray-600 bg-gray-200 border-2 border-gray-300 transition-all duration-200 hover:bg-gray-300 text-sm">
                                Sudut Merah
                            </button>
                        @else
                            <button id="btnMonitorSide_{{ $allSides->first() ?? 1 }}" onclick="switchMonitoringSide('{{ $allSides->first() ?? 1 }}')" 
                                    class="monitor-btn px-4 py-2 rounded-lg font-bold text-white transition-all duration-200 shadow hover:shadow-lg transform hover:scale-105 bg-purple-600 border-2 border-purple-700 text-sm">
                                Peserta {{ $allSides->first() ?? 1 }}
                            </button>
                            <span id="monitoringSideIndicator" class="px-3 py-1 rounded-md bg-purple-100 text-purple-800 font-semibold text-xs whitespace-nowrap">
                                Monitor: Peserta {{ $allSides->first() ?? 1 }}
                            </span>
                            @foreach($allSides as $sideNum)
                                @if($loop->first) @continue @endif
                                <button id="btnMonitorSide_{{ $sideNum }}" onclick="switchMonitoringSide('{{ $sideNum }}')" 
                                        class="monitor-btn px-4 py-2 rounded-lg font-bold text-gray-600 bg-gray-200 border-2 border-gray-300 transition-all duration-200 hover:bg-gray-300 text-sm">
                                    Peserta {{ $sideNum }}
                                </button>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="flex gap-4 p-4 flex-1">
                
                <!-- Team Section -->
                <div class="flex-[2]">
                    <div class="bg-gradient-to-br from-red-50 to-indigo-50 rounded-xl p-4 border border-red-100 shadow-lg h-full">
                        
                        <!-- Team Info -->
                        <div class="flex items-center gap-4 mb-3">
                            <!-- Flag placeholder -->
                            <div class="relative w-16 h-10 rounded-lg overflow-hidden shadow-lg border-2 border-white bg-gradient-to-br from-red-500 to-red-600">
                                <span class="absolute inset-0 flex items-center justify-center text-white font-bold text-xs">FLAG</span>
                            </div>
                            
                            <!-- Athlete Photos -->
                            <div class="flex gap-2" id="playerAvatars">
                                <!-- Will be filled dynamically -->
                            </div>
                        </div>
                        
                        <!-- Country Name -->
                        <div class="mb-3">
                            <h2 class="text-2xl font-black text-gray-800 tracking-wide" id="contingentName">
                                LOADING...
                            </h2>
                        </div>
                        
                        <!-- Athlete Names -->
                        <div class="bg-white/70 rounded-lg p-3 border border-red-200" id="playerNames">
                            <p class="text-sm font-semibold text-gray-700 leading-relaxed">
                                Loading player names...
                            </p>
                        </div>

                        <!-- Penalty Breakdown -->
                        <div class="mt-3">
                            <p class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Penalti Aktif</p>
                            <div id="penalty-list" class="space-y-1">
                                <p class="text-xs text-gray-400 italic">Tidak ada penalti</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Section -->
                <div class="flex-1">
                    <div id="stats-container" class="bg-gradient-to-br from-slate-50 to-gray-50 rounded-xl p-4 border border-gray-200 shadow-lg h-full flex flex-col justify-center">
                        <div class="text-center space-y-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Median</p>
                                <div id="median-score" class="text-3xl font-mono font-black text-gray-800">0.00</div>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Std Deviation</p>
                                <div id="std-deviation-score" class="text-lg font-mono font-bold text-gray-600">0.000000</div>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-red-700 uppercase tracking-wider">Total Penalti</p>
                                <div id="total-penalty-score" class="text-2xl font-mono font-bold text-red-600">0.00</div>
                            </div>
                            <div class="bg-green-50 rounded-lg py-2 px-3 border border-green-200">
                                <p class="text-xs font-semibold text-green-700 uppercase tracking-wider">Skor Akhir</p>
                                <div id="final-score-with-penalty" class="text-4xl font-mono font-black text-green-600">0.00</div>
                                <p id="final-score-breakdown" class="text-xs text-green-500 mt-1">Score (0.00) - Penalty (0.00)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Score Table Section -->
            <div class="px-4 pb-4 flex-1">
                <div class="bg-gradient-to-r from-red-50 to-indigo-50 rounded-xl p-3 border border-red-100 shadow-lg h-full">
                    <div class="overflow-hidden">
                        <table class="w-full h-full">
                            <thead id="scoreTableHeader">
                                <!-- Will be generated by JavaScript -->
                            </thead>
                            <tbody id="scoreTableBody">
                                <!-- Will be generated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-2 border-t border-gray-200 flex justify-between items-center">
                <div class="font-mono text-xs text-gray-600 bg-white px-2 py-1 rounded border border-gray-200">
                    <span id="timestamp">2022-09-01 22:04:06</span>
                </div>
                <div class="text-xs text-gray-600 font-medium">
                    <span class="text-red-600 font-semibold">EventSilat.Com</span> - Pencak Silat for the World
                </div>
            </div>
        </div>
    </div>

    <script>
        const MATCH_ID = {{ $id }};
        const NUM_JUDGES = {{ $jumlahJuri ?? 4 }};
        const MAX_JURUS = {{ $maxJurus ?? 14 }};
        const MATCH_TYPE = '{{ $matchType ?? 'tunggal' }}';

        // Dynamic Penalty Rules from DB (same as dewanOperator)
        const dbRules = @json($penaltyRules ?? []);
        const penaltyTypes = dbRules.map(rule => ({
            name: rule.name,
            value: parseFloat(rule.value),
            id: rule.type
        }));

        // Side monitoring state (same as dewanOperator)
        let monitoringSide = '1'; // Default: Sudut Biru

        const IS_PEMASALAN = '{{ $jenisPertandingan ?? 'prestasi' }}' === 'pemasalan';

        // Local cache for change detection
        const judgeScores = {};

        // =========================================================
        // Update timestamp
        // =========================================================
        function updateTimestamp() {
            const now = new Date();
            const timestamp = now.getFullYear() + '-' + 
                            String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                            String(now.getDate()).padStart(2, '0') + ' ' + 
                            String(now.getHours()).padStart(2, '0') + ':' + 
                            String(now.getMinutes()).padStart(2, '0') + ':' + 
                            String(now.getSeconds()).padStart(2, '0');
            
            document.getElementById('timestamp').textContent = timestamp;
        }
        
        updateTimestamp();
        setInterval(updateTimestamp, 1000);

        // =========================================================
        // Side Toggle Function (same logic as dewanOperator)
        // =========================================================
        function switchMonitoringSide(side) {
            monitoringSide = side;
            side = String(side);

            const indicator = document.getElementById('monitoringSideIndicator');
            const statsContainer = document.getElementById('stats-container');

            // Loop all monitor buttons and update styles
            document.querySelectorAll('.monitor-btn').forEach(btn => {
                const btnSide = btn.id.split('_')[1];
                if (btnSide === side) {
                    btn.className = 'monitor-btn px-4 py-2 rounded-lg font-bold text-white transition-all duration-200 shadow hover:shadow-lg transform hover:scale-105 border-2 text-sm ' + 
                        (IS_PEMASALAN ? 'bg-purple-600 border-purple-700' : (side === '1' ? 'bg-blue-600 border-blue-700' : 'bg-red-600 border-red-700'));
                } else {
                    btn.className = 'monitor-btn px-4 py-2 rounded-lg font-bold text-gray-600 bg-gray-200 border-2 border-gray-300 transition-all duration-200 hover:bg-gray-300 text-sm';
                }
            });

            // Update Indicator text and styles
            if (IS_PEMASALAN) {
                indicator.textContent = 'Monitor: Peserta ' + side;
                indicator.className = 'px-3 py-1 rounded-md bg-purple-100 text-purple-800 font-semibold text-xs whitespace-nowrap';
            } else {
                if (side === '1') {
                    indicator.textContent = 'Monitor: Sudut Biru';
                    indicator.className = 'px-3 py-1 rounded-md bg-blue-100 text-blue-800 font-semibold text-xs whitespace-nowrap';
                } else {
                    indicator.textContent = 'Monitor: Sudut Merah';
                    indicator.className = 'px-3 py-1 rounded-md bg-red-100 text-red-800 font-semibold text-xs whitespace-nowrap';
                }
            }

            if (statsContainer) {
                if (IS_PEMASALAN) {
                    statsContainer.className = 'bg-gradient-to-br from-slate-50 to-purple-50 rounded-xl p-4 border border-purple-200 shadow-lg h-full flex flex-col justify-center';
                } else {
                    if (side === '1') {
                        statsContainer.className = 'bg-gradient-to-br from-slate-50 to-blue-50 rounded-xl p-4 border border-blue-200 shadow-lg h-full flex flex-col justify-center';
                    } else {
                        statsContainer.className = 'bg-gradient-to-br from-slate-50 to-red-50 rounded-xl p-4 border border-red-200 shadow-lg h-full flex flex-col justify-center';
                    }
                }
            }

            // Re-fetch and re-render data for the selected side
            fetchMatchData();
        }
        
        // =========================================================
        // Initialize score table (same structure as before)
        // =========================================================
        function initScoreTable() {
            const headerContainer = document.getElementById('scoreTableHeader');
            const bodyContainer = document.getElementById('scoreTableBody');
            
            // Generate header row
            let headerHTML = '<tr>';
            headerHTML += '<th class="bg-gradient-to-br from-red-600 to-red-700 text-white font-bold text-sm py-2 px-2 border border-red-800 first:rounded-tl-lg">Kriteria</th>';
            for (let i = 1; i <= NUM_JUDGES; i++) {
                const roundedClass = (i === NUM_JUDGES) ? 'last:rounded-tr-lg' : '';
                headerHTML += `<th class="bg-gradient-to-br from-red-600 to-red-700 text-white font-bold text-lg py-2 px-2 border border-red-800 ${roundedClass}">${i}</th>`;
            }
            headerHTML += '</tr>';
            headerContainer.innerHTML = headerHTML;
            
            // Generate body rows
            let bodyHTML = '';
            
            // Correctness Score row
            bodyHTML += '<tr><td class="bg-gray-50 border border-red-700 px-2 py-2 font-semibold text-gray-800 text-xs">Correctness</td>';
            for (let i = 1; i <= NUM_JUDGES; i++) {
                bodyHTML += `<td id="judge-${i}-correctness" class="bg-gradient-to-br from-red-500 to-red-600 text-white font-bold text-lg py-2 px-2 border border-red-700 transition-all duration-300 panel-${i}">9.90</td>`;
            }
            bodyHTML += '</tr>';
            
            // Flow / Stamina row
            bodyHTML += '<tr><td class="bg-gray-50 border border-red-700 px-2 py-2 font-semibold text-gray-800 text-xs">Flow/Stamina</td>';
            for (let i = 1; i <= NUM_JUDGES; i++) {
                bodyHTML += `<td id="judge-${i}-category" class="bg-gradient-to-br from-red-500 to-red-600 text-white font-bold text-lg py-2 px-2 border border-red-700 transition-all duration-300 panel-${i}">0.00</td>`;
            }
            bodyHTML += '</tr>';
            
            // Total Score row
            bodyHTML += '<tr><td class="bg-blue-100 border border-red-700 px-2 py-2 font-bold text-gray-800 text-xs first:rounded-bl-lg">Total</td>';
            for (let i = 1; i <= NUM_JUDGES; i++) {
                const roundedClass = (i === NUM_JUDGES) ? 'last:rounded-br-lg' : '';
                bodyHTML += `<td id="judge-${i}-total" class="bg-gradient-to-br from-blue-500 to-blue-600 text-white font-bold text-xl py-2 px-2 border border-red-700 transition-all duration-300 ${roundedClass} panel-${i}">9.90</td>`;
            }
            bodyHTML += '</tr>';
            
            bodyContainer.innerHTML = bodyHTML;
        }

        // =========================================================
        // Fetch match info (contingent, players, arena, status)
        // =========================================================
        async function fetchMatchInfo() {
            try {
                const response = await fetch(`/api/superadmin/matches/${MATCH_ID}`);
                const result = await response.json();
                
                if (result.success && result.data) {
                    renderMatchInfo(result.data);
                }
            } catch (error) {
                console.error('Error fetching match info:', error);
            }
        }
        
        // Render match info — filter by monitoringSide
        function renderMatchInfo(match) {
            const players = match.players || [];
            const sideNumber = parseInt(monitoringSide);
            const sidePlayers = players.filter(p => p.side_number === sideNumber);
            
            if (sidePlayers.length > 0) {
                const firstPlayer = sidePlayers[0];
                const contingent = firstPlayer.player_contingent;
                
                document.getElementById('contingentName').textContent = contingent.toUpperCase();

                const arenaName = match.arena ? match.arena.arena_name : 'Arena';
                document.getElementById('arenaInfo').textContent = arenaName;
                
                const statusMap = {
                    'belum_dimulai': 'READY',
                    'berlangsung': 'LIVE',
                    'selesai': 'FINAL'
                };
                const status = statusMap[match.status] || match.status.toUpperCase();
                document.getElementById('matchStatus').textContent = status;
                
                // Render player avatars
                const avatarsContainer = document.getElementById('playerAvatars');
                avatarsContainer.innerHTML = sidePlayers.map(player => {
                    const initials = player.player_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                    const colorClass = monitoringSide === '1'
                        ? 'from-blue-500 to-blue-600'
                        : 'from-red-500 to-red-600';
                    return `
                        <div class="w-12 h-12 bg-gradient-to-br ${colorClass} rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg border-2 border-white">
                            ${initials}
                        </div>
                    `;
                }).join('');
                
                // Render player names
                const namesContainer = document.getElementById('playerNames');
                namesContainer.innerHTML = sidePlayers.map(player => `
                    <p class="text-sm font-semibold text-gray-700 leading-relaxed">
                        | ${player.player_name.toUpperCase()}
                    </p>
                `).join('');
            } else {
                document.getElementById('contingentName').textContent = 'TIDAK ADA DATA';
                document.getElementById('playerNames').innerHTML = '<p class="text-xs text-gray-400 italic">Tidak ada peserta pada sudut ini</p>';
            }
        }
        
        // =========================================================
        // Fetch scoring events from API (same endpoint as dewanOperator)
        // =========================================================
        function fetchMatchData() {
            fetch(`/api/seni/tunggal-regu/events/${MATCH_ID}`)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success' && result.data) {
                        updateJudgeScores(result.data.judges || {});
                        calculateSideStatistics(result.data.judges || {}, monitoringSide);
                        updatePenalties(result.data.penalties || [], monitoringSide);
                        calculateFinalScore(result.data.judges || {}, result.data.penalties || [], monitoringSide);
                    }
                })
                .catch(error => console.error('Error fetching events:', error));
        }

        // =========================================================
        // Update judge scores display — FILTER BY MONITORING SIDE
        // (same logic as dewanOperator)
        // =========================================================
        function updateJudgeScores(judges) {
            // Reset ALL judge panels to default
            for (let juriNum = 1; juriNum <= NUM_JUDGES; juriNum++) {
                const correctnessCell = document.getElementById(`judge-${juriNum}-correctness`);
                const categoryCell = document.getElementById(`judge-${juriNum}-category`);
                const totalCell = document.getElementById(`judge-${juriNum}-total`);
                if (correctnessCell) correctnessCell.textContent = '9.90';
                if (categoryCell) categoryCell.textContent = '0.00';
                if (totalCell) totalCell.textContent = '9.90';
            }

            // Filter: only show judges for the monitoring side
            const filteredJudges = Object.entries(judges).filter(([judgeId, data]) => {
                return data.side === monitoringSide;
            });

            filteredJudges.forEach(([judgeId, data]) => {
                const juriNumber = data.judge_id;
                const correctnessCell = document.getElementById(`judge-${juriNumber}-correctness`);
                const categoryCell = document.getElementById(`judge-${juriNumber}-category`);
                const totalCell = document.getElementById(`judge-${juriNumber}-total`);

                if (correctnessCell && categoryCell && totalCell) {
                    const previousData = judgeScores[juriNumber];
                    const hasChanged = !previousData ||
                        previousData.correctness_score !== data.correctness_score ||
                        previousData.category_score !== data.category_score;

                    correctnessCell.textContent = data.correctness_score.toFixed(2);
                    categoryCell.textContent = data.category_score.toFixed(2);
                    totalCell.textContent = data.total_score.toFixed(2);

                    if (hasChanged) {
                        correctnessCell.classList.add('highlight-score');
                        totalCell.classList.add('highlight-score');
                        setTimeout(() => {
                            correctnessCell.classList.remove('highlight-score');
                            totalCell.classList.remove('highlight-score');
                        }, 800);
                    }

                    judgeScores[juriNumber] = data;
                }
            });
        }

        // =========================================================
        // Update penalties display (same logic as dewanOperator)
        // =========================================================
        function updatePenalties(penalties, side) {
            const penaltyListEl = document.getElementById('penalty-list');
            const totalPenaltyEl = document.getElementById('total-penalty-score');

            if (!penaltyListEl || !totalPenaltyEl) return;

            // Filter active penalties for this side
            const sidePenalties = penalties.filter(p =>
                p.status === 'active' && (p.side === side || (!p.side && side === '1'))
            );

            const activeMap = {};
            sidePenalties.forEach(p => { activeMap[p.type] = true; });

            let totalPenalty = 0;
            let hasActive = false;
            penaltyListEl.innerHTML = '';

            if (penaltyTypes.length === 0) {
                penaltyListEl.innerHTML = '<p class="text-xs text-gray-400 italic">Rule tidak ditemukan</p>';
            } else {
                penaltyTypes.forEach(rule => {
                    if (activeMap[rule.id]) {
                        totalPenalty += rule.value;
                        hasActive = true;
                        const div = document.createElement('div');
                        div.className = 'flex justify-between items-center bg-red-50 border border-red-200 rounded px-2 py-1';
                        div.innerHTML = `
                            <span class="text-xs font-semibold text-gray-700">${rule.name}</span>
                            <span class="text-xs font-bold text-red-600">${Math.abs(rule.value).toFixed(2)}</span>
                        `;
                        penaltyListEl.appendChild(div);
                    }
                });
            }

            if (!hasActive) {
                penaltyListEl.innerHTML = '<p class="text-xs text-gray-400 italic">Tidak ada penalti aktif</p>';
            }

            totalPenaltyEl.textContent = Math.abs(totalPenalty).toFixed(2);
        }

        // =========================================================
        // Calculate statistics per side (same as dewanOperator)
        // =========================================================
        function calculateSideStatistics(judges, side) {
            const filteredJudges = Object.values(judges).filter(data => data.side === side);

            const scores = [];
            for (let i = 1; i <= NUM_JUDGES; i++) {
                const judgeData = filteredJudges.find(j => j.judge_id === i);
                scores.push(judgeData ? judgeData.total_score : 9.90);
            }

            scores.sort((a, b) => a - b);

            const median = scores.length % 2 === 0
                ? (scores[scores.length / 2 - 1] + scores[scores.length / 2]) / 2
                : scores[Math.floor(scores.length / 2)];

            const mean = scores.reduce((sum, s) => sum + s, 0) / scores.length;
            const variance = scores.reduce((sum, s) => sum + Math.pow(s - mean, 2), 0) / scores.length;
            const stdDev = Math.sqrt(variance);

            const medianEl = document.getElementById('median-score');
            const stdDevEl = document.getElementById('std-deviation-score');

            if (medianEl) medianEl.textContent = median.toFixed(2);
            if (stdDevEl) stdDevEl.textContent = stdDev.toFixed(6);
        }

        // =========================================================
        // Calculate final score per side (same as dewanOperator)
        // =========================================================
        function calculateFinalScore(judges, penalties, side) {
            const filteredJudges = Object.values(judges).filter(data => data.side === side);

            const scores = [];
            for (let i = 1; i <= NUM_JUDGES; i++) {
                const judgeData = filteredJudges.find(j => j.judge_id === i);
                scores.push(judgeData ? judgeData.total_score : 9.90);
            }

            scores.sort((a, b) => a - b);

            const median = scores.length % 2 === 0
                ? (scores[scores.length / 2 - 1] + scores[scores.length / 2]) / 2
                : scores[Math.floor(scores.length / 2)];

            const sidePenalties = penalties.filter(p =>
                p.status === 'active' && (p.side === side || (!p.side && side === '1'))
            );

            let totalPenalties = 0;
            sidePenalties.forEach(p => { totalPenalties += parseFloat(p.value); });

            const finalScore = median + totalPenalties;

            const finalEl = document.getElementById('final-score-with-penalty');
            const breakdownEl = document.getElementById('final-score-breakdown');

            if (finalEl) finalEl.textContent = finalScore.toFixed(2);
            if (breakdownEl) breakdownEl.textContent = `Score (${median.toFixed(2)}) - Penalty (${Math.abs(totalPenalties).toFixed(2)})`;
        }
        
        // =========================================================
        // Setup WebSocket (same as dewanOperator)
        // =========================================================
        function setupWebSocket() {
            fetchMatchInfo();
            fetchMatchData();
            
            if (window.Echo) {
                window.Echo.channel(`pertandingan.${MATCH_ID}`)
                    .listen('.tunggal.score.updated', (e) => {
                        console.log('Score updated:', e);
                        fetchMatchInfo();
                        fetchMatchData();
                    })
                    .listen('.penalty.updated', (e) => {
                        console.log('Penalty updated:', e);
                        fetchMatchInfo();
                        fetchMatchData();
                    });
                    
                console.log(`Subscribed to WebSocket: pertandingan.${MATCH_ID}`);
            } else {
                console.warn('Laravel Echo not available, using polling');
                setInterval(() => {
                    fetchMatchInfo();
                    fetchMatchData();
                }, 2000);
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Match ID:', MATCH_ID);
            console.log('Number of Judges:', NUM_JUDGES);
            console.log('Match Type:', MATCH_TYPE);
            console.log('Penalty Rules:', penaltyTypes);
            
            initScoreTable();
            setupWebSocket();
        });
    </script>
</body>
</html>

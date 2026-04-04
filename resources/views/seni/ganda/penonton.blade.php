<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoreboard Pencak Silat - Ganda</title>
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
        @keyframes flashYellow {
            0%, 100% { opacity: 1; transform: scale(1); }
            30%  { opacity: 0.5; transform: scale(1.08); }
            60%  { opacity: 1; transform: scale(1.04); }
        }
        .flash-animation {
            animation: flashYellow 1.2s ease-in-out;
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
                        <span class="bg-green-100 px-3 py-1 rounded-lg border border-green-200 text-green-700">GANDA</span>
                    </div>

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
                                <div id="median-score" class="text-3xl font-mono font-black text-gray-800">-</div>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Std Deviation</p>
                                <div id="std-deviation-score" class="text-lg font-mono font-bold text-gray-600">-</div>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-red-700 uppercase tracking-wider">Total Penalti</p>
                                <div id="total-penalty-score" class="text-2xl font-mono font-bold text-red-600">0.00</div>
                            </div>
                            <div class="bg-green-50 rounded-lg py-2 px-3 border border-green-200">
                                <p class="text-xs font-semibold text-green-700 uppercase tracking-wider">Skor Akhir</p>
                                <div id="final-score" class="text-4xl font-mono font-black text-green-600">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Score Table Section -->
            <div class="px-4 pb-2 flex-1">
                <div class="bg-gradient-to-r from-red-50 to-indigo-50 rounded-xl p-3 border border-red-100 shadow-lg h-full">
                    <!-- Sorted Scores -->
                    <div id="sortedSection" class="hidden mb-2">
                        <p class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-1 text-center">Sorted Scores (Median Highlighted)</p>
                        <div id="sortedScores" class="flex justify-center gap-2 flex-wrap"></div>
                    </div>
                    <!-- Judge table -->
                    <div class="overflow-hidden mt-1">
                        <table class="w-full">
                            <thead id="tableHeader">
                                <!-- Will be generated by JavaScript -->
                            </thead>
                            <tbody id="tableBody">
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

        // Side monitoring state (same as dewanOperator)
        let monitoringSide = '1'; // Default: Sudut Biru
        let lastEventData = null;
        let previousJudgeData = {};
        let previousPenalties = [];

        // Penalty categories (same as dewanOperator ganda)
        const penaltyCategories = [
            { type: 'WAKTU',                  label: 'WAKTU' },
            { type: 'KELUAR_GARIS',           label: 'KELUAR GARIS' },
            { type: 'SENJATA_JATUH',          label: 'SENJATA JATUH TIDAK SESUAI' },
            { type: 'SENJATA_TIDAK_JATUH',    label: 'SENJATA TIDAK JATUH SESUAI' },
            { type: 'TIDAK_ADA_SALAM_SUARA',  label: 'TIDAK ADA SALAM / SUARA' },
            { type: 'BAJU_SENJATA_PATAH',     label: 'BAJU / SENJATA PATAH' }
        ];

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

            const btnSide1 = document.getElementById('btnMonitorSide1');
            const btnSide2 = document.getElementById('btnMonitorSide2');
            const indicator = document.getElementById('monitoringSideIndicator');
            const statsContainer = document.getElementById('stats-container');

            if (side === '1') {
                btnSide1.className = 'px-5 py-2 rounded-lg font-bold text-white transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 bg-blue-600 border-2 border-blue-700 text-sm';
                btnSide2.className = 'px-5 py-2 rounded-lg font-bold text-gray-600 bg-gray-200 border-2 border-gray-300 transition-all duration-200 hover:bg-gray-300 text-sm';
                indicator.className = 'px-3 py-1 rounded-md bg-blue-100 text-blue-800 font-semibold text-xs';
                indicator.textContent = 'Monitor: Sudut Biru';
                if (statsContainer) {
                    statsContainer.className = 'bg-gradient-to-br from-slate-50 to-blue-50 rounded-xl p-4 border border-blue-200 shadow-lg h-full flex flex-col justify-center';
                }
            } else {
                btnSide1.className = 'px-5 py-2 rounded-lg font-bold text-gray-600 bg-gray-200 border-2 border-gray-300 transition-all duration-200 hover:bg-gray-300 text-sm';
                btnSide2.className = 'px-5 py-2 rounded-lg font-bold text-white transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 bg-red-600 border-2 border-red-700 text-sm';
                indicator.className = 'px-3 py-1 rounded-md bg-red-100 text-red-800 font-semibold text-xs';
                indicator.textContent = 'Monitor: Sudut Merah';
                if (statsContainer) {
                    statsContainer.className = 'bg-gradient-to-br from-slate-50 to-red-50 rounded-xl p-4 border border-red-200 shadow-lg h-full flex flex-col justify-center';
                }
            }

            // Re-render with new side
            if (lastEventData) {
                renderMatchInfo(lastEventData.matchInfo);
                renderDashboard(lastEventData);
            }
        }

        // =========================================================
        // Initialize default judges (empty data) — same as dewanOperator
        // =========================================================
        function initializeDefaultJudges() {
            const defaultJudges = [];
            for (let i = 1; i <= NUM_JUDGES; i++) {
                defaultJudges.push({
                    judge_id: i,
                    judge_name: `Juri ${i}`,
                    scores: { teknik: 0, kekuatan: 0, penampilan: 0 },
                    total: 9.10,
                    last_update: null
                });
            }
            return defaultJudges;
        }

        // =========================================================
        // Build judgeArray from API response — same as dewanOperator
        // =========================================================
        function buildJudgeArray(judges) {
            const defaultJudges = initializeDefaultJudges();
            return defaultJudges.map(defaultJudge => {
                const key = `${defaultJudge.judge_id}_${monitoringSide}`;
                return judges[key] || defaultJudge;
            });
        }

        // =========================================================
        // Change detection for flash animation
        // =========================================================
        function checkForChanges(newData) {
            const currentJudges = newData.judges || {};
            const currentPenalties = newData.penalties || [];

            Object.keys(currentJudges).forEach(judgeId => {
                const currentJudge = currentJudges[judgeId];
                const previousJudge = previousJudgeData[judgeId];

                if (previousJudge) {
                    if (JSON.stringify(currentJudge.scores) !== JSON.stringify(previousJudge.scores) ||
                        currentJudge.total !== previousJudge.total) {
                        flashJudgeCell(judgeId);
                    }
                } else {
                    flashJudgeCell(judgeId);
                }
            });

            if (currentPenalties.length !== previousPenalties.length) {
                const penaltyList = document.getElementById('penalty-list');
                if (penaltyList) {
                    penaltyList.classList.add('flash-animation');
                    setTimeout(() => penaltyList.classList.remove('flash-animation'), 1200);
                }
            }

            previousJudgeData = JSON.parse(JSON.stringify(currentJudges));
            previousPenalties = JSON.parse(JSON.stringify(currentPenalties));
        }

        function flashJudgeCell(judgeId) {
            setTimeout(() => {
                const cells = document.querySelectorAll(`[data-judge-id="${judgeId}"]`);
                cells.forEach(cell => {
                    cell.classList.add('flash-animation');
                    setTimeout(() => cell.classList.remove('flash-animation'), 1200);
                });
            }, 100);
        }

        // =========================================================
        // Fetch match info (contingent, players, arena, status)
        // =========================================================
        async function fetchMatchInfo() {
            try {
                const response = await fetch(`/api/superadmin/matches/${MATCH_ID}`);
                const result = await response.json();
                if (result.success && result.data) {
                    if (lastEventData) lastEventData.matchInfo = result.data;
                    renderMatchInfo(result.data);
                }
            } catch (error) {
                console.error('Error fetching match info:', error);
            }
        }

        // Render match info — filtered by monitoringSide
        function renderMatchInfo(match) {
            if (!match) return;
            const players = match.players || [];
            const sideNumber = parseInt(monitoringSide);
            const sidePlayers = players.filter(p => p.side_number === sideNumber);

            const arenaName = match.arena ? match.arena.arena_name : 'Arena';
            document.getElementById('arenaInfo').textContent = arenaName;

            const statusMap = { 'belum_dimulai': 'READY', 'berlangsung': 'LIVE', 'selesai': 'FINAL' };
            const status = statusMap[match.status] || match.status.toUpperCase();
            document.getElementById('matchStatus').textContent = status;

            if (sidePlayers.length > 0) {
                const firstPlayer = sidePlayers[0];
                document.getElementById('contingentName').textContent = firstPlayer.player_contingent.toUpperCase();

                const colorClass = monitoringSide === '1' ? 'from-blue-500 to-blue-600' : 'from-red-500 to-red-600';
                const avatarsContainer = document.getElementById('playerAvatars');
                avatarsContainer.innerHTML = sidePlayers.map(player => {
                    const initials = player.player_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                    return `<div class="w-12 h-12 bg-gradient-to-br ${colorClass} rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg border-2 border-white">${initials}</div>`;
                }).join('');

                const namesContainer = document.getElementById('playerNames');
                namesContainer.innerHTML = sidePlayers.map(player => `
                    <p class="text-sm font-semibold text-gray-700 leading-relaxed">| ${player.player_name.toUpperCase()}</p>
                `).join('');
            } else {
                document.getElementById('contingentName').textContent = 'TIDAK ADA DATA';
                document.getElementById('playerNames').innerHTML = '<p class="text-xs text-gray-400 italic">Tidak ada peserta pada sudut ini</p>';
            }
        }

        // =========================================================
        // Fetch events from API (same endpoint as dewanOperator)
        // =========================================================
        async function fetchEvents() {
            try {
                const response = await fetch(`/api/seni/ganda/events/${MATCH_ID}`);
                const result = await response.json();

                if (result.status === 'success') {
                    checkForChanges(result.data);
                    lastEventData = result.data;
                    renderDashboard(result.data);
                }
            } catch (error) {
                console.error('Error fetching events:', error);
            }
        }

        // =========================================================
        // Render dashboard — same logic as dewanOperator
        // =========================================================
        function renderDashboard(data) {
            const judges = data.judges || {};
            const penalties = data.penalties || [];

            // Build judgeArray filtered by monitoringSide
            const judgeArray = buildJudgeArray(judges);

            // Calculate side-specific penalties
            const activePenalties = penalties.filter(p => p.status === 'active' && (p.side == monitoringSide || !p.side));
            const sideTotalPenalties = activePenalties.reduce((sum, p) => sum + parseFloat(p.value), 0);

            // Render penalties in left panel
            renderPenaltiesPanel(penalties);

            // Render judge table
            renderTable(judgeArray);

            // Render sorted scores
            renderSortedScores(judgeArray);

            // Calculate & render statistics
            calculateStatistics(judgeArray, sideTotalPenalties);
        }

        // =========================================================
        // Render penalties in the left panel (compact)
        // =========================================================
        function renderPenaltiesPanel(penalties) {
            const penaltyListEl = document.getElementById('penalty-list');
            const totalPenaltyEl = document.getElementById('total-penalty-score');
            if (!penaltyListEl || !totalPenaltyEl) return;

            const activePenalties = penalties.filter(p =>
                p.status === 'active' && (p.side == monitoringSide || !p.side)
            );

            // Count by type
            const penaltyCounts = {};
            activePenalties.forEach(p => {
                if (!penaltyCounts[p.type]) penaltyCounts[p.type] = { count: 0, value: p.value };
                penaltyCounts[p.type].count++;
            });

            let totalPenalty = 0;
            let hasActive = false;
            penaltyListEl.innerHTML = '';

            penaltyCategories.forEach(category => {
                const data = penaltyCounts[category.type];
                if (data && data.count > 0) {
                    hasActive = true;
                    const total = data.count * parseFloat(data.value);
                    totalPenalty += total;
                    const div = document.createElement('div');
                    div.className = 'flex justify-between items-center bg-red-50 border border-red-200 rounded px-2 py-1';
                    div.innerHTML = `
                        <span class="text-xs font-semibold text-gray-700">${category.label} ×${data.count}</span>
                        <span class="text-xs font-bold text-red-600">${Math.abs(total).toFixed(2)}</span>
                    `;
                    penaltyListEl.appendChild(div);
                }
            });

            if (!hasActive) {
                penaltyListEl.innerHTML = '<p class="text-xs text-gray-400 italic">Tidak ada penalti aktif</p>';
            }

            totalPenaltyEl.textContent = Math.abs(totalPenalty).toFixed(2);
        }

        // =========================================================
        // Render judge table — same structure as dewanOperator
        // =========================================================
        function renderTable(judgeArray) {
            const tableHeader = document.getElementById('tableHeader');
            const tableBody = document.getElementById('tableBody');

            // Generate header
            let headerHTML = '<tr class="bg-gradient-to-br from-red-600 to-red-700"><th class="px-3 py-2 border border-red-800 text-white font-bold text-xs text-left">Kriteria</th>';
            judgeArray.forEach((judge, idx) => {
                headerHTML += `<th class="px-2 py-2 border border-red-800 text-white font-bold text-base" data-judge-id="${judge.judge_id}">${idx + 1}</th>`;
            });
            headerHTML += '</tr>';
            tableHeader.innerHTML = headerHTML;

            // Row categories
            const categories = [
                { key: 'teknik',      label: 'Teknik Dasar' },
                { key: 'kekuatan',    label: 'Kekuatan & Kecepatan' },
                { key: 'penampilan',  label: 'Penampilan & Gaya' }
            ];

            let bodyHTML = '';
            categories.forEach(category => {
                bodyHTML += `<tr><td class="bg-gray-50 border border-red-200 px-2 py-1 font-semibold text-gray-800 text-xs">${category.label}</td>`;
                judgeArray.forEach(judge => {
                    const score = judge.scores[category.key] || 0;
                    const hasData = judge.last_update !== null;
                    const bgClass = hasData
                        ? 'bg-gradient-to-br from-red-500 to-red-600 text-white'
                        : 'bg-gray-100 text-gray-500';
                    bodyHTML += `<td class="border border-red-200 text-lg font-bold text-center py-1 px-1 ${bgClass}" data-judge-id="${judge.judge_id}">${score.toFixed(2)}</td>`;
                });
                bodyHTML += '</tr>';
            });

            // Total row
            bodyHTML += '<tr class="bg-blue-50"><td class="bg-blue-100 border border-red-200 px-2 py-2 font-bold text-gray-800 text-xs">Total Score</td>';
            judgeArray.forEach(judge => {
                const hasData = judge.last_update !== null;
                const bgClass = hasData
                    ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white'
                    : 'bg-gray-100 text-gray-400';
                bodyHTML += `<td class="border border-red-200 text-xl font-bold font-mono text-center py-2 px-1 ${bgClass}" data-judge-id="${judge.judge_id}">${judge.total.toFixed(2)}</td>`;
            });
            bodyHTML += '</tr>';

            tableBody.innerHTML = bodyHTML;
        }

        // =========================================================
        // Render sorted scores (same as dewanOperator)
        // =========================================================
        function renderSortedScores(judgeArray) {
            const sortedSection = document.getElementById('sortedSection');
            const sortedScoresDiv = document.getElementById('sortedScores');

            const scores = judgeArray.map(j => j.total).sort((a, b) => a - b);
            const median = calculateMedian(scores);

            sortedSection.classList.remove('hidden');

            sortedScoresDiv.innerHTML = scores.map((score) => {
                const isMedian = (scores.length % 2 === 0)
                    ? (score === scores[Math.floor(scores.length / 2) - 1] || score === scores[Math.floor(scores.length / 2)])
                    : (score === scores[Math.floor(scores.length / 2)]);

                const judgeWithThisScore = judgeArray.find(j => j.total === score);
                const hasSubmitted = judgeWithThisScore && judgeWithThisScore.last_update !== null;
                const opacity = hasSubmitted ? '' : 'opacity-60';

                const bgColor = isMedian
                    ? 'bg-yellow-200 border-yellow-400 font-bold'
                    : 'bg-white border-gray-300';

                return `<span class="px-3 py-1 ${bgColor} ${opacity} border-2 rounded-lg font-mono text-base shadow-sm">${score.toFixed(2)}</span>`;
            }).join('');
        }

        // =========================================================
        // Math helpers (same as dewanOperator)
        // =========================================================
        function calculateMedian(sortedArray) {
            const len = sortedArray.length;
            if (len === 0) return 0;
            if (len % 2 === 0) {
                return (sortedArray[len / 2 - 1] + sortedArray[len / 2]) / 2;
            } else {
                return sortedArray[Math.floor(len / 2)];
            }
        }

        function calculateStdDev(values, mean) {
            if (values.length === 0) return 0;
            const squaredDiffs = values.map(val => Math.pow(val - mean, 2));
            const variance = squaredDiffs.reduce((a, b) => a + b, 0) / values.length;
            return Math.sqrt(variance);
        }

        // =========================================================
        // Calculate & display statistics (same as dewanOperator)
        // =========================================================
        function calculateStatistics(judgeArray, totalPenalties) {
            const scores = judgeArray.map(j => j.total).sort((a, b) => a - b);
            const median = calculateMedian(scores);
            const mean = scores.reduce((a, b) => a + b, 0) / scores.length;
            const stdDev = calculateStdDev(scores, mean);
            const finalScore = median + totalPenalties; // penalties are negative

            const medianEl = document.getElementById('median-score');
            const stdDevEl = document.getElementById('std-deviation-score');
            const finalEl = document.getElementById('final-score');

            if (medianEl) medianEl.textContent = median.toFixed(3);
            if (stdDevEl) stdDevEl.textContent = stdDev.toFixed(6);
            if (finalEl) finalEl.textContent = finalScore.toFixed(3);
        }

        // =========================================================
        // Setup WebSocket (same as dewanOperator)
        // =========================================================
        function setupWebSocket() {
            fetchMatchInfo();
            fetchEvents();

            if (window.Echo) {
                window.Echo.channel(`pertandingan.${MATCH_ID}`)
                    .listen('.judge.score.updated', (e) => {
                        console.log('Judge score updated:', e);
                        fetchMatchInfo();
                        fetchEvents();
                    })
                    .listen('.penalty.updated', (e) => {
                        console.log('Penalty updated:', e);
                        fetchMatchInfo();
                        fetchEvents();
                    });

                console.log(`Subscribed to WebSocket channel: pertandingan.${MATCH_ID}`);
            } else {
                console.warn('Laravel Echo not available, falling back to polling');
                setInterval(() => {
                    fetchMatchInfo();
                    fetchEvents();
                }, 2000);
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Match ID:', MATCH_ID);
            console.log('Number of Judges:', NUM_JUDGES);

            // Initial render with default judges
            renderDashboard({ judges: {}, penalties: [], total_penalties: 0 });

            // Setup WebSocket or polling
            setupWebSocket();
        });
    </script>
</body>
</html>

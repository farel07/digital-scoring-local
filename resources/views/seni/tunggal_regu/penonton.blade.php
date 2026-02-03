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
                    </div>
                </div>
                
                <!-- Stats Section -->
                <div class="flex-1">
                    <div class="bg-gradient-to-br from-slate-50 to-gray-50 rounded-xl p-4 border border-gray-200 shadow-lg h-full flex flex-col justify-center">
                        <div class="text-center space-y-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Median</p>
                                <div id="median-score" class="text-3xl font-mono font-black text-gray-800">0.00</div>
                            </div>
                            <div class="bg-green-50 rounded-lg py-2 px-3 border border-green-200">
                                <p class="text-xs font-semibold text-green-700 uppercase tracking-wider">Final Score</p>
                                <div id="final-score-with-penalty" class="text-4xl font-mono font-black text-green-600">0.00</div>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-red-700 uppercase tracking-wider">Total Penalty</p>
                                <div id="total-penalty-score" class="text-2xl font-mono font-bold text-red-600">0.00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Score Section -->
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
        
        // Update timestamp
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
        
        // Initialize score table
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
                bodyHTML += `<td class="bg-gradient-to-br from-red-500 to-red-600 text-white font-bold text-lg py-2 px-2 border border-red-700 transition-all duration-300 panel-${i}-correctness">9.90</td>`;
            }
            bodyHTML += '</tr>';
            
            // Flow / Stamina row
            bodyHTML += '<tr><td class="bg-gray-50 border border-red-700 px-2 py-2 font-semibold text-gray-800 text-xs">Flow/Stamina</td>';
            for (let i = 1; i <= NUM_JUDGES; i++) {
                bodyHTML += `<td class="bg-gradient-to-br from-red-500 to-red-600 text-white font-bold text-lg py-2 px-2 border border-red-700 transition-all duration-300 panel-${i}-category">0.00</td>`;
            }
            bodyHTML += '</tr>';
            
            // Total Score row
            bodyHTML += '<tr><td class="bg-blue-100 border border-red-700 px-2 py-2 font-bold text-gray-800 text-xs first:rounded-bl-lg">Total</td>';
            for (let i = 1; i <= NUM_JUDGES; i++) {
                const roundedClass = (i === NUM_JUDGES) ? 'last:rounded-br-lg' : '';
                bodyHTML += `<td class="bg-gradient-to-br from-blue-500 to-blue-600 text-white font-bold text-xl py-2 px-2 border border-red-700 transition-all duration-300 ${roundedClass} panel-${i}-total">9.90</td>`;
            }
            bodyHTML += '</tr>';
            
            bodyContainer.innerHTML = bodyHTML;
        }
        
        // Fetch match data
        async function fetchMatchData() {
            try {
                const response = await fetch(`/api/superadmin/matches/${MATCH_ID}`);
                const result = await response.json();
                
                if (result.success && result.data) {
                    renderMatchInfo(result.data);
                }
            } catch (error) {
                console.error('Error fetching match data:', error);
            }
        }
        
        // Render match info
        function renderMatchInfo(match) {
            const players = match.players || [];
            const side1Players = players.filter(p => p.side_number === 1);
            
            if (side1Players.length > 0) {
                const firstPlayer = side1Players[0];
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
                avatarsContainer.innerHTML = side1Players.map(player => {
                    const initials = player.player_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                    return `
                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg border-2 border-white">
                            ${initials}
                        </div>
                    `;
                }).join('');
                
                // Render player names
                const namesContainer = document.getElementById('playerNames');
                namesContainer.innerHTML = side1Players.map(player => `
                    <p class="text-sm font-semibold text-gray-700 leading-relaxed">
                        | ${player.player_name.toUpperCase()}
                    </p>
                `).join('');
            }
        }
        
        // Fetch events from API
        async function fetchEvents() {
            try {
                const response = await fetch(`/api/seni/tunggal-regu/events/${MATCH_ID}`);
                const result = await response.json();
                
                if (result.status === 'success') {
                    updateJudgeScores(result.data.judges || {});
                    updateStatistics(result.data.statistics || {});
                }
            } catch (error) {
                console.error('Error fetching events:', error);
            }
        }
        
        // Update judge scores display
        function updateJudgeScores(judges) {
            Object.entries(judges).forEach(([judgeId, data]) => {
                const juriNumber = data.judge_id;
                
                const correctnessCell = document.querySelector(`.panel-${juriNumber}-correctness`);
                const categoryCell = document.querySelector(`.panel-${juriNumber}-category`);
                const totalCell = document.querySelector(`.panel-${juriNumber}-total`);
                
                if (correctnessCell && categoryCell && totalCell) {
                    correctnessCell.textContent = data.correctness_score.toFixed(2);
                    categoryCell.textContent = data.category_score.toFixed(2);
                    totalCell.textContent = data.total_score.toFixed(2);
                    
                    // Highlight if updated
                    if (data.last_update) {
                        correctnessCell.classList.add('from-green-400', 'to-green-500');
                        correctnessCell.classList.remove('from-red-500', 'to-red-600');
                        categoryCell.classList.add('from-green-400', 'to-green-500');
                        categoryCell.classList.remove('from-red-500', 'to-red-600');
                    } else {
                        correctnessCell.classList.add('from-red-500', 'to-red-600');
                        correctnessCell.classList.remove('from-green-400', 'to-green-500');
                        categoryCell.classList.add('from-red-500', 'to-red-600');
                        categoryCell.classList.remove('from-green-400', 'to-green-500');
                    }
                }
            });
        }
        
        // Update statistics display
        function updateStatistics(stats) {
            if (!stats.median) return;
            
            document.getElementById('median-score').textContent = stats.median.toFixed(2);
            document.getElementById('total-penalty-score').textContent = Math.abs(stats.total_penalties).toFixed(2);
            document.getElementById('final-score-with-penalty').textContent = stats.final_score.toFixed(2);
        }
        
        // Setup WebSocket
        function setupWebSocket() {
            fetchEvents();
            
            if (window.Echo) {
                window.Echo.channel(`pertandingan.${MATCH_ID}`)
                    .listen('.tunggal.score.updated', (e) => {
                        console.log('Score updated:', e);
                        fetchEvents();
                    })
                    .listen('.penalty.updated', (e) => {
                        console.log('Penalty updated:', e);
                        fetchEvents();
                    });
                    
                console.log(`Subscribed to WebSocket: pertandingan.${MATCH_ID}`);
            } else {
                console.warn('Laravel Echo not available, using polling');
                setInterval(fetchEvents, 2000);
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Match ID:', MATCH_ID);
            console.log('Number of Judges:', NUM_JUDGES);
            
            initScoreTable();
            fetchMatchData();
            setupWebSocket();
        });
    </script>
</body>
</html>

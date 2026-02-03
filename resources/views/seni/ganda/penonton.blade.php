<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoreboard Pencak Silat - Compact</title>
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
                        <span class="bg-green-100 px-3 py-1 rounded-lg border border-green-200 text-green-700">GANDA</span>
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
                
                <!-- Timer Section -->
                <div class="flex-1">
                    <div class="bg-gradient-to-br from-slate-50 to-gray-50 rounded-xl p-4 border border-gray-200 shadow-lg h-full flex flex-col justify-center">
                        <div class="text-center">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Timer
                            </p>
                            <div id="timer" class="text-4xl font-mono font-black text-gray-800 bg-white rounded-xl py-4 px-4 shadow-inner border-2 border-red-200 cursor-pointer hover:border-red-400 transition-all duration-300">
                                00:00
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Click: start/stop • Double: reset</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Score Section -->
            <div class="px-4 pb-4 flex-1">
                <div class="bg-gradient-to-r from-red-50 to-indigo-50 rounded-xl p-3 border border-red-100 shadow-lg h-full">
                    <div class="overflow-hidden">
                        <table class="w-full h-full">
                            <!-- Header Row -->
                            <thead id="scoreTableHeader">
                                <!-- Will be generated by JavaScript -->
                            </thead>
                            <!-- Score Row -->
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
        const NUM_JUDGES = {{ $jumlahJuri ?? 10 }}; // Dynamic from database
        
        // Timer functionality
        let seconds = 0;
        let timerInterval;
        let isRunning = false;
        
        const timerElement = document.getElementById('timer');
        
        function updateTimer() {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            const display = `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
            timerElement.textContent = display;
        }
        
        function startTimer() {
            if (!timerInterval) {
                isRunning = true;
                timerElement.classList.add('border-green-400', 'bg-green-50');
                timerElement.classList.remove('border-red-200');
                timerInterval = setInterval(() => {
                    seconds++;
                    updateTimer();
                }, 1000);
            }
        }
        
        function stopTimer() {
            if (timerInterval) {
                isRunning = false;
                timerElement.classList.remove('border-green-400', 'bg-green-50');
                timerElement.classList.add('border-red-400', 'bg-red-50');
                clearInterval(timerInterval);
                timerInterval = null;
                
                setTimeout(() => {
                    timerElement.classList.remove('border-red-400', 'bg-red-50');
                    timerElement.classList.add('border-red-200');
                }, 1500);
            }
        }
        
        function resetTimer() {
            stopTimer();
            seconds = 0;
            updateTimer();
        }
        
        // Timer event listeners
        timerElement.addEventListener('click', function() {
            if (isRunning) {
                stopTimer();
            } else {
                startTimer();
            }
        });
        
        timerElement.addEventListener('dblclick', function() {
            resetTimer();
        });
        
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
        
        // ========== DYNAMIC TABLE INITIALIZATION ==========
        
        // Initialize score table based on number of judges
        function initScoreTable() {
            const headerContainer = document.getElementById('scoreTableHeader');
            const bodyContainer = document.getElementById('scoreTableBody');
            
            // Generate header row
            let headerHTML = '<tr>';
            for (let i = 1; i <= NUM_JUDGES; i++) {
                const roundedClass = (i === 1) ? 'first:rounded-tl-lg' : (i === NUM_JUDGES) ? 'last:rounded-tr-lg' : '';
                headerHTML += `<th class="bg-gradient-to-br from-red-600 to-red-700 text-white font-bold text-lg py-2 px-2 border border-red-800 ${roundedClass}">${i}</th>`;
            }
            headerHTML += '</tr>';
            headerContainer.innerHTML = headerHTML;
            
            // Generate body row
            let bodyHTML = '<tr id="scoreRow">';
            for (let i = 1; i <= NUM_JUDGES; i++) {
                const roundedClass = (i === 1) ? 'first:rounded-bl-lg' : (i === NUM_JUDGES) ? 'last:rounded-br-lg' : '';
                bodyHTML += `<td class="bg-gradient-to-br from-red-500 to-red-600 text-white font-bold text-xl py-3 px-2 border border-red-700 transition-all duration-300 ${roundedClass}">9.10</td>`;
            }
            bodyHTML += '</tr>';
            bodyContainer.innerHTML = bodyHTML;
        }
        
        // ========== REALTIME SCORE DISPLAY ==========
        
        // Fetch match data (players, arena, etc)
        async function fetchMatchData() {
            try {
                const response = await fetch(`/api/superadmin/matches/${MATCH_ID}`);
                const result = await response.json();
                
                if (result.success && result.data) {
                    renderMatchInfo(result.data);
                } else {
                    console.error('Failed to fetch match data');
                }
            } catch (error) {
                console.error('Error fetching match data:', error);
            }
        }
        
        // Render match info (players, arena, etc)
        function renderMatchInfo(match) {
            // Get all players
            const players = match.players || [];
            
            // Get players for side 1 (default display)
            const side1Players = players.filter(p => p.side_number === 1);
            
            if (side1Players.length > 0) {
                const firstPlayer = side1Players[0];
                const contingent = firstPlayer.player_contingent;
                
                // Update contingent name
                document.getElementById('contingentName').textContent = contingent.toUpperCase();
                
                // Update arena info
                const arenaName = match.arena ? match.arena.arena_name : 'Arena';
                document.getElementById('arenaInfo').textContent = arenaName;
                
                // Update match status
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
                const response = await fetch(`/api/seni/ganda/events/${MATCH_ID}`);
                const result = await response.json();
                
                if (result.status === 'success') {
                    renderScores(result.data);
                } else {
                    console.error('Failed to fetch events:', result.message);
                }
            } catch (error) {
                console.error('Error fetching events:', error);
            }
        }
        
        // Render judge scores
        function renderScores(data) {
            const judges = data.judges || {};
            const scoreRow = document.getElementById('scoreRow');
            
            if (!scoreRow) return; // Table not initialized yet
            
            const cells = scoreRow.querySelectorAll('td');
            
            // Update each judge score cell
            for (let i = 1; i <= NUM_JUDGES; i++) {
                const judgeData = judges[i];
                const score = judgeData ? judgeData.total : 9.10;
                const cellIndex = i - 1;
                
                if (cells[cellIndex]) {
                    cells[cellIndex].textContent = score.toFixed(2);
                    
                    // Add highlight if score changed (not default)
                    if (judgeData && judgeData.last_update) {
                        cells[cellIndex].classList.add('bg-gradient-to-br', 'from-green-400', 'to-green-500');
                        cells[cellIndex].classList.remove('from-red-500', 'to-red-600');
                    } else {
                        cells[cellIndex].classList.add('bg-gradient-to-br', 'from-red-500', 'to-red-600');
                        cells[cellIndex].classList.remove('from-green-400', 'to-green-500');
                    }
                }
            }
        }
        
        // Setup WebSocket connection for realtime updates
        function setupWebSocket() {
            // Initial fetch to load existing data
            fetchEvents();
            
            // Subscribe to pertandingan channel
            if (window.Echo) {
                window.Echo.channel(`pertandingan.${MATCH_ID}`)
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
                    
                console.log(`Subscribed to WebSocket channel: pertandingan.${MATCH_ID}`);
            } else {
                console.warn('Laravel Echo not available, falling back to polling');
                // Fallback to polling if Echo not available
                setInterval(fetchEvents, 2000);
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Match ID:', MATCH_ID);
            console.log('Number of Judges:', NUM_JUDGES);
            
            // Initialize score table first
            initScoreTable();
            
            // Fetch match data (players, arena, etc)
            fetchMatchData();
            
            // Setup WebSocket or polling for scores
            setupWebSocket();
        });
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'97415a96a7346cfd',t:'MTc1NjAyMjgyMy4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>

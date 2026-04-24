<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pertandingan Seni - {{ $matchType === 'regu' ? 'Regu' : 'Tunggal' }}</title>
    @vite(['resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f0f1a;
            min-height: 100vh;
            overflow: hidden;
        }

        /* ── Animated BG ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 40% at 20% 50%, rgba(37,99,235,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 50%, rgba(220,38,38,0.12) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        .scoreboard {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-rows: auto 1fr auto;
            height: 100vh;
            padding: 12px;
            gap: 10px;
        }

        /* ── Header ── */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 12px 24px;
            backdrop-filter: blur(12px);
        }

        .header-logo {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #dc2626, #9b1c1c);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 14px; color: white;
            box-shadow: 0 0 20px rgba(220,38,38,0.4);
        }

        .header-center { text-align: center; }
        .header-title {
            font-size: 20px; font-weight: 900;
            color: white; letter-spacing: 3px; text-transform: uppercase;
        }
        .header-sub {
            font-size: 11px; color: rgba(255,255,255,0.5);
            letter-spacing: 2px; margin-top: 2px;
        }

        .header-badge {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 11px; font-weight: 700;
            color: rgba(255,255,255,0.7);
            letter-spacing: 1px;
        }

        /* ── Main dual panel ── */
        .panels {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 12px;
        }

        /* ── Team Panel ── */
        .team-panel {
            border-radius: 18px;
            border: 2px solid transparent;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            transition: all 0.6s ease;
            position: relative;
            overflow: hidden;
        }

        .team-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 18px;
            opacity: 0;
            transition: opacity 0.6s ease;
            z-index: 0;
        }

        .team-panel > * { position: relative; z-index: 1; }

        /* Blue panel */
        .panel-blue {
            background: linear-gradient(145deg, rgba(23,37,84,0.9), rgba(29,78,216,0.2));
            border-color: rgba(59,130,246,0.3);
        }
        .panel-blue.winner {
            border-color: rgba(59,130,246,0.9);
            box-shadow: 0 0 60px rgba(59,130,246,0.35), inset 0 0 40px rgba(59,130,246,0.05);
            animation: pulseBlue 2.5s ease-in-out infinite;
        }
        .panel-blue.winner::before {
            background: radial-gradient(ellipse at top, rgba(59,130,246,0.15), transparent 70%);
            opacity: 1;
        }

        /* Red panel */
        .panel-red {
            background: linear-gradient(145deg, rgba(69,10,10,0.9), rgba(185,28,28,0.2));
            border-color: rgba(239,68,68,0.3);
        }
        .panel-red.winner {
            border-color: rgba(239,68,68,0.9);
            box-shadow: 0 0 60px rgba(239,68,68,0.35), inset 0 0 40px rgba(239,68,68,0.05);
            animation: pulseRed 2.5s ease-in-out infinite;
        }
        .panel-red.winner::before {
            background: radial-gradient(ellipse at top, rgba(239,68,68,0.15), transparent 70%);
            opacity: 1;
        }

        @keyframes pulseBlue {
            0%, 100% { box-shadow: 0 0 60px rgba(59,130,246,0.35), inset 0 0 40px rgba(59,130,246,0.05); }
            50%       { box-shadow: 0 0 90px rgba(59,130,246,0.55), inset 0 0 60px rgba(59,130,246,0.10); }
        }
        @keyframes pulseRed {
            0%, 100% { box-shadow: 0 0 60px rgba(239,68,68,0.35), inset 0 0 40px rgba(239,68,68,0.05); }
            50%       { box-shadow: 0 0 90px rgba(239,68,68,0.55), inset 0 0 60px rgba(239,68,68,0.10); }
        }

        /* ── Winner Badge ── */
        .winner-badge {
            display: none;
            align-items: center;
            gap: 8px;
            background: rgba(255,200,0,0.15);
            border: 1.5px solid rgba(255,200,0,0.5);
            border-radius: 40px;
            padding: 6px 18px;
            font-size: 12px; font-weight: 800;
            color: #fbbf24;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: badgeFade 0.5s ease-out forwards;
        }
        .winner .winner-badge { display: flex; }
        @keyframes badgeFade {
            from { opacity: 0; transform: scale(0.8) translateY(-6px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* ── Team Header ── */
        .team-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .team-avatar {
            width: 52px; height: 52px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 18px; color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        }
        .avatar-blue { background: linear-gradient(135deg, #1d4ed8, #3b82f6); }
        .avatar-red  { background: linear-gradient(135deg, #b91c1c, #ef4444); }

        .team-info { flex: 1; min-width: 0; }
        .contingent-name {
            font-size: 15px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 1px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .blue-text { color: #93c5fd; }
        .red-text  { color: #fca5a5; }
        .player-names {
            font-size: 11px; color: rgba(255,255,255,0.55);
            margin-top: 3px; line-height: 1.5;
        }

        .side-label {
            font-size: 10px; font-weight: 700;
            letter-spacing: 2px; text-transform: uppercase;
            padding: 4px 10px; border-radius: 20px;
        }
        .side-label-blue { background: rgba(59,130,246,0.2); color: #93c5fd; border: 1px solid rgba(59,130,246,0.3); }
        .side-label-red  { background: rgba(239,68,68,0.2);  color: #fca5a5; border: 1px solid rgba(239,68,68,0.3); }

        /* ── Final Score (big) ── */
        .final-score-box {
            border-radius: 14px;
            padding: 16px;
            text-align: center;
        }
        .final-score-box-blue { background: rgba(59,130,246,0.12); border: 1px solid rgba(59,130,246,0.25); }
        .final-score-box-red  { background: rgba(239,68,68,0.12);  border: 1px solid rgba(239,68,68,0.25);  }

        .final-label {
            font-size: 9px; font-weight: 700;
            letter-spacing: 3px; text-transform: uppercase;
            color: rgba(255,255,255,0.5); margin-bottom: 6px;
        }
        .final-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 40px; font-weight: 700;
            line-height: 1;
        }
        .final-value-blue { color: #60a5fa; }
        .final-value-red  { color: #f87171; }
        .winner .final-value-blue { color: #fbbf24; text-shadow: 0 0 30px rgba(251,191,36,0.5); }
        .winner .final-value-red  { color: #fbbf24; text-shadow: 0 0 30px rgba(251,191,36,0.5); }

        .final-breakdown {
            font-size: 10px; color: rgba(255,255,255,0.4);
            margin-top: 4px; font-family: 'JetBrains Mono', monospace;
        }

        /* ── Stats Grid ── */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
        }
        .stat-box {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            padding: 10px;
            text-align: center;
        }
        .stat-label {
            font-size: 9px; font-weight: 600;
            letter-spacing: 1.5px; text-transform: uppercase;
            color: rgba(255,255,255,0.4); margin-bottom: 4px;
        }
        .stat-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 16px; font-weight: 700; color: white;
        }
        .stat-value.penalty { color: #f87171; }

        /* ── Judge Table ── */
        .judge-table-wrap { overflow: hidden; border-radius: 10px; }
        .judge-table {
            width: 100%; border-collapse: collapse;
            font-size: 11px;
        }
        .judge-table thead th {
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.5);
            font-weight: 600; letter-spacing: 1px;
            padding: 7px 6px; text-align: center;
            text-transform: uppercase; font-size: 9px;
        }
        .judge-table thead th:first-child { text-align: left; padding-left: 10px; }
        .judge-table tbody tr { border-top: 1px solid rgba(255,255,255,0.04); }
        .judge-table tbody td {
            padding: 6px; text-align: center;
            color: rgba(255,255,255,0.8);
            font-family: 'JetBrains Mono', monospace;
        }
        .judge-table tbody td:first-child {
            text-align: left; padding-left: 10px;
            color: rgba(255,255,255,0.5); font-family: 'Inter', sans-serif;
            font-size: 10px;
        }
        .judge-table .total-row td {
            font-weight: 700; font-size: 13px;
        }
        .judge-table .total-row td:first-child { color: rgba(255,255,255,0.7); }

        /* ── Penalty list ── */
        .penalty-section { font-size: 10px; }
        .penalty-title {
            color: rgba(255,255,255,0.4); font-size: 9px;
            font-weight: 700; letter-spacing: 2px;
            text-transform: uppercase; margin-bottom: 5px;
        }
        .penalty-item {
            display: flex; justify-content: space-between;
            padding: 4px 8px; border-radius: 6px;
            background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2);
            margin-bottom: 3px;
            color: rgba(255,255,255,0.8);
        }
        .penalty-item .pval { color: #f87171; font-weight: 700; }
        .no-penalty { color: rgba(255,255,255,0.25); font-style: italic; font-size: 10px; }

        /* ── Center divider ── */
        .vs-divider {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 70px;
        }

        .vs-circle {
            width: 56px; height: 56px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            border: 1.5px solid rgba(255,255,255,0.12);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 900; color: rgba(255,255,255,0.6);
            letter-spacing: 1px;
        }

        .divider-line {
            width: 1px;
            background: linear-gradient(to bottom, transparent, rgba(255,255,255,0.15), transparent);
            flex: 1;
        }

        /* ── Footer ── */
        .footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 10px;
            padding: 8px 20px;
        }
        .footer-item { display: flex; align-items: center; gap: 8px; }
        .footer-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 8px #22c55e;
            animation: blink 2s ease-in-out infinite;
        }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        .footer-text { font-size: 10px; color: rgba(255,255,255,0.4); letter-spacing: 1px; }
        .footer-brand { font-size: 11px; font-weight: 700; color: #dc2626; }

        /* ── Score highlight animation ── */
        .highlight-score {
            animation: flashScore 0.8s ease-in-out;
        }
        @keyframes flashScore {
            0%   { opacity: 1; }
            30%  { opacity: 0.3; background: rgba(251,191,36,0.3); border-radius: 4px; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>

<div class="scoreboard">

    <!-- ═══════════════ HEADER ═══════════════ -->
    <div class="header">
        <div class="header-logo">PS</div>
        <div class="header-center">
            <div class="header-title">Pertandingan Seni</div>
            <div class="header-sub" id="headerSub">
                {{ strtoupper($matchType) }} &bull; {{ $pertandingan->kelas->nama_kelas ?? '-' }} &bull; {{ $pertandingan->arena->arena_name ?? 'Arena' }}
            </div>
        </div>
        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:6px;">
            <div class="header-badge" id="matchStatusBadge">⬤ LIVE</div>
            <div class="header-badge" style="font-family:'JetBrains Mono'; font-size:10px;" id="clockDisplay">--:--:--</div>
        </div>
    </div>

    <!-- ═══════════════ MAIN PANELS ═══════════════ -->
    <div class="panels">

        <!-- ── BLUE (Sudut Biru) ── -->
        <div class="team-panel panel-blue" id="panelBlue">

            <!-- Winner badge -->
            <div class="winner-badge">
                <span>🏆</span>
                <span>Pemenang</span>
            </div>

            <!-- Team header -->
            <div class="team-header">
                <div class="team-avatar avatar-blue" id="blueAvatarInitials">SB</div>
                <div class="team-info">
                    <div class="contingent-name blue-text" id="blueContingent">MEMUAT...</div>
                    <div class="player-names" id="bluePlayerNames">—</div>
                </div>
                <div class="side-label side-label-blue">SUDUT BIRU</div>
            </div>

            <!-- Final Score Big -->
            <div class="final-score-box final-score-box-blue">
                <div class="final-label">Skor Akhir</div>
                <div class="final-value final-value-blue" id="blueFinalScore">0.00</div>
                <div class="final-breakdown" id="blueBreakdown">Median (0.00) – Penalti (0.00)</div>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-label">Median</div>
                    <div class="stat-value" id="blueMedian">0.00</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Std Dev</div>
                    <div class="stat-value" id="blueStdDev">0.000000</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Penalti</div>
                    <div class="stat-value penalty" id="bluePenaltyTotal">0.00</div>
                </div>
            </div>

            <!-- Judge scores table -->
            <div class="judge-table-wrap">
                <table class="judge-table">
                    <thead>
                        <tr>
                            <th>Kriteria</th>
                            @for ($i = 1; $i <= ($jumlahJuri ?? 4); $i++)
                            <th>J{{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Correctness</td>
                            @for ($i = 1; $i <= ($jumlahJuri ?? 4); $i++)
                            <td id="blue-judge-{{ $i }}-correctness">9.90</td>
                            @endfor
                        </tr>
                        <tr>
                            <td>Flow/Stamina</td>
                            @for ($i = 1; $i <= ($jumlahJuri ?? 4); $i++)
                            <td id="blue-judge-{{ $i }}-category">0.00</td>
                            @endfor
                        </tr>
                        <tr class="total-row">
                            <td>Total</td>
                            @for ($i = 1; $i <= ($jumlahJuri ?? 4); $i++)
                            <td id="blue-judge-{{ $i }}-total" class="blue-text">9.90</td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Active penalties -->
            <div class="penalty-section">
                <div class="penalty-title">Penalti Aktif</div>
                <div id="bluePenaltyList">
                    <p class="no-penalty">Tidak ada penalti</p>
                </div>
            </div>

        </div>

        <!-- ── CENTER DIVIDER ── -->
        <div class="vs-divider">
            <div class="divider-line"></div>
            <div class="vs-circle">VS</div>
            <div class="divider-line"></div>
        </div>

        <!-- ── RED (Sudut Merah) ── -->
        <div class="team-panel panel-red" id="panelRed">

            <!-- Winner badge -->
            <div class="winner-badge">
                <span>🏆</span>
                <span>Pemenang</span>
            </div>

            <!-- Team header -->
            <div class="team-header">
                <div class="team-avatar avatar-red" id="redAvatarInitials">SM</div>
                <div class="team-info">
                    <div class="contingent-name red-text" id="redContingent">MEMUAT...</div>
                    <div class="player-names" id="redPlayerNames">—</div>
                </div>
                <div class="side-label side-label-red">SUDUT MERAH</div>
            </div>

            <!-- Final Score Big -->
            <div class="final-score-box final-score-box-red">
                <div class="final-label">Skor Akhir</div>
                <div class="final-value final-value-red" id="redFinalScore">0.00</div>
                <div class="final-breakdown" id="redBreakdown">Median (0.00) – Penalti (0.00)</div>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-label">Median</div>
                    <div class="stat-value" id="redMedian">0.00</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Std Dev</div>
                    <div class="stat-value" id="redStdDev">0.000000</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Penalti</div>
                    <div class="stat-value penalty" id="redPenaltyTotal">0.00</div>
                </div>
            </div>

            <!-- Judge scores table -->
            <div class="judge-table-wrap">
                <table class="judge-table">
                    <thead>
                        <tr>
                            <th>Kriteria</th>
                            @for ($i = 1; $i <= ($jumlahJuri ?? 4); $i++)
                            <th>J{{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Correctness</td>
                            @for ($i = 1; $i <= ($jumlahJuri ?? 4); $i++)
                            <td id="red-judge-{{ $i }}-correctness">9.90</td>
                            @endfor
                        </tr>
                        <tr>
                            <td>Flow/Stamina</td>
                            @for ($i = 1; $i <= ($jumlahJuri ?? 4); $i++)
                            <td id="red-judge-{{ $i }}-category">0.00</td>
                            @endfor
                        </tr>
                        <tr class="total-row">
                            <td>Total</td>
                            @for ($i = 1; $i <= ($jumlahJuri ?? 4); $i++)
                            <td id="red-judge-{{ $i }}-total" class="red-text">9.90</td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Active penalties -->
            <div class="penalty-section">
                <div class="penalty-title">Penalti Aktif</div>
                <div id="redPenaltyList">
                    <p class="no-penalty">Tidak ada penalti</p>
                </div>
            </div>

        </div>
    </div>

    <!-- ═══════════════ FOOTER ═══════════════ -->
    <div class="footer">
        <div class="footer-item">
            <div class="footer-dot"></div>
            <span class="footer-text">REALTIME SCORING &bull; <span id="footerTimestamp">--</span></span>
        </div>
        <div class="footer-text" id="footerMatchType">
            {{ strtoupper($matchType) }} &bull; Max {{ $maxJurus }} Jurus
        </div>
        <div class="footer-brand">EventSilat.com</div>
    </div>

</div>

<script>
    // Config from server
    const MATCH_ID   = {{ $id }};
    const NUM_JUDGES = {{ $jumlahJuri ?? 4 }};
    const MAX_JURUS  = {{ $maxJurus ?? 14 }};
    const MATCH_TYPE = '{{ $matchType ?? 'tunggal' }}';

    // Penalty rules from DB
    const dbRules    = @json($penaltyRules ?? []);
    const penaltyTypes = dbRules.map(r => ({ name: r.name, value: parseFloat(r.value), id: r.type }));

    // Local cache for change detection
    const prevScores = { blue: {}, red: {} };

    // ─────────────────────────────────────────
    // Clock
    // ─────────────────────────────────────────
    function updateClock() {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        const str = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
        document.getElementById('clockDisplay').textContent = str;
        document.getElementById('footerTimestamp').textContent =
            `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())} ${str}`;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ─────────────────────────────────────────
    // Fetch match info (players / contingent)
    // ─────────────────────────────────────────
    async function fetchMatchInfo() {
        try {
            const res    = await fetch(`/api/superadmin/matches/${MATCH_ID}`);
            const result = await res.json();
            if (result.success && result.data) renderMatchInfo(result.data);
        } catch (e) { console.warn('Match info fetch failed', e); }
    }

    function getInitials(name) {
        return name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
    }

    function renderMatchInfo(match) {
        const players = match.players || [];

        ['1', '2'].forEach(side => {
            const isBlue    = side === '1';
            const prefix    = isBlue ? 'blue' : 'red';
            const sidePlayers = players.filter(p => String(p.side_number) === side);

            const contingentEl  = document.getElementById(`${prefix}Contingent`);
            const playerNamesEl = document.getElementById(`${prefix}PlayerNames`);
            const avatarEl      = document.getElementById(`${prefix}AvatarInitials`);

            if (sidePlayers.length > 0) {
                const contingent = sidePlayers[0].player_contingent;
                contingentEl.textContent  = contingent.toUpperCase();
                playerNamesEl.textContent = sidePlayers.map(p => p.player_name).join(' / ');
                avatarEl.textContent      = getInitials(sidePlayers[0].player_name);
            } else {
                contingentEl.textContent  = '-';
                playerNamesEl.textContent = 'Tidak ada data';
            }
        });

        // Match status badge
        const statusMap = { berlangsung: '⬤ LIVE', belum_dimulai: '● READY', selesai: '■ FINAL' };
        const badge = document.getElementById('matchStatusBadge');
        badge.textContent = statusMap[match.status] || match.status;
        if (match.status === 'berlangsung') badge.style.color = '#4ade80';
        else if (match.status === 'selesai') badge.style.color = '#94a3b8';
    }

    // ─────────────────────────────────────────
    // Calculate stats for one side
    // ─────────────────────────────────────────
    function calcStats(judges, penalties, side) {
        const filtered = Object.values(judges).filter(d => d.side === side);

        const scores = [];
        for (let i = 1; i <= NUM_JUDGES; i++) {
            const jd = filtered.find(j => j.judge_id === i);
            scores.push(jd ? jd.total_score : 9.90);
        }
        scores.sort((a, b) => a - b);

        const median = scores.length % 2 === 0
            ? (scores[scores.length / 2 - 1] + scores[scores.length / 2]) / 2
            : scores[Math.floor(scores.length / 2)];

        const mean     = scores.reduce((s, v) => s + v, 0) / scores.length;
        const variance = scores.reduce((s, v) => s + Math.pow(v - mean, 2), 0) / scores.length;
        const stdDev   = Math.sqrt(variance);

        const sidePenalties = penalties.filter(p =>
            p.status === 'active' && (p.side === side || (!p.side && side === '1'))
        );
        let totalPenalty = 0;
        sidePenalties.forEach(p => totalPenalty += parseFloat(p.value));

        return { scores, filtered, median, stdDev, totalPenalty, finalScore: median + totalPenalty };
    }

    // ─────────────────────────────────────────
    // Render one side
    // ─────────────────────────────────────────
    function renderSide(prefix, side, judges, penalties) {
        const isBlue = side === '1';
        const stats  = calcStats(judges, penalties, side);

        // Final score
        const finalEl     = document.getElementById(`${prefix}FinalScore`);
        const breakdownEl = document.getElementById(`${prefix}Breakdown`);
        finalEl.textContent     = stats.finalScore.toFixed(2);
        breakdownEl.textContent = `Median (${stats.median.toFixed(2)}) – Penalti (${Math.abs(stats.totalPenalty).toFixed(2)})`;

        // Stats boxes
        document.getElementById(`${prefix}Median`).textContent      = stats.median.toFixed(2);
        document.getElementById(`${prefix}StdDev`).textContent      = stats.stdDev.toFixed(6);
        document.getElementById(`${prefix}PenaltyTotal`).textContent = Math.abs(stats.totalPenalty).toFixed(2);

        // Reset judge cells
        for (let i = 1; i <= NUM_JUDGES; i++) {
            const c = document.getElementById(`${prefix}-judge-${i}-correctness`);
            const k = document.getElementById(`${prefix}-judge-${i}-category`);
            const t = document.getElementById(`${prefix}-judge-${i}-total`);
            if (c) c.textContent = '9.90';
            if (k) k.textContent = '0.00';
            if (t) t.textContent = '9.90';
        }

        // Fill judge cells
        stats.filtered.forEach(data => {
            const jNum = data.judge_id;
            const c = document.getElementById(`${prefix}-judge-${jNum}-correctness`);
            const k = document.getElementById(`${prefix}-judge-${jNum}-category`);
            const t = document.getElementById(`${prefix}-judge-${jNum}-total`);

            if (c && k && t) {
                const prev = prevScores[isBlue ? 'blue' : 'red'][jNum];
                const changed = !prev ||
                    prev.correctness_score !== data.correctness_score ||
                    prev.category_score   !== data.category_score;

                c.textContent = data.correctness_score.toFixed(2);
                k.textContent = data.category_score.toFixed(2);
                t.textContent = data.total_score.toFixed(2);

                if (changed) {
                    [c, t].forEach(el => {
                        el.classList.add('highlight-score');
                        setTimeout(() => el.classList.remove('highlight-score'), 800);
                    });
                }

                prevScores[isBlue ? 'blue' : 'red'][jNum] = data;
            }
        });

        // Penalty list
        const penListEl = document.getElementById(`${prefix}PenaltyList`);
        const sidePens  = penalties.filter(p =>
            p.status === 'active' && (p.side === side || (!p.side && side === '1'))
        );
        const activeMap = {};
        sidePens.forEach(p => activeMap[p.type] = true);

        let hasActive = false;
        penListEl.innerHTML = '';

        penaltyTypes.forEach(rule => {
            if (activeMap[rule.id]) {
                hasActive = true;
                const div = document.createElement('div');
                div.className = 'penalty-item';
                div.innerHTML = `<span>${rule.name}</span><span class="pval">${Math.abs(rule.value).toFixed(2)}</span>`;
                penListEl.appendChild(div);
            }
        });

        if (!hasActive) {
            penListEl.innerHTML = '<p class="no-penalty">Tidak ada penalti aktif</p>';
        }

        return stats.finalScore;
    }

    // ─────────────────────────────────────────
    // Determine winner
    // ─────────────────────────────────────────
    function updateWinner(blueScore, redScore) {
        const panelBlue = document.getElementById('panelBlue');
        const panelRed  = document.getElementById('panelRed');

        panelBlue.classList.remove('winner');
        panelRed.classList.remove('winner');

        const diff = Math.abs(blueScore - redScore);
        if (diff < 0.001) return; // seri → tidak ada pemenang

        if (blueScore > redScore) {
            panelBlue.classList.add('winner');
        } else {
            panelRed.classList.add('winner');
        }
    }

    // ─────────────────────────────────────────
    // Main fetch & render
    // ─────────────────────────────────────────
    function fetchMatchData() {
        fetch(`/api/seni/tunggal-regu/events/${MATCH_ID}`)
            .then(r => r.json())
            .then(result => {
                if (result.status === 'success' && result.data) {
                    const judges    = result.data.judges    || {};
                    const penalties = result.data.penalties || [];

                    const blueScore = renderSide('blue', '1', judges, penalties);
                    const redScore  = renderSide('red',  '2', judges, penalties);

                    updateWinner(blueScore, redScore);
                }
            })
            .catch(e => console.error('Fetch error:', e));
    }

    // ─────────────────────────────────────────
    // WebSocket / Polling
    // ─────────────────────────────────────────
    function setup() {
        fetchMatchInfo();
        fetchMatchData();

        if (window.Echo) {
            window.Echo.channel(`pertandingan.${MATCH_ID}`)
                .listen('.tunggal.score.updated', () => { fetchMatchData(); })
                .listen('.penalty.updated', () => { fetchMatchInfo(); fetchMatchData(); });
            console.log(`WebSocket subscribed: pertandingan.${MATCH_ID}`);
        } else {
            console.warn('Echo not available, polling every 2 seconds');
            setInterval(() => { fetchMatchInfo(); fetchMatchData(); }, 2000);
        }
    }

    document.addEventListener('DOMContentLoaded', setup);
</script>
</body>
</html>

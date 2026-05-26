<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Pertandingan - Digital Scoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    @include('components.auto-refresh')
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at 50% 50%, #1e1b4b 0%, #0f172a 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .pulse-glow {
            box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 20px rgba(99, 102, 241, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(99, 102, 241, 0);
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full glass-card rounded-3xl p-10 text-center relative overflow-hidden">
        <!-- Decorative Glow -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl"></div>

        <!-- Loader/Icon -->
        <div class="mb-8 relative flex justify-center">
            <div class="w-24 h-24 rounded-full bg-indigo-500/10 flex items-center justify-center pulse-glow border border-indigo-500/30">
                <svg class="h-10 w-10 text-indigo-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>

        <h1 class="text-3xl font-extrabold text-white tracking-tight mb-3">Menunggu Pertandingan</h1>
        <p class="text-indigo-200/60 text-lg mb-6 leading-relaxed">
            {{ $message ?? 'Tidak ada pertandingan yang sedang berlangsung di arena Anda saat ini.' }}
        </p>

        @if(isset($arena_name) && $arena_name !== '-')
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500/10 border border-indigo-500/20 rounded-full text-indigo-300 font-semibold text-sm mb-8">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                {{ $arena_name }}
            </div>
        @endif

        <div class="text-sm text-indigo-300/40">
            <p>Halaman ini akan otomatis memuat pertandingan baru begitu operator mengaktifkannya.</p>
        </div>
    </div>
</body>
</html>

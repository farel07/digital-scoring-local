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

    {{-- 1. Memuat Laravel Echo dari app.js --}}
    @vite(['resources/js/app.js'])
    
    {{-- 2. Memuat file listener eksternal kita --}}
    <script type="module" src="{{ asset('js/listenSeni.js') }}"></script>
</body>
</html>
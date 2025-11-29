<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penilaian Penalti</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- PENTING: Tambahkan CSRF Token untuk keamanan --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 min-h-screen p-5">

    {{-- Data Tersembunyi untuk dikirim via API --}}
    <input type="hidden" id="pertandingan_id" value="1">

    <div class="max-w-6xl mx-auto bg-white shadow-lg">
        <!-- ... (Header tidak berubah) ... -->
        <div class="p-6">
            <div class="border border-gray-300 rounded-md shadow-sm overflow-hidden">
                <table class="w-full">
                    <!-- ... (thead tidak berubah) ... -->
                    <tbody id="penaltyTable" class="bg-white divide-y divide-gray-200">
                        {{-- Data penalti akan digenerate oleh JavaScript --}}
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-between items-center bg-gray-50 px-4 py-3 rounded-md border">
                <div class="text-lg font-bold text-gray-700">Total Penalti</div>
                <div class="text-red-600 text-xl font-bold" id="grandTotal">0.00</div>
            </div>
        </div>
    </div>

    <script>
        // =======================================================
        // DATA & KONFIGURASI
        // =======================================================
        const penalties = [
            { name: 'WAKTU', value: -0.50, active: false, id: 'waktu' },
            { name: 'SETIAP KALI KELUAR GARIS', value: -0.50, active: false, id: 'keluar_garis' },
            { name: 'SENJATA JATUH TIDAK SESUAI DESKRIPSI', value: -0.50, active: false, id: 'senjata_jatuh_1' },
            { name: 'SENJATA TIDAK JATUH SESUAI DESKRIPSI', value: -0.50, active: false, id: 'senjata_jatuh_2' },
            { name: 'TIDAK ADA SALAM & MENGELUARKAN SUARA', value: -0.50, active: false, id: 'salam_suara' },
            { name: 'BAJU / SENJATA TIDAK SESUAI (PATAH)', value: -0.50, active: false, id: 'atribut' },
        ];

        const penaltyTableBody = document.getElementById('penaltyTable');
        const grandTotalEl = document.getElementById('grandTotal');
        const pertandinganId = document.getElementById('pertandingan_id').value;

        // =======================================================
        // FUNGSI UTAMA
        // =======================================================
        
        // Fungsi untuk mengirim event ke server
        function sendPenaltyEvent(penaltyId, value) {
            console.log(`Mengirim event: penaltyId=${penaltyId}, value=${value}`);
            fetch('/dewan/kirim-penalti', { // Pastikan URL ini sesuai dengan route Anda
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    pertandingan_id: pertandinganId,
                    penalty_id: penaltyId, // ID unik penalti, e.g., 'keluar_garis'
                    value: value // Nilai penalti, e.g., -0.50 atau 0
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Gagal mengirim event penalti');
                return response.json();
            })
            .then(data => console.log('Respon Server:', data))
            .catch(error => console.error('Error:', error));
        }

        // Fungsi untuk mengaktifkan penalti
        function addPenalty(index) {
            const penalty = penalties[index];
            if (penalty.active) return; // Jangan lakukan apa-apa jika sudah aktif
            penalty.active = true;
            sendPenaltyEvent(penalty.id, penalty.value);
            render(); // Perbarui seluruh tampilan
        }

        // Fungsi untuk menonaktifkan penalti
        function clearPenalty(index) {
            const penalty = penalties[index];
            if (!penalty.active) return; // Jangan lakukan apa-apa jika sudah non-aktif
            penalty.active = false;
            sendPenaltyEvent(penalty.id, 0); // Kirim nilai 0 untuk menandakan clear
            render(); // Perbarui seluruh tampilan
        }

        // Fungsi untuk menghitung total
        function updateGrandTotal() {
            const total = penalties.reduce((sum, p) => p.active ? sum + p.value : sum, 0);
            grandTotalEl.textContent = total.toFixed(2);
        }

        // Fungsi untuk me-render seluruh tabel berdasarkan data 'penalties'
        function render() {
            penaltyTableBody.innerHTML = ''; // Kosongkan tabel
            penalties.forEach((p, index) => {
                const tr = document.createElement('tr');
                tr.className = p.active ? 'bg-red-50' : 'hover:bg-gray-50';
                
                tr.innerHTML = `
                    <td class="px-4 py-3 text-sm text-gray-700">${p.name}</td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="clearPenalty(${index})" class="bg-blue-500 text-white px-9 py-5 rounded-md hover:bg-blue-600 text-xs font-medium transition-colors">Clear</button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="addPenalty(${index})" class="bg-red-500 text-white px-9 py-5 rounded-md hover:bg-red-600 text-xs font-bold transition-colors">${p.value.toFixed(2)}</button>
                    </td>
                    <td class="px-4 py-3 text-center text-sm ${p.active ? 'text-red-600 font-bold' : 'text-gray-700 font-semibold'}">
                        ${p.active ? p.value.toFixed(2) : '0.00'}
                    </td>
                `;
                penaltyTableBody.appendChild(tr);
            });
            updateGrandTotal();
        }

        // Inisialisasi tampilan saat halaman pertama kali dimuat
        document.addEventListener('DOMContentLoaded', render);
    </script>
</body>
</html>
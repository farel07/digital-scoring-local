// Mengambil ID pertandingan dari Meta Tag (rekomendasi) atau input hidden
// Pastikan di HTML ada: <meta name="pertandingan-id" content="{{ $pertandingan->id }}">
const pertandinganId = document.querySelector('meta[name="pertandingan-id"]')?.getAttribute('content') || 2;

// Ambil ID Juri dari URL (Segmen terakhir)
// Contoh URL: http://website.com/juri/tanding/2/5  (5 adalah id juri)
const pathSegments = window.location.pathname.split('/');
const juriIdFromUrl = pathSegments[pathSegments.length - 1]; 
// Atau sesuaikan index array jika struktur URL berbeda


// State Management: Memisahkan counter untuk Blue dan Red
const counters = {
    blue: { bina: 1, teguran: 1, peringatan: 1 },
    red:  { bina: 1, teguran: 1, peringatan: 1 }
};

function sendAction(action, filter = 'blue') {
    // Validasi filter (jaga-jaga jika input salah)
    if (!['blue', 'red'].includes(filter)) {
        console.error("Tim tidak dikenali");
        return;
    }

    let penalty_id = '';
    let value = 0;

    // Logika Penentuan Value & ID
    switch (action) {
        case "BINA":
            penalty_id = 'bina';
            value = counters[filter].bina; // Ambil nilai saat ini
            counters[filter].bina++;       // Increment untuk berikutnya
            break;
        
        case "TEGURAN":
            penalty_id = 'teguran';
            value = counters[filter].teguran;
            counters[filter].teguran++;
            break;

        case "PERINGATAN":
            penalty_id = 'peringatan';
            value = counters[filter].peringatan;
            counters[filter].peringatan++;
            break;

        case "JATUH":
            penalty_id = 'jatuhan';
            // Jatuhan biasanya poin tetap (misal 3) atau flag 1. 
            // Di sini saya set 1 agar tidak undefined seperti kode asli.
            value = 1; 
            break;

        default:
            alert("Action tidak dikenali");
            return; // Hentikan fungsi jika action salah
    }

    console.log(`Sending ${action} for ${filter}: Value ${value}`);

    // Kirim Data
    fetch('/dewan/kirim-penalti-tanding', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            pertandingan_id: pertandinganId, // ID Dinamis
            penalty_id: penalty_id,
            filter: filter, // 'blue' atau 'red'
            value: value
        })
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        console.log('Success:', data);
        // Opsional: Update tampilan UI di sini jika perlu
    })
    .catch((error) => {
        console.error('Error:', error);
        // Opsional: Rollback counter jika gagal fetch (agar sinkron)
        if (action !== "JATUH") counters[filter][penalty_id]--; 
    });
}

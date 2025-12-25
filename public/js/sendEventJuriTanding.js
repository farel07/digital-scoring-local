const pertandinganId = document.querySelector('meta[name="pertandingan-id"]')?.getAttribute('content') || 2;
const pathSegments = window.location.pathname.split('/');
const juriIdFromUrl = pathSegments[pathSegments.length - 1]; 

var poin;

function sendPoin(type, filter = 'blue') {
    // kondisi poin
    if(type == 'PUKUL'){
        poin = 1;
    } else if(type == 'TENDANG'){
        poin = 2;
    }

   fetch('/juri-tanding/kirim-poin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            pertandingan_id: pertandinganId,
            juri_id: juriIdFromUrl, // Pastikan ini ada
            type: type,
            filter: filter,
            poin: poin
        })
    })
    .then(response => response.json())
    .then(data => {
        // Feedback Visual untuk Juri
        if(data.status == 'waiting') {
            console.log("Menunggu partner juri...");
            // Bisa kasih efek tombol kedip kuning
        } else if (data.status == 'valid') {
            console.log("Poin Masuk!");
            // Bisa kasih efek tombol kedip hijau
        } else {
            console.log("Terjadi kesalahan:", data.pendingVote);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}
    // Ambil data poin dan filter dari elemen HTML atau variabel JavaScript
    const poin = document.getElementById('poinInput').value; // Contoh: input poin
    const filter = document.getElementById('filterInput').value; // Contoh: input filter
    const pertandingan_id = document.getElementById('pertandingan_id').value; // Contoh: input pertandingan ID
    const type = 'seni_tunggal_regu'; // Sesuaikan dengan kebutuhan
    const id_user = window.location.pathname.split("/").pop();
    const role = 'juri-' + id_user; // Sesuaikan dengan kebutuhan

    console.log(pertandingan_id);

function kirim_poin() {

    fetch('/juri/kirim_poin_seni_tunggal_regu', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            poin,
            filter,
            pertandingan_id,
            type,
            role
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Success:', data);
    })
    .catch((error) => {
        console.error('Error:', error);
    });

}
    
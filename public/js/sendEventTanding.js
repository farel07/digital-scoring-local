const pertandingan_id = 2;

function sendAction(action, filter = 'blue') {
    let penalty_id
    if (action === "BINA"){
        console.log('bina');
        penalty_id = 1; // ID untuk BINA
    } else if (action === "TEGURAN"){
        console.log('teguran');
        penalty_id = 2; // ID untuk TEGURAN
    } else if (action === "PERINGATAN"){
        console.log('peringatan');
        penalty_id = 3; // ID untuk PERINGATAN
    } else if (action === "JATUH"){
        console.log('jatuhan');
        penalty_id = 4; // ID untuk JATUH
    } else {
        alert("Action tidak dikenali");
    }

    let value = 1; // Nilai default untuk semua action
     fetch('/dewan/kirim-penalti-tanding', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            penalty_id,
            filter,
            pertandingan_id,
            value
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
<!-- HTML SIMULASI LAYAR -->
<div style="display: flex; gap: 20px;">
    <!-- SCOREBOARD -->
    <div>
        <h1>TIM BIRU: <span id="skor-blue">0</span></h1>
        <h1>TIM MERAH: <span id="skor-red">0</span></h1>
    </div>

    <!-- LAMPU INDIKATOR JURI (Untuk Feedback Input) -->
    <div style="border: 1px solid #000; padding: 10px;">
        <h3>Indikator Juri (Realtime)</h3>
        <div id="lampu-juri-1" style="width: 50px; height: 50px; background: gray; border-radius: 50%; display:inline-block;">1</div>
        <div id="lampu-juri-2" style="width: 50px; height: 50px; background: gray; border-radius: 50%; display:inline-block;">2</div>
        <div id="lampu-juri-3" style="width: 50px; height: 50px; background: gray; border-radius: 50%; display:inline-block;">3</div>
    </div>
</div>

@vite(['resources/js/app.js'])

<script type="module">
    let skorBlue = 0;
    let skorRed = 0;

    window.Echo.channel(`kirim-poin-tanding-2`).listen(
        ".KirimPoinTanding",
        (event) => {
            console.log(`Event diterima: [${event.status}] Juri ${event.juri_id}`);

            // --- LOGIKA 1: INPUT MASUK (NYALAKAN LAMPU) ---
            if (event.status === 'input') {
                nyalakanLampuJuri(event.juri_id, event.filter);
            } 
            
            // --- LOGIKA 2: POIN SAH (TAMBAH SKOR) ---
            else if (event.status === 'sah') {
                updateSkor(event.filter, event.poin);
                resetLampuJuri(); // Opsional: Matikan semua lampu setelah poin sah
            }
        }
    );

    // Fungsi Update Skor
    function updateSkor(tim, poin) {
        let nilaiPoin = parseInt(poin);
        if (tim === 'blue') {
            skorBlue += nilaiPoin;
            document.getElementById('skor-blue').innerText = skorBlue;
            // Efek animasi skor (opsional)
            alert(`POIN SAH TIM BIRU +${nilaiPoin}`);
        } else {
            skorRed += nilaiPoin;
            document.getElementById('skor-red').innerText = skorRed;
            alert(`POIN SAH TIM MERAH +${nilaiPoin}`);
        }
    }

    // Fungsi Visual Lampu Juri
    function nyalakanLampuJuri(juriId, tim) {
        const lampu = document.getElementById(`lampu-juri-${juriId}`);
        if(lampu) {
            // Warnai sesuai tim (Biru/Merah)
            lampu.style.backgroundColor = (tim === 'blue') ? 'blue' : 'red';
            lampu.style.color = 'white';
            
            // Otomatis mati setelah 3 detik (kalau tidak sah)
            setTimeout(() => {
                lampu.style.backgroundColor = 'gray'; 
            }, 3000);
        }
    }

    // Fungsi Reset Semua Lampu (Dipanggil saat poin sah)
    function resetLampuJuri() {
        // Beri jeda sedikit biar kelihatan lampunya nyala dulu
        setTimeout(() => {
            document.getElementById('lampu-juri-1').style.backgroundColor = 'gray';
            document.getElementById('lampu-juri-2').style.backgroundColor = 'gray';
            document.getElementById('lampu-juri-3').style.backgroundColor = 'gray';
        }, 500); 
    }
</script>
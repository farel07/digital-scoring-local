const PERTANDINGAN_ID = 2;

// ========================================
// EVENT LISTENER UNTUK PENALTI (Existing)
// ========================================
window.Echo.channel(`kirim-penalti-tanding-${PERTANDINGAN_ID}`).listen(
    ".KirimPenaltiTanding",
    (event) => {
        console.log("Data Penalti Diterima:", event);

        // Destrukturisasi data dari event
        // event format: { penalty_id: "bina", value: 1, filter: "blue", pertandingan_id: 2 }
        const { penalty_id, value, filter } = event;

        // 1. LOGIKA UNTUK MENYALAKAN GAMBAR (BINA/TEGURAN/PERINGATAN)
        // Format ID di blade: id="blue-notif-binaan-1"
        // Kita petakan penalty_id jika namanya berbeda sedikit
        let penaltyType = penalty_id;
        if (penalty_id === "bina") penaltyType = "binaan"; // Menyesuaikan id 'blue-notif-binaan-x'

        const iconId = `${filter}-notif-${penaltyType}-${value}`;
        const iconElement = document.getElementById(iconId);

        if (iconElement) {
            // Berikan filter warna kuning atau background kuning
            // Menggunakan background agar terlihat menyala di sekitar icon
            iconElement.style.backgroundColor = "#ffc107"; // Warna Kuning Bootstrap
            iconElement.style.borderRadius = "10px";
            iconElement.style.boxShadow = "0 0 15px #ffc107";
            iconElement.style.padding = "5px";
        }

        // 2. LOGIKA UNTUK MENGISI TABEL STATISTIK DI BAWAH
        // Format ID di blade: id="stat-blue-teguran1"
        let tableStatId = "";

        if (penalty_id === "jatuhan") {
            tableStatId = `stat-${filter}-jatuhan`;
        } else if (penalty_id === "teguran") {
            tableStatId = `stat-${filter}-teguran${value}`;
        } else if (penalty_id === "peringatan") {
            tableStatId = `stat-${filter}-peringatan${value}`;
        }

        const statElement = document.getElementById(tableStatId);
        if (statElement) {
            // Untuk jatuhan, increment nilai yang ada (+1)
            // Untuk penalti lainnya (Teguran/Peringatan), set ke 1
            if (penalty_id === "jatuhan") {
                const currentValue = parseInt(statElement.innerText) || 0;
                statElement.innerText = currentValue + 1;
            } else {
                statElement.innerText = 1;
            }

            // Berikan highlight sejenak agar user tahu ada perubahan
            statElement.style.transition = "background-color 0.5s";
            let originalBg = statElement.classList.contains("bg-primary")
                ? "blue"
                : "red";
            statElement.style.backgroundColor = "white";
            statElement.style.color = "black";

            setTimeout(() => {
                statElement.style.backgroundColor = "";
                statElement.style.color = "";
            }, 1000);
        }
    }
);

// ========================================
// EVENT LISTENER UNTUK PUKUL DAN TENDANG
// ========================================
window.Echo.channel(`kirim-poin-tanding-${PERTANDINGAN_ID}`).listen(
    ".KirimPoinTanding",
    (event) => {
        // event format dari backend: { status: "input"/"sah", juri_id: 1-3, filter: "blue"/"red", type: "PUKUL"/"TENDANG", poin: 1/2 }
        const { status, juri_id, filter, type, poin } = event;

        // Konversi type ke lowercase untuk matching dengan ID HTML (pukul/tendang)
        const teknik = type.toLowerCase();

        // --- LOGIKA 1: INPUT MASUK (NYALAKAN INDIKATOR JURI) ---
        if (status === "input") {
            nyalakanJuriIndicator(juri_id, filter, teknik);
        }

        // --- LOGIKA 2: POIN SAH (TAMBAH SKOR + UPDATE STATS) ---
        else if (status === "sah") {
            updateTotalPoin(filter, poin);
            updateStatsTeknik(filter, teknik);
            resetJuriIndicators(filter, teknik);
        }
    }
);

// Fungsi untuk menyalakan indikator juri (background kuning)
function nyalakanJuriIndicator(juriId, tim, teknik) {
    // Format ID di blade: id="blue-notif-juri-1-pukul" atau "red-notif-juri-2-tendang"
    const indicatorId = `${tim}-notif-juri-${juriId}-${teknik}`;
    const indicator = document.getElementById(indicatorId);

    if (indicator) {
        // PENTING: Remove class bg-light agar background-color terlihat
        indicator.classList.remove("bg-light");

        // Nyalakan dengan background kuning
        indicator.style.backgroundColor = "#ffc107"; // Kuning
        indicator.style.transition = "background-color 0.3s";
        indicator.style.boxShadow = "0 0 10px #ffc107";

        // Auto-reset setelah 3 detik jika tidak sah
        setTimeout(() => {
            // Cek apakah masih kuning (belum di-reset oleh poin sah)
            if (indicator.style.backgroundColor === "rgb(255, 193, 7)") {
                indicator.style.backgroundColor = "";
                indicator.style.boxShadow = "";
                // Kembalikan class bg-light
                indicator.classList.add("bg-light");
            }
        }, 3000);
    }
}

// Fungsi untuk update total poin tim
function updateTotalPoin(tim, poin) {
    // Format ID di blade: id="total-point-blue" atau "total-point-red"
    const totalPointId = `total-point-${tim}`;
    const totalElement = document.getElementById(totalPointId);

    if (totalElement) {
        const currentTotal = parseInt(totalElement.innerText) || 0;
        const newTotal = currentTotal + parseInt(poin);
        totalElement.innerText = newTotal;

        // Efek animasi flash
        totalElement.style.transition = "transform 0.3s, color 0.3s";
        totalElement.style.transform = "scale(1.1)";
        totalElement.style.color = "#ffc107"; // Flash kuning

        setTimeout(() => {
            totalElement.style.transform = "scale(1)";
            totalElement.style.color = "";
        }, 300);
    }
}

// Fungsi untuk update stats table (pukulan/tendangan)
function updateStatsTeknik(tim, teknik) {
    // Format ID di blade: id="stat-blue-pukul" atau "stat-red-tendang"
    const statsId = `stat-${tim}-${teknik}`;
    const statsElement = document.getElementById(statsId);

    if (statsElement) {
        const currentValue = parseInt(statsElement.innerText) || 0;
        statsElement.innerText = currentValue + 1;

        // Highlight sejenak
        statsElement.style.transition = "background-color 0.5s";
        statsElement.style.backgroundColor = "white";
        statsElement.style.color = "black";

        setTimeout(() => {
            statsElement.style.backgroundColor = "";
            statsElement.style.color = "";
        }, 1000);
    }
}

// Fungsi untuk reset semua indikator juri setelah poin sah
function resetJuriIndicators(tim, teknik) {
    // Reset semua juri (1, 2, 3) untuk teknik tertentu
    setTimeout(() => {
        for (let i = 1; i <= 3; i++) {
            const indicatorId = `${tim}-notif-juri-${i}-${teknik}`;
            const indicator = document.getElementById(indicatorId);
            if (indicator) {
                indicator.style.backgroundColor = "";
                indicator.style.boxShadow = "";
                // Kembalikan class bg-light
                indicator.classList.add("bg-light");
            }
        }
    }, 500); // Delay 500ms biar keliatan dulu lampunya nyala
}

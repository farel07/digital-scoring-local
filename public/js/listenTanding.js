const PERTANDINGAN_ID = 2;

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

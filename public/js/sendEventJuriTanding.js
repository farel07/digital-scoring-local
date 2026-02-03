const pertandinganId =
    document
        .querySelector('meta[name="pertandingan-id"]')
        ?.getAttribute("content") || 2;
const juriId = document
    .querySelector('meta[name="user-id"]')
    ?.getAttribute("content"); // Get authenticated user ID

var poin;

function sendPoin(type, filter = "blue") {
    // kondisi poin
    if (type == "PUKUL") {
        poin = 1;
    } else if (type == "TENDANG") {
        poin = 2;
    }

    console.log("📤 Mengirim poin:", {
        pertandingan_id: pertandinganId,
        juri_id: juriId,
        type,
        filter,
        poin,
    });

    fetch("/juri-tanding/kirim-poin", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify({
            pertandingan_id: pertandinganId,
            juri_id: juriId, // FIXED: now uses authenticated user ID
            type: type,
            filter: filter,
            poin: poin,
        }),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            console.log("📥 Response data:", data);

            // Feedback Visual untuk Juri
            if (data.status == "waiting") {
                console.log("✅ Input tercatat. Menunggu juri lain...");
                // Bisa kasih efek tombol kedip kuning
            } else if (data.status == "valid") {
                console.log("✅ Poin SAH! Kedua juri setuju.");
                // Bisa kasih efek tombol kedip hijau
            } else if (data.status == "ignored") {
                console.log("⚠️ Input diabaikan (juri yang sama menekan 2x)");
            } else {
                console.log("⚠️ Status tidak dikenal:", data.status);
            }
        })
        .catch((error) => {
            console.error("❌ Terjadi Kesalahan:", error);
            console.error("Error details:", error.message);
            alert(
                "Gagal mengirim poin. Silakan cek koneksi atau console untuk detail.",
            );
        });
}

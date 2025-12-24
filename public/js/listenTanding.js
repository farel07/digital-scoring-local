// alert("sadasd");
const PERTANDINGAN_ID = 2;

window.Echo.channel(`kirim-penalti-tanding-2`).listen(
    ".KirimPenaltiTanding",
    (event) => {
        console.log("Pesan diterima:", event);
        alert("Penalti diterima: " + JSON.stringify(event));
    }
);

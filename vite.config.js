import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    server: {
        host: "0.0.0.0", // Wajib agar bisa diakses dari jaringan
        origin: "http://192.168.0.167:5173", // Port Vite dev server WAJIB untuk fix CORS
        cors: true, // Tambahkan ini untuk mengaktifkan CORS
    },
});

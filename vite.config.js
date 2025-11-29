import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // Wajib agar bisa diakses dari jaringan
        origin: 'http://192.168.110.90:5173', // Ganti IP ini dengan IP komputer Anda
         cors: true // Tambahkan ini untuk mengaktifkan CORS
    }
});
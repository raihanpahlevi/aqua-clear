import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // Dipaksa selalu terang — jangan ikut prefers-color-scheme OS/browser user.
    // Sistem ini sengaja TIDAK punya toggle/class "dark" di manapun, jadi varian dark: tidak akan pernah aktif.
    darkMode: 'selector',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Public Sans"', ...defaultTheme.fontFamily.sans],
                display: ['"Space Grotesk"', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                // Palet BIRU LANGIT + PUTIH (revisi user 2026-07-09, menggantikan biru laut).
                // Nama token lama (teal-*/paper/sand/lumpur) sengaja dipertahankan supaya
                // 60+ view tidak perlu diubah — anggap ini "slot" warna, bukan nama literal:
                //   teal-deep = sidebar/brand, teal-mid = tombol/link/grafik,
                //   paper = background putih, sand = tint kartu biru muda, lumpur = border.
                ink: '#1A2733',
                'teal-deep': '#1265AF',
                'teal-mid': '#1878C8',
                paper: '#FFFFFF',
                sand: '#D9EBFA',
                lumpur: '#7591A8',
                sehat: '#3F8A5E',
                perhatian: '#C98A2E',
                kritis: '#B23B3B',
            },
        },
    },

    plugins: [forms],
};

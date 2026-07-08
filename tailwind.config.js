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
                // Primer digeser ke BIRU LAUT (permintaan client 2026-07-08, approve user).
                // Nama token "teal-*" sengaja dipertahankan supaya 60+ view tidak perlu diubah —
                // anggap "teal" = warna primer, nilainya sekarang biru laut.
                ink: '#161D23',
                'teal-deep': '#12303F',
                'teal-mid': '#2D6480',
                paper: '#FAF7F0',
                sand: '#E4D9BE',
                lumpur: '#7C6B4F',
                sehat: '#3F8A5E',
                perhatian: '#C98A2E',
                kritis: '#B23B3B',
            },
        },
    },

    plugins: [forms],
};

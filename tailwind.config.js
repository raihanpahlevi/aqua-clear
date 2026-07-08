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
                ink: '#17211D',
                'teal-deep': '#143C36',
                'teal-mid': '#2B6357',
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

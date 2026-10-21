import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import colors from 'tailwindcss/colors';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                slate: {
                    ...colors.slate,
                    50: '#f4f6fa',
                    100: '#edf1f6',
                    200: '#dce2eb',
                    300: '#bdc8d8',
                    400: '#76869b',
                    500: '#637084',
                    600: '#4c5d74',
                    700: '#344966',
                    800: '#233b5c',
                    900: '#172a4c',
                    950: '#10213d',
                },
                indigo: {
                    ...colors.blue,
                    50: '#eff4ff',
                    100: '#dce7ff',
                    500: '#3974f0',
                    600: '#235de7',
                    700: '#1c4dbf',
                    800: '#1e4194',
                },
            },
            fontFamily: {
                sans: ['IBM Plex Sans', ...defaultTheme.fontFamily.sans],
                display: ['Manrope', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};

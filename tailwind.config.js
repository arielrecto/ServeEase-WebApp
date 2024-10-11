import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './node_modules/@inertiaui/modal-vue/src/**/*.{js,vue}',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        forms,
        require('daisyui'),
        require('@tailwindcss/typography'),
    ],

    daisyui: {
        themes: [
            {
                mytheme: {

                    "primary": "#003eff",

                    "secondary": "#009f00",

                    "accent": "#0086ff",

                    "neutral": "#091319",

                    "base-100": "#fffaff",

                    "info": "#7ec9ff",

                    "success": "#00cb8b",

                    "warning": "#ffc900",

                    "error": "#f50030",
                },
            },
        ],
    },

};

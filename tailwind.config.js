import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
        "./node_modules/@inertiaui/modal-vue/src/**/*.{js,vue}",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: "#003eff",
                    50: "#eef5ff",
                    100: "#e0edff",
                    200: "#c7ddff",
                    300: "#a1c7ff",
                    400: "#75a6ff",
                    500: "#477eff",
                    600: "#1b4fff",
                    700: "#0035db",
                    800: "#0035db",
                    900: "#002fb8",
                },
                secondary: {
                    DEFAULT: "#FFB800",
                    50: "#fff9eb",
                    100: "#ffefc7",
                    200: "#ffde85",
                    300: "#ffc847",
                    400: "#ffb800",
                    500: "#e6a600",
                    600: "#cc9200",
                    700: "#a67700",
                    800: "#805c00",
                    900: "#664900",
                },
            },
        },
    },

    plugins: [forms, require("daisyui"), require("@tailwindcss/typography")],

    daisyui: {
        themes: [
            {
                mytheme: {
                    primary: "#003eff",

                    secondary: "#009f00",

                    accent: "#0086ff",

                    neutral: "#091319",

                    "base-100": "#fffaff",

                    info: "#7ec9ff",

                    success: "#00cb8b",

                    warning: "#ffc900",

                    error: "#f50030",
                },
            },
        ],
    },
};

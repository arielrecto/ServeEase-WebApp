import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

// PrimeVue
import PrimeVue from "primevue/config";
import { definePreset } from "@primevue/themes";
import Aura from "@primevue/themes/aura";
const themePreset = definePreset(Aura, {
    semantic: {
        primary: {
            50: "#003eff",
            100: "#003eff",
            200: "#003eff",
            300: "#003eff",
            400: "#003eff",
            500: "#003eff",
            600: "#003eff",
            700: "#003eff",
            800: "#003eff",
            900: "#003eff",
            950: "#003eff",
        },
    },
});

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: themePreset,
                    options: {
                        darkModeSelector: false || "none",
                    },
                },
            })
            .mount(el);
    },
    progress: {
        color: "#4B5563",
        showSpinner: true,
    },
});

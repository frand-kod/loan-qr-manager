import "./bootstrap";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { Ziggy } from "./ziggy.js";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue");
        const page = pages[`./Pages/${name}.vue`];

        if (!page) {
            throw new Error(
                `Halaman "${name}.vue" tidak ditemukan di resources/js/Pages/`
            );
        }

        return typeof page === "function" ? page() : page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
});

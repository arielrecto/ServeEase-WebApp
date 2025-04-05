<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Hero from '@/Components/Guest/Hero.vue';
import ServicesList from '@/Components/Guest/ServicesList.vue';
import Header from '@/Components/Guest/Header.vue';
import Footer from '@/Components/Footer.vue';
import NavLink from '@/Components/NavLink.vue';
import About from '@/Components/Guest/About.vue';
import ComboBox from '@/Components/Form/ComboBox.vue';

defineProps({
    pages: {
        type: Array,
        required: true,
    },
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    services: Array,
    totalServices: Number
});
</script>

<template>

    <Head title="ServEase" />

    <div class="min-h-screen bg-gray-100">
        <Header :canLogin="canLogin" :canRegister="canRegister" :pages="pages" />

        <main>

            <Hero />
            <!-- <ComboBox /> -->
            <About />
            <ServicesList :services="services" :totalServices="totalServices" />
        </main>

        <Footer />
    </div>
</template>

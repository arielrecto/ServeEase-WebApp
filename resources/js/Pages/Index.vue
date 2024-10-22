<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Hero from '@/Components/Guest/Hero.vue';
import ServicesList from '@/Components/Guest/ServicesList.vue';
import Header from '@/Components/Guest/Header.vue';
import Footer from '@/Components/Footer.vue';
import NavLink from '@/Components/NavLink.vue';

defineProps({
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
});
</script>

<template>
    <Head title="ServEase" />

    <div class="min-h-screen bg-gray-100">
        <Header :canLogin="canLogin" :canRegister="canRegister" />

        <main>
            <Hero />
            <ServicesList />
        </main>

        <Footer />
    </div>
</template>

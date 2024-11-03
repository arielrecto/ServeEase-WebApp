<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Header from '@/Components/Guest/Header.vue';
import Footer from '@/Components/Footer.vue';

defineProps(['service']);
</script>

<template>
    <Head title="Services" />

    <div class="min-h-screen bg-gray-100">
        <Header :canLogin="canLogin" :canRegister="canRegister" />
        <main class="container py-8 mx-auto space-y-10">
            <section class="px-20 space-y-4">
                <div class="w-full mx-auto overflow-hidden text-center rounded-lg h-96">
                    <img :src="service.thumbnail" :alt="service.name" class="object-cover">
                </div>

                <h1 class="text-3xl font-bold">
                    {{ service.name }}
                </h1>

                <p class="leading-relaxed text-gray-600">
                    {{ service.description }}
                </p>
            </section>
        </main>
        <Footer />
    </div>
</template>

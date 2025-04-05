<script setup>
import { Head } from '@inertiajs/vue3';
import Header from '@/Components/Guest/Header.vue';
import Footer from '@/Components/Footer.vue';

const props = defineProps({
    page: {
        type: Object,
        required: true
    },
    canLogin: Boolean,
    canRegister: Boolean
});
</script>

<template>

    <Head :title="page.name" />

    <div class="min-h-screen bg-gray-100">
        <!-- Header -->
        <Header :canLogin="canLogin" :canRegister="canRegister" />

        <!-- Main Content -->
        <main class="container h-screen px-4 py-12 mx-auto sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <!-- Page Title -->
                <h1 class="mb-8 text-3xl font-bold text-gray-900">
                    {{ page.name }}
                </h1>

                <!-- Page Content -->
                <div class="prose prose-lg max-w-none" v-html="page.content" />
            </div>
        </main>

        <!-- Footer -->
        <Footer />
    </div>
</template>

<style>
.prose {
    @apply text-gray-700;
}

.prose h2 {
    @apply text-2xl font-semibold text-gray-900 mb-4 mt-8;
}

.prose p {
    @apply mb-4 leading-relaxed;
}

.prose ul {
    @apply list-disc list-inside mb-4;
}

.prose ol {
    @apply list-decimal list-inside mb-4;
}

.prose a {
    @apply text-blue-600 hover:text-blue-800 underline;
}
</style>

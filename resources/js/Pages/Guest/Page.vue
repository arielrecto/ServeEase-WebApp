<script setup>
import { Head } from "@inertiajs/vue3";
import Header from "@/Components/Guest/Header.vue";
import Footer from "@/Components/Footer.vue";

const props = defineProps({
    aboutUs: Object,
    contactUs: Object,
    termsAndCondition: Object,
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head
        :title="
            termsAndCondition?.name ||
            aboutUs?.name ||
            contactUs?.name ||
            'Page'
        "
    />

    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
        <!-- Header -->
        <Header :canLogin="canLogin" :canRegister="canRegister" />

        <!-- Hero Section -->
        <div class="relative py-16 sm:py-24">
            <div class="absolute inset-0 overflow-hidden">
                <div
                    class="absolute inset-0 bg-[url(/assets/images/bgc.jpg)] bg-cover bg-center bg-no-repeat opacity-10"
                ></div>
            </div>

            <!-- Main Content -->
            <main class="container relative px-4 mx-auto sm:px-6 lg:px-8">
                <div class="max-w-4xl mx-auto space-y-10">
                    <!-- Terms and Conditions -->
                    <template v-if="termsAndCondition">
                        <div class="space-y-6">
                            <h1
                                class="text-4xl font-bold text-gray-900 sm:text-5xl"
                            >
                                {{ termsAndCondition.name }}
                            </h1>
                            <div class="w-20 h-1 rounded bg-primary"></div>
                            <div
                                class="content-card"
                                v-html="termsAndCondition.content"
                            />
                        </div>
                    </template>

                    <!-- About Us -->
                    <template v-if="aboutUs">
                        <div class="space-y-6">
                            <h1
                                class="text-4xl font-bold text-gray-900 sm:text-5xl"
                            >
                                {{ aboutUs.name }}
                            </h1>
                            <div class="w-20 h-1 rounded bg-primary"></div>
                            <div
                                class="content-card"
                                v-html="aboutUs.content"
                            />
                        </div>
                    </template>

                    <!-- Contact Us -->
                    <template v-if="contactUs">
                        <div class="space-y-6">
                            <h1
                                class="text-4xl font-bold text-gray-900 sm:text-5xl"
                            >
                                {{ contactUs.name }}
                            </h1>
                            <div class="w-20 h-1 rounded bg-primary"></div>
                            <div
                                class="content-card"
                                v-html="contactUs.content"
                            />
                        </div>
                    </template>
                </div>
            </main>
        </div>

        <!-- Footer -->
        <Footer />
    </div>
</template>

<style scoped>
.content-card {
    @apply prose prose-lg max-w-none bg-white rounded-2xl shadow-sm p-8;
}

:deep(.content-card) {
    color: #374151;
}

:deep(.content-card h2) {
    font-size: 1.5rem;
    line-height: 2rem;
    font-weight: 700;
    color: #111827;
    margin-top: 2rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e5e7eb;
}

:deep(.content-card h3) {
    font-size: 1.25rem;
    line-height: 1.75rem;
    font-weight: 600;
    color: #111827;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

:deep(.content-card p) {
    margin-bottom: 1.5rem;
    line-height: 1.75;
}

:deep(.content-card ul) {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
}

:deep(.content-card ol) {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
}

:deep(.content-card li) {
    margin-bottom: 0.5rem;
}

:deep(.content-card a) {
    color: var(--primary-color, #003eff);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}

:deep(.content-card a:hover) {
    color: var(--primary-hover-color, #0035db);
}

:deep(.content-card strong) {
    font-weight: 600;
    color: #111827;
}

:deep(.content-card blockquote) {
    border-left: 4px solid rgba(0, 62, 255, 0.2);
    padding-left: 1rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #4b5563;
}

:deep(.content-card table) {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
}

:deep(.content-card th) {
    background-color: #f9fafb;
    text-align: left;
    padding: 0.75rem;
    font-weight: 600;
    color: #111827;
    border: 1px solid #e5e7eb;
}

:deep(.content-card td) {
    padding: 0.75rem;
    border: 1px solid #e5e7eb;
}

:deep(.content-card hr) {
    margin: 2rem 0;
    border-color: #e5e7eb;
}

:deep(.content-card img) {
    border-radius: 0.5rem;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    margin: 1.5rem 0;
}
</style>

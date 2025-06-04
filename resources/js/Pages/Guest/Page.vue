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

    <div
        class="min-h-screen bg-gradient-to-br from-white via-secondary-50/30 to-primary-50/30"
    >
        <!-- Header -->
        <Header :canLogin="canLogin" :canRegister="canRegister" />

        <!-- Hero Section with enhanced background -->
        <div class="relative py-16 sm:py-24">
            <div class="absolute inset-0 overflow-hidden">
                <div
                    class="absolute inset-0 bg-[url(/assets/images/bgc.jpg)] bg-cover bg-center bg-no-repeat opacity-10"
                ></div>
                <div
                    class="absolute inset-0 bg-gradient-to-br from-primary-500/5 to-secondary-500/5"
                ></div>
            </div>

            <!-- Main Content -->
            <main class="container relative px-4 mx-auto sm:px-6 lg:px-8">
                <div class="max-w-4xl mx-auto space-y-10">
                    <!-- Terms and Conditions -->
                    <template v-if="termsAndCondition">
                        <div class="space-y-8 animate-fade-in">
                            <div
                                class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:space-x-6 sm:space-y-0"
                            >
                                <div class="relative group">
                                    <h1
                                        class="text-4xl font-bold text-gray-900 transition-colors duration-300 sm:text-5xl group-hover:text-primary-600"
                                    >
                                        {{ termsAndCondition.name }}
                                    </h1>
                                    <div
                                        class="absolute left-0 w-1/2 h-1 -bottom-3 bg-gradient-to-r from-primary via-secondary to-primary bg-[length:200%_auto] animate-gradient"
                                    ></div>
                                </div>
                            </div>
                            <div
                                class="p-8 transition-all duration-300 bg-white shadow-sm group rounded-2xl hover:shadow-md hover:shadow-secondary-100/50"
                            >
                                <div class="prose prose-lg max-w-none">
                                    <div
                                        class="prose-headings:text-primary-900 prose-a:text-primary-600 hover:prose-a:text-primary-700 prose-blockquote:border-l-secondary prose-strong:text-primary-900 prose-pre:bg-gray-50"
                                        v-html="termsAndCondition.content"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- About Us -->
                    <template v-if="aboutUs">
                        <div class="space-y-8 animate-fade-in">
                            <div
                                class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:space-x-6 sm:space-y-0"
                            >
                                <div class="relative group">
                                    <h1
                                        class="text-4xl font-bold text-gray-900 transition-colors duration-300 sm:text-5xl group-hover:text-primary-600"
                                    >
                                        {{ aboutUs.name }}
                                    </h1>
                                    <div
                                        class="absolute left-0 w-1/2 h-1 -bottom-3 bg-gradient-to-r from-primary via-secondary to-primary bg-[length:200%_auto] animate-gradient"
                                    ></div>
                                </div>
                            </div>
                            <div
                                class="p-8 transition-all duration-300 bg-white shadow-sm group rounded-2xl hover:shadow-md hover:shadow-secondary-100/50"
                            >
                                <div class="prose prose-lg max-w-none">
                                    <div
                                        class="prose-headings:text-primary-900 prose-a:text-primary-600 hover:prose-a:text-primary-700 prose-blockquote:border-l-secondary prose-strong:text-primary-900 prose-pre:bg-gray-50"
                                        v-html="aboutUs.content"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Contact Us -->
                    <template v-if="contactUs">
                        <div class="space-y-8 animate-fade-in">
                            <div
                                class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:space-x-6 sm:space-y-0"
                            >
                                <div class="relative group">
                                    <h1
                                        class="text-4xl font-bold text-gray-900 transition-colors duration-300 sm:text-5xl group-hover:text-primary-600"
                                    >
                                        {{ contactUs.name }}
                                    </h1>
                                    <div
                                        class="absolute left-0 w-1/2 h-1 -bottom-3 bg-gradient-to-r from-primary via-secondary to-primary bg-[length:200%_auto] animate-gradient"
                                    ></div>
                                </div>
                            </div>
                            <div
                                class="p-8 transition-all duration-300 bg-white shadow-sm group rounded-2xl hover:shadow-md hover:shadow-secondary-100/50"
                            >
                                <div class="prose prose-lg max-w-none">
                                    <div
                                        class="prose-headings:text-primary-900 prose-a:text-primary-600 hover:prose-a:text-primary-700 prose-blockquote:border-l-secondary prose-strong:text-primary-900 prose-pre:bg-gray-50"
                                        v-html="contactUs.content"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </main>
        </div>

        <!-- Footer with enhanced gradient border -->
        <div
            class="border-t border-gradient-to-r from-primary/20 via-secondary/20 to-primary/20"
        >
            <Footer />
        </div>
    </div>
</template>

<style>
/* Base prose styles */
.prose h2 {
    color: theme("colors.primary.900");
    font-size: 1.75rem;
    line-height: 2.25rem;
    font-weight: 700;
    margin-top: 2.5rem;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid theme("colors.secondary.100");
    transition: border-color 0.3s ease;
}

.prose h2:hover {
    border-bottom-color: theme("colors.secondary.300");
}

.prose h3 {
    color: theme("colors.primary.800");
    font-size: 1.35rem;
    line-height: 1.85rem;
    font-weight: 600;
    margin-top: 2rem;
    margin-bottom: 1rem;
    position: relative;
    padding-left: 1rem;
}

.prose h3::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0.25rem;
    bottom: 0.25rem;
    width: 3px;
    background: linear-gradient(
        to bottom,
        theme("colors.primary.400"),
        theme("colors.secondary.400")
    );
    border-radius: 1px;
}

.prose p {
    margin-bottom: 1.5rem;
    line-height: 1.8;
    color: theme("colors.gray.700");
}

.prose ul {
    list-style-type: none;
    padding-left: 1.75rem;
    margin-bottom: 1.5rem;
}

.prose ul li {
    position: relative;
    margin-bottom: 0.75rem;
    transition: transform 0.2s ease;
}

.prose ul li:hover {
    transform: translateX(4px);
}

.prose ul li::before {
    content: "";
    position: absolute;
    left: -1.5rem;
    top: 0.75rem;
    width: 0.5rem;
    height: 0.5rem;
    background: linear-gradient(
        135deg,
        theme("colors.primary.400"),
        theme("colors.secondary.400")
    );
    border-radius: 50%;
    transition: transform 0.2s ease;
}

.prose ul li:hover::before {
    transform: scale(1.2);
}

.prose ol {
    list-style-type: none;
    counter-reset: item;
    padding-left: 1.75rem;
    margin-bottom: 1.5rem;
}

.prose ol li {
    position: relative;
    margin-bottom: 0.75rem;
    counter-increment: item;
}

.prose ol li::before {
    content: counter(item);
    position: absolute;
    left: -2rem;
    top: 0.15rem;
    height: 1.25rem;
    width: 1.25rem;
    background: linear-gradient(
        135deg,
        theme("colors.primary.100"),
        theme("colors.secondary.100")
    );
    border-radius: 50%;
    color: theme("colors.primary.700");
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s ease;
}

.prose ol li:hover::before {
    transform: scale(1.1);
}

.prose a {
    color: theme("colors.primary.600");
    text-decoration: none;
    font-weight: 500;
    position: relative;
    transition: color 0.2s ease;
}

.prose a::after {
    content: "";
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(
        to right,
        theme("colors.primary.300"),
        theme("colors.secondary.300")
    );
    transform: scaleX(0);
    transition: transform 0.3s ease;
    transform-origin: right;
}

.prose a:hover {
    color: theme("colors.primary.700");
}

.prose a:hover::after {
    transform: scaleX(1);
    transform-origin: left;
}

.prose blockquote {
    border-left: 4px solid theme("colors.secondary.400");
    padding: 1.25rem 1.75rem;
    margin: 2rem 0;
    background: linear-gradient(
        to right,
        theme("colors.secondary.50"),
        theme("colors.primary.50/30")
    );
    border-radius: 0 1rem 1rem 0;
    box-shadow: 0 2px 4px theme("colors.secondary.100/50");
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.prose blockquote:hover {
    transform: translateX(4px);
    box-shadow: 0 4px 8px theme("colors.secondary.100/50");
}

.prose blockquote p {
    color: theme("colors.gray.700");
    font-style: italic;
    line-height: 1.7;
}

.prose table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin: 2rem 0;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
        0 2px 4px -2px rgba(0, 0, 0, 0.05);
}

.prose table th {
    background: linear-gradient(
        to right,
        theme("colors.primary.50"),
        theme("colors.primary.100")
    );
    color: theme("colors.primary.900");
    font-weight: 600;
    padding: 1rem;
    text-align: left;
    border: 1px solid theme("colors.primary.200");
}

.prose table td {
    padding: 1rem;
    border: 1px solid theme("colors.gray.200");
    transition: background-color 0.2s ease;
}

.prose table tr:nth-child(even) {
    background-color: theme("colors.secondary.50/50");
}

.prose table tr:hover td {
    background-color: theme("colors.primary.50/50");
}

.prose hr {
    border: none;
    height: 2px;
    background: linear-gradient(
        to right,
        theme("colors.primary.200"),
        theme("colors.secondary.200"),
        theme("colors.primary.200")
    );
    margin: 3rem 0;
    border-radius: 1px;
}

.prose img {
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
        0 2px 4px -2px rgba(0, 0, 0, 0.05);
    margin: 2rem 0;
    border: 1px solid theme("colors.secondary.100");
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.prose img:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 12px -1px rgba(0, 0, 0, 0.1),
        0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

@keyframes gradient {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.animate-gradient {
    animation: gradient 3s linear infinite;
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out forwards;
}
</style>

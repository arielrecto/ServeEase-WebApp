<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Header from "@/Components/Guest/Header.vue";
import Footer from "@/Components/Footer.vue";

defineProps(["service", "services", "canLogin", "canRegister"]);
</script>

<template>
    <Head title="Services" />

    <div
        class="min-h-screen bg-gradient-to-br from-white via-secondary-50/30 to-primary-50/30"
    >
        <Header :canLogin="canLogin" :canRegister="canRegister" />

        <!-- Hero Section -->
        <div class="relative py-16">
            <div class="absolute inset-0 overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-primary-500/5 to-secondary-500/5"
                ></div>
            </div>

            <div class="container relative px-4 mx-auto">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <!-- Main Service Details -->
                    <div class="col-span-2 space-y-6 animate-fade-in">
                        <div
                            class="overflow-hidden transition-all duration-300 bg-white shadow-sm rounded-xl hover:shadow-md hover:shadow-secondary-100/50"
                        >
                            <div class="aspect-video">
                                <img
                                    :src="service.thumbnail"
                                    :alt="service.name"
                                    class="object-cover w-full h-full"
                                />
                            </div>
                            <div class="p-8 space-y-6">
                                <div class="space-y-4">
                                    <h1
                                        class="relative inline-block text-3xl font-bold text-gray-900 group"
                                    >
                                        {{ service.name }}
                                        <div
                                            class="absolute left-0 right-0 h-1 -bottom-2 bg-gradient-to-r from-primary via-secondary to-primary bg-[length:200%_auto] animate-gradient"
                                        ></div>
                                    </h1>
                                </div>
                                <div class="pt-6 border-t border-gray-100">
                                    <h2
                                        class="mb-4 text-xl font-semibold text-primary-900"
                                    >
                                        Description
                                    </h2>
                                    <p class="leading-relaxed text-gray-600">
                                        {{ service.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Other Services Sidebar -->
                    <div class="space-y-6 animate-fade-in">
                        <div class="p-6 bg-white shadow-sm rounded-xl">
                            <h2
                                class="relative inline-block mb-6 text-xl font-semibold text-gray-900"
                            >
                                Similar Services
                                <div
                                    class="absolute left-0 right-0 h-0.5 -bottom-2 bg-secondary-400"
                                ></div>
                            </h2>
                            <div class="space-y-4">
                                <Link
                                    :href="route('guest.show', service.id)"
                                    v-for="(service, i) in services"
                                    :key="service.id"
                                    class="flex gap-4 p-3 transition-all duration-300 rounded-lg hover:bg-gradient-to-r hover:from-primary-50 hover:to-secondary-50 group"
                                >
                                    <div
                                        class="w-24 overflow-hidden rounded-lg aspect-video"
                                    >
                                        <img
                                            :src="service.thumbnail"
                                            :alt="service.name"
                                            class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <h3
                                            class="font-medium text-gray-900 transition-colors duration-300 group-hover:text-primary-600"
                                        >
                                            {{ service.name }}
                                        </h3>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer with gradient border -->
        <div
            class="border-t border-gradient-to-r from-primary/20 via-secondary/20 to-primary/20"
        >
            <Footer />
        </div>
    </div>
</template>

<style>
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

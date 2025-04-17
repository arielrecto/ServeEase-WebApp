<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Header from "@/Components/Guest/Header.vue";
import Footer from "@/Components/Footer.vue";

defineProps(["service", 'services', "canLogin", "canRegister"]);
</script>

<template>

    <Head title="Services" />

    <div class="min-h-screen bg-gray-100">
        <Header :canLogin="canLogin" :canRegister="canRegister" />

        <div class="container px-4 py-8 mx-auto">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <!-- Main Service Details -->
                <div class="col-span-2 space-y-6">
                    <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                        <div class="aspect-video">
                            <img :src="service.service_thumbnail" :alt="service.name"
                                class="object-cover w-full h-full" />
                        </div>
                        <div class="p-6 space-y-4">
                            <h1 class="text-3xl font-bold text-gray-900">
                                {{ service.name }}
                            </h1>
                            <div class="pt-4 border-t">
                                <h2 class="mb-3 text-lg font-semibold">Description</h2>
                                <p class="text-gray-600">
                                    {{ service.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Other Services Sidebar -->
                <div class="space-y-6">
                    <div class="p-6 bg-white rounded-lg shadow-sm">
                        <h2 class="mb-4 text-xl font-semibold">Similar Services</h2>
                        <div class="space-y-4">
                            <Link :href="route('guest.show', service.id)" v-for="(service, i) in services"
                                :key="service.id"
                                class="flex gap-4 p-3 transition rounded-lg bg-gray-50 hover:bg-gray-100">
                            <div class="w-24 overflow-hidden rounded-lg aspect-video">
                                <img :src="service.service_thumbnail" :alt="service.name"
                                    class="object-cover w-full h-full" />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">{{ service.name }}</h3>
                            </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Footer />
    </div>
</template>

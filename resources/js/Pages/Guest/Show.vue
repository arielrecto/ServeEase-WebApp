<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Header from "@/Components/Guest/Header.vue";
import Footer from "@/Components/Footer.vue";

defineProps(["service", 'canLogin', 'canRegister']);
</script>

<template>
    <Head title="Services" />

    <div class="bg-gray-100">
        <Header :canLogin="canLogin" :canRegister="canRegister" />

        <div
            class="container flex items-start min-h-screen py-8 mx-auto space-y-10"
        >
            <main>
                <section class="px-20 space-y-4">
                    <div
                        class="w-full mx-auto overflow-hidden text-center rounded-lg h-96"
                    >
                        <img
                            :src="service.thumbnail"
                            :alt="service.name"
                            class="object-cover"
                        />
                    </div>

                    <h1 class="text-3xl font-bold">
                        {{ service.name }}
                    </h1>

                    <p class="leading-relaxed text-gray-600 whitespace-normal">
                        {{ service.description }}
                    </p>
                </section>
            </main>

            <aside class="w-72">
                <h2 class="mb-5 text-xl font-black">Other Services</h2>

                <div class="space-y-4">
                    <div v-for="n in 3">
                        <div class="w-full h-40 mb-2 bg-gray-500 rounded-lg">
                            <a href="#" class="block w-full h-full"></a>
                        </div>
                        <div>
                            <a href="#" class="text-lg hover:underline"
                                >Service type</a
                            >
                        </div>
                    </div>
                </div>
            </aside>
        </div>
        <Footer />
    </div>
</template>

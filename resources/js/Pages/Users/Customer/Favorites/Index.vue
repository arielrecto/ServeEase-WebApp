<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref, reactive, computed } from "vue";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UserServiceCard from "@/Components/UserServiceCard.vue";
import PaginationLinks from "@/Components/PaginationLinks.vue";

const props = defineProps({
    favorites: Array,
});
</script>

<template>
    <Head title="Favorites" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight ggtext-gray-800">
                Favorites
            </h2>
        </template>

        <div class="py-12">
            <div class="flex flex-col gap-5 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="favorites.data.length > 0">
                    <section class="px-5 md:px-20">
                        <div
                            ref="dataContainer"
                            class="grid grid-cols-1 gap-10 mb-4 overflow-y-auto justify-items-center sm:grid-cols-2 max-h-[70vh]"
                        >
                            <UserServiceCard
                                v-for="service in favorites.data"
                                :key="service.id"
                                :service="service"
                            />
                        </div>
                    </section>
                </div>
                <div
                    v-else
                    class="flex items-center justify-center h-64 mx-auto"
                >
                    <p class="text-xl text-center text-gray-500">
                        Services are coming soon!
                    </p>
                </div>

                <PaginationLinks :links="favorites.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

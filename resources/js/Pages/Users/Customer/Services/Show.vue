<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AddToFavorites from "@/Components/AddToFavorites.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";

const props = defineProps({
    service: Object,
});
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    View Service
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="flex flex-col gap-5 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="flex flex-col gap-5 p-5 bg-white rounded-lg shadow-lg"
                >
                    <div class="flex items-center justify-between">
                        <h1
                            class="w-full py-2 text-4xl font-bold capitalize border-b text-primary"
                        >
                            {{ service.name }}
                        </h1>

                        <AddToFavorites :service="service" />
                    </div>

                    <div class="flex justify-center">
                        <img
                            :src="service.thumbnail"
                            alt=""
                            srcset=""
                            class="object-center w-1/2 aspect-auto"
                        />
                    </div>

                    <div class="flex flex-col gap-2 py-10 border-t">
                        <div class="flex justify-between">
                            <h1 class="text-xl font-bold capitalize">
                                Provider : {{ service.user.name }}
                            </h1>
                            <h1 class="text-xl font-bold capitalize">
                                Rate : ₱ {{ service.price }} /
                                {{ service.price_type }}
                            </h1>
                        </div>

                        <h1>Description</h1>
                        <p class="p-2 min-h-32 bg-gray-50">
                            {{ service.description }}
                        </p>
                        <h1>Terms & Conditions</h1>
                        <p class="p-2 min-h-32 bg-gray-50">
                            {{ service.term_and_condition }}
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <Link
                            :href="
                                route(
                                    'customer.services.avail.create',
                                    service.id
                                )
                            "
                            class="btn btn-primary"
                            >Avail</Link
                        >
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<!-- <template>
    <h1>
        Customer dashboard
    </h1>


    <Link href="/logout" method="POST">Log out</Link>
</template> -->

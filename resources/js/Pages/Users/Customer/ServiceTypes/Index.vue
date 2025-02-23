<script setup>
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineProps({
    serviceTypes: Array,
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Service Types
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="grid grid-cols-1 gap-10 overflow-y-auto sm:grid-cols-3"
                >
                    <article
                        v-for="(serviceType, index) in serviceTypes"
                        :key="serviceType.id"
                        class="overflow-hidden transition bg-white rounded-lg shadow hover:shadow-lg"
                    >
                        <Link :href="route('types.show', serviceType.id)">
                            <img
                                alt=""
                                :src="serviceType.thumbnail"
                                class="object-cover w-full h-56"
                            />
                            <div class="p-4 bg-white sm:p-6">
                                <h3 class="mt-0.5 text-lg text-gray-900">
                                    {{ serviceType.name }}
                                </h3>

                                <p
                                    class="my-2 text-gray-500 line-clamp-3 text-sm/relaxed"
                                >
                                    {{ serviceType.description }}
                                </p>
                            </div>
                        </Link>
                    </article>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import moment from "moment";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AddToFavorites from "@/Components/AddToFavorites.vue";
import Calendar from "@/Components/Calendar.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";

const props = defineProps({
    service: Object,
    availServices: Array,
});

const events = computed(() => {
    if (props.availServices.length === 0) return [];

    return [
        ...props.availServices.map((item) => ({
            title: `${item.name} | status : ${item.status}`,
            start: item.start_date,
            end: moment(item.end_date).add(1, "day").format("YYYY-MM-DD"),
        })),
    ];
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
            <div class="grid grid-cols-1 gap-5 mx-auto max-w-7xl sm:px-6 lg:px-8 md:grid-cols-3">
                <div class="flex flex-col col-span-2 gap-5">
                    <div class="flex flex-col gap-5 p-5 bg-white rounded-lg shadow-lg">
                        <div class="flex items-center justify-between">
                            <h1 class="w-full py-2 text-4xl font-bold capitalize border-b text-primary">
                                {{ service.name }}
                            </h1>

                            <!-- <AddToFavorites :service="service" /> -->
                        </div>

                        <div class="flex justify-center">
                            <img :src="service.service_thumbnail" alt="" srcset=""
                                class="object-center w-1/2 aspect-auto" />
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

                        <div class="flex justify-end gap-2">
                            <Link
                                :href="route('customer.services.bulk-form', { provider_id: service.user.id, query: { service_id: service.id } })"
                                class="btn btn-secondary">
                            Add to Bulk Service
                            </Link>
                            <Link :href="route(
                                'customer.services.avail.create',
                                service.id
                            )
                                " class="btn btn-primary">Avail</Link>
                        </div>
                    </div>
                </div>
                <div class="p-5 bg-white rounded-lg shadow-lg h-max">
                    <h2 class="mb-4">
                        {{ service.user.profile.first_name }}'s upcoming
                        bookings
                    </h2>
                    <Calendar :events="events" />
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

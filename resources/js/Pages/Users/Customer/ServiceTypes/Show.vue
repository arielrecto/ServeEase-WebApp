<script setup>
import { ref, computed, watch } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UserServiceCard from "@/Components/UserServiceCard.vue";
import SearchForm from "@/Components/Form/SearchForm.vue";
import PriceFilter from "@/Components/PriceFilter.vue";
import RatingFilter from "@/Components/RatingFilter.vue";

const props = defineProps({
    serviceType: Object,
    services: Array,
});

const priceRange = ref([0, 100]);
const rating = ref(0);

const filteredServices = computed(() => {
    return props.services;
    // return props.services.filter((service) => {
    //     return (
    //         service.price >= priceRange.value[0] &&
    //         service.price <= priceRange.value[1]
    //     );
    // });
});

console.log(filteredServices.value);

function updatePriceRange(newPriceRange) {
    priceRange.value = newPriceRange;
}

function updateRating(newRating) {
    rating.value = newRating;
}

const searchForm = useForm({
    searchQuery: null,
});

const search = () => {
    searchForm.get(route("types.show", props.serviceType.id));
};

watch(priceRange, () => {
    console.log(priceRange.value);
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ serviceType.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- <div class="flex justify-between mb-4">
                    <PriceFilter @update:price-range="updatePriceRange" />
                    <RatingFilter @update:rating="updateRating" />
                </div> -->

                <div class="flex justify-start w-full mb-12">
                    <SearchForm @submitted="
                        (query) => {
                            searchForm.searchQuery = query;
                            search();
                        }
                    " placeholder="Search service provider" class="max-w-lg mx-0" />
                </div>

                <div class="grid grid-cols-1 gap-10 overflow-y-auto sm:grid-cols-3">
                    <UserServiceCard v-for="service in services" :service="service" />
                    <!-- <article v-for="(service, index) in filteredServices" :key="service.id"
                        class="overflow-hidden transition bg-white rounded-lg shadow hover:shadow-lg">
                        <Link :href="route('customer.services.show', service.id)">
                        <img alt="" :src="service.service_thumbnail" class="object-cover w-full h-56" />
                        <div class="p-4 bg-white sm:p-6">
                            <h3 class="mt-0.5 text-lg text-gray-900">
                                {{ service.name }}
                            </h3>

                            <p class="my-2 text-gray-500 line-clamp-3 text-sm/relaxed">
                                {{ service.description }}
                            </p>

                            <div class="flex justify-between mt-4">
                                <span class="text-gray-600">Price: {{ service.price }}</span>
                                <span class="text-gray-600">Rating: {{ service.rating }}/5</span>
                            </div>
                        </div>
                        </Link>
                    </article> -->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

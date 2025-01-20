<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref, reactive, computed } from "vue";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import GoTo from "@/Components/Dashboard/GoTo.vue";
import UserServiceCard from "@/Components/UserServiceCard.vue";

const page = usePage();

const authUser = ref(page.props.auth.user);
const isVerifiedProvider = computed(() => page.props.auth.isVerifiedProvider);
const isServiceProvider = computed(() => page.props.auth.isServiceProvider);

const menuItems = [
    { id: 1, title: "Favorites", url: "#", icon: "ri-star-line" },
    { id: 2, title: "Service Types", url: "#", icon: "ri-service-line" },
    {
        id: 3,
        title: "My Bookings",
        url: route("customer.booking.index"),
        icon: "ri-book-marked-line",
    },
    {
        id: 4,
        title: "Search",
        url: route("search.index"),
        icon: "ri-menu-search-line",
    },
    {
        id: 5,
        title: "Provider Profile",
        url: route("profile.provider"),
        icon: "ri-user-2-line",
    },
    {
        id: 6,
        title: "Apply as Service Provider",
        url: route("customer.service-provider.create"),
        icon: "ri-contract-line",
    },
];

const services = computed(() => page.props.services);

console.log(services.value);
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight ggtext-gray-800">
                Home
            </h2>
        </template>

        <div class="py-12">
            <div class="flex flex-col gap-5 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="flex flex-wrap items-start justify-center gap-x-8 gap-y-10"
                >
                    <template v-for="item in menuItems">
                        <GoTo
                            v-if="item.id < 5"
                            :title="item.title"
                            :icon="item.icon"
                            :url="item.url"
                        />
                        <GoTo
                            v-if="
                                item.id === 5 &&
                                isServiceProvider &&
                                isVerifiedProvider
                            "
                            :title="item.title"
                            :icon="item.icon"
                            :url="item.url"
                        />
                        <GoTo
                            v-if="item.id === 6 && !isServiceProvider"
                            :title="item.title"
                            :icon="item.icon"
                            :url="item.url"
                        />
                    </template>
                </div>
                <div v-if="services?.length > 0">
                    <section class="px-5 md:px-20">
                        <div
                            ref="dataContainer"
                            class="grid grid-cols-1 gap-10 mb-4 overflow-y-auto justify-items-center sm:grid-cols-2 max-h-[70vh]"
                        >
                            <UserServiceCard
                                v-for="service in services"
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
            </div>
        </div>
    </AuthenticatedLayout>
</template>

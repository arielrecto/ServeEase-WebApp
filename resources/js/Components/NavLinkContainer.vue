<script setup>
import NavLink from "@/Components/NavLink.vue";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const isServiceProvider = usePage().props?.auth?.isServiceProvider;
const isVerifiedProvider = usePage().props?.auth?.isVerifiedProvider;
const roleName = usePage().props.auth.roleName;

const isAdmin = computed(() => roleName.some((role) => role == "admin"));
</script>

<template>
    <div class="hidden space-x-8 md:-my-px sm:ms-10 md:flex">
        <template v-if="isAdmin">
            <NavLink
                :href="route('admin.dashboard')"
                :active="route().current('admin.dashboard')"
            >
                Dashboard
            </NavLink>
            <NavLink
                :href="route('admin.service-types.index')"
                :active="route().current('admin.service-types.index')"
            >
                Services
            </NavLink>
            <NavLink
                :href="route('admin.barangays.index')"
                :active="route().current('admin.barangays.index')"
            >
                Barangays
            </NavLink>
            <NavLink
                :href="route('admin.service-provider.index')"
                :active="route().current('admin.service-provider.index')"
            >
                Service Providers
            </NavLink>
            <NavLink
                :href="route('admin.feedbacks.index')"
                :active="route().current('admin.feedbacks.index')"
            >
                Reviews
            </NavLink>
            <NavLink
                :href="route('admin.cms.index')"
                :active="route().current('admin.cms.index')"
            >
                Pages
            </NavLink>
            <NavLink
                :href="route('admin.users.index')"
                :active="route().current('admin.users.index')"
            >
                Users
            </NavLink>
            <NavLink
                :href="route('admin.reports.index')"
                :active="route().current('customer.report.index')"
            >
                Report
            </NavLink>
        </template>

        <template v-else>
            <NavLink
                :href="route('customer.dashboard')"
                :active="route().current('customer.dashboard')"
            >
                Home
            </NavLink>
            <NavLink
                v-if="isVerifiedProvider"
                :href="route('service-provider.dashboard')"
                :active="route().current('service-provider.dashboard')"
            >
                Dashboard
            </NavLink>
            <NavLink
                :href="route('customer.booking.index')"
                :active="route().current('customer.booking.index')"
            >
                My Bookings
            </NavLink>
            <!-- <NavLink
                :href="route('customer.favorites.index')"
                :active="route().current('customer.favorites.index')"
            >
                Favorites
            </NavLink> -->
            <NavLink :href="route('types.index')" :active="route().current('types.index')">
                Service Types
            </NavLink>
            <NavLink
                :href="route('messages.index')"
                :active="route().current('messages.*')"
            >
                Messages
            </NavLink>
            <NavLink
                :href="route('customer.report.index')"
                :active="route().current('customer.report.index')"
            >
                Report
            </NavLink>
        </template>
    </div>
</template>

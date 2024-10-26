<script setup>
import NavLink from '@/Components/NavLink.vue';
import {usePage} from '@inertiajs/vue3';

const isServiceProvider = usePage().props.auth.isServiceProvider;
const roleName = usePage().props.auth.roleName;
</script>

<template>
    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
        <template v-if="roleName === 'admin'">
            <NavLink :href="route('admin.dashboard')"
            :active="route().current('admin.dashboard')">
                Dashboard
            </NavLink>
            <NavLink :href="route('admin.service-types.index')"
            :active="route().current('admin.service-types.index')">
                Services
            </NavLink>
            <NavLink :href="route('admin.service-provider.index')"
            :active="route().current('admin.service-provider.index')">
                Applications
            </NavLink>
        </template>

        <template v-else>
            <NavLink :href="route('customer.dashboard')"
                :active="route().current('customer.dashboard')">
                Home
            </NavLink>
            <NavLink v-if="isServiceProvider" :href="route('service-provider.dashboard')"
                :active="route().current('service-provider.dashboard')">
                Dashboard
            </NavLink>
            <NavLink :href="route('customer.dashboard')"
                :active="route().current('customer.dashboard')">
                Explore
            </NavLink>
        </template>
    </div>
</template>

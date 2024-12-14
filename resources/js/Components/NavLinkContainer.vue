<script setup>
import NavLink from '@/Components/NavLink.vue';
import { usePage } from '@inertiajs/vue3';
import { computed} from 'vue';

const isServiceProvider = usePage().props?.auth?.isServiceProvider;
const roleName = usePage().props.auth.roleName;

const isAdmin = computed(() => roleName.some((role) => role == 'admin'))


</script>

<template>
    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
        <template v-if="isAdmin">
            <NavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')">
                Dashboard
            </NavLink>
            <NavLink :href="route('admin.service-types.index')" :active="route().current('admin.service-types.index')">
                Services
            </NavLink>
            <NavLink :href="route('admin.barangays.index')" :active="route().current('admin.barangays.index')">
                Barangays
            </NavLink>
            <NavLink :href="route('admin.service-provider.index')"
                :active="route().current('admin.service-provider.index')">
                Applications
            </NavLink>
            <NavLink :href="route('admin.users.index')" :active="route().current('admin.users.index')">
                Users
            </NavLink>
        </template>

        <template v-else>
            <NavLink :href="route('customer.dashboard')" :active="route().current('customer.dashboard')">
                Home
            </NavLink>
            <NavLink v-if="isServiceProvider" :href="route('service-provider.dashboard')"
                :active="route().current('service-provider.dashboard')">
                Dashboard
            </NavLink>
            <NavLink :href="route('search.index')" :active="route().current('search.index')">
                Search
            </NavLink>
        </template>
    </div>
</template>

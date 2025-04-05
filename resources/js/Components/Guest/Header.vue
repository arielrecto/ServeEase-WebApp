<script setup>
import { onMounted, ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import NavLink from "../NavLink.vue";

import ApplicationLogo from "@/Components/ApplicationLogo.vue";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const pages = ref([]);
</script>

<template>
    <header class="sticky top-0 left-0 z-20">
        <nav class="bg-white border-b border-gray-100">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center shrink-0">
                            <Link href="/">
                            <ApplicationLogo />
                            </Link>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('guest.services')" :active="route().current('guest.services')">
                                    Services
                                </NavLink>
                                <NavLink :href="route('guest.search')" :active="route().current('guest.search')">
                                    Search
                                </NavLink>
                                <template v-for="page in $page.props.auth.pages" :key="page.id">
                                    <NavLink :href="route('guest.page.show', page.slug)"
                                        :active="$page.url === `/page/${page.slug}`">
                                        {{ page.name }}
                                    </NavLink>
                                </template>
                            </div>
                        </div>
                        <div v-if="canLogin" class="flex items-center gap-x-8">
                            <!-- <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                            >Dashboard</Link
                        > -->
                            <Link v-if="$page.props.auth.user" :href="route('home')">
                            Go to Home
                            </Link>

                            <template v-else>
                                <Link :href="route('login')" class="button-primary">
                                Log in
                                </Link>
                                <Link v-if="canRegister" :href="route('register')" class="button-ghost"
                                    :active="route().current('register')">
                                Register
                                </Link>

                                <!-- <Link
                                :href="route('login')"
                                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                                >Log in</Link
                            > -->

                                <!-- <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="font-semibold text-gray-600 ms-4 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                                >Register</Link
                            > -->
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
</template>

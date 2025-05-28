    <script setup>
import { onMounted, ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import NavLink from "../NavLink.vue";
import ResponsiveNavLink from "../ResponsiveNavLink.vue";

import ApplicationLogo from "@/Components/ApplicationLogo.vue";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const pages = ref([]);
const showingNavigationDropdown = ref(false);
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

                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 md:flex">
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
                        <div v-if="canLogin" class="hidden md:flex md:items-center gap-x-2 md:gap-x-8">
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
                        <div class="flex items-center md:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500"
                            >
                                <svg
                                    class="w-6 h-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div
                :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }"
                class="z-50 md:hidden"
            >
                <div class="pt-2 pb-3 space-y-1">
                    <ResponsiveNavLink :href="route('guest.services')" :active="route().current('guest.services')">
                        Services
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('guest.search')" :active="route().current('guest.search')">
                        Search
                    </ResponsiveNavLink>
                    <template v-for="page in $page.props.auth.pages" :key="page.id">
                        <ResponsiveNavLink :href="route('guest.page.show', page.slug)"
                            :active="$page.url === `/page/${page.slug}`">
                            {{ page.name }}
                        </ResponsiveNavLink>
                    </template>

                    <ResponsiveNavLink v-if="$page.props.auth.user" :href="route('home')">
                        Go to Home
                    </ResponsiveNavLink>

                    <template v-else>
                        <ResponsiveNavLink :href="route('login')">
                        Log in
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="canRegister" :href="route('register')"
                            :active="route().current('register')">
                        Register
                        </ResponsiveNavLink>
                    </template>
                </div>
            </div>
        </nav>
    </header>
</template>

<script setup>
import { ref } from "vue";
import { ModalRoot } from "@inertiaui/modal-vue";
import { Link, usePage } from "@inertiajs/vue3";
import moment from "moment";

import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import NavLinkContainer from "@/Components/NavLinkContainer.vue";
import FlashMessage from "@/Components/FlashMessage.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import Footer from "@/Components/Footer.vue";
import Loader from "@/Components/Loader.vue";

const showingNavigationDropdown = ref(false);
const isServiceProvider = usePage().props.auth.isServiceProvider;
const roleName = usePage().props.auth.roleName;
const profile = usePage().props.auth.user.profile;
const finishedBookings = usePage().props.auth.finishedBookings;
</script>

<template>
    <!-- Flash messages -->
    <FlashMessage
        v-if="$page.props.flash.message_success"
        type="success"
        :message="$page.props.flash.message_success"
    />
    <FlashMessage
        v-if="$page.props.flash.message_error"
        type="error"
        :message="$page.props.flash.message_error"
    />
    <FlashMessage
        v-if="$page.props.flash.message_warning"
        type="warning"
        :message="$page.props.flash.message_warning"
    />

    <!-- Loading spinner -->
    <Loader />

    <div>
        <div class="min-h-screen bg-gray-100">
            <nav
                class="sticky top-0 left-0 z-20 bg-white border-b border-gray-100"
            >
                <!-- Primary Navigation Menu -->
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex items-center shrink-0">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <!-- <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink v-if="roleName === 'admin'" :href="route('admin.dashboard')"
                                    :active="route().current('admin.dashboard')">
                                    Dashboard
                                </NavLink>
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
                            </div> -->
                            <NavLinkContainer />
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Notifications popup -->
                            <div
                                v-if="!$page.props.auth.isAdmin"
                                class="relative ms-3"
                            >
                                <Dropdown align="right" width="80">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center justify-center w-10 h-10 text-lg font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-gray-400 rounded-full hover:text-gray-700 focus:outline-none hover:bg-gray-100"
                                            >
                                                <i
                                                    class="ri-notification-2-line"
                                                ></i>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            v-for="finishedBooking in finishedBookings"
                                            :href="
                                                route(
                                                    'customer.booking.detail',
                                                    finishedBooking.id
                                                )
                                            "
                                        >
                                            <div class="space-y-1">
                                                <p>
                                                    The service that you booked
                                                    with
                                                    <span class="font-bold">{{
                                                        finishedBooking.service
                                                            .user.name
                                                    }}</span>
                                                    has been finished. Write a
                                                    review.
                                                </p>
                                                <div class="text-xs">
                                                    {{
                                                        moment(
                                                            finishedBooking.created_at
                                                        ).fromNow()
                                                    }}
                                                </div>
                                            </div>
                                        </DropdownLink>
                                        <div
                                            v-if="finishedBookings.length === 0"
                                            class="py-5 text-sm text-center"
                                        >
                                            There are no notifications.
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="flex items-center -me-2 sm:hidden">
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

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"
                                >Profile</ResponsiveNavLink
                            >
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div
                    class="flex items-center justify-between px-4 py-6 mx-auto gap-x-4 max-w-7xl sm:px-6 lg:px-8"
                >
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="text-gray-900">
                <slot />
            </main>
        </div>
    </div>

    <Footer />

    <ModalRoot />
</template>

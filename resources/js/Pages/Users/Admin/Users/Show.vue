<script setup>
import { reactive } from "vue";
import moment from "moment";
import { Head, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";
import TableWrapper from "@/Components/Table/TableWrapper.vue";
import TableHeader from "@/Components/Table/TableHeader.vue";
import ActionButton from "@/Components/ActionButton.vue";
import StatusBadge from "@/Components/StatusBadge.vue";

import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";

const props = defineProps({
    user: Object,
    availServices: Array
});

const state = reactive({
    tabs: [
        { name: "Profile", value: "0" },
        { name: "Bookings", value: "1" },
        // { name: "Activity", value: "2" },
    ],
});

// Mock data for demonstration
const mockUser = {
    id: 1,
    name: "John Doe",
    email: "john@example.com",
    profile: {
        first_name: "John",
        last_name: "Doe",
        contact: "123-456-7890",
        address: "123 Main St, City",
        user_avatar: "https://via.placeholder.com/150",
        created_at: "2024-01-01",
    },
    roles: ["customer"],
    is_active: true
};

const mockBookings = [
    {
        id: 1,
        service_name: "Plumbing Service",
        provider_name: "Service Provider 1",
        status: "completed",
        price: 2500,
        date: "2024-04-15"
    },
    {
        id: 2,
        service_name: "Electrical Service",
        provider_name: "Service Provider 2",
        status: "pending",
        price: 1800,
        date: "2024-04-17"
    }
];

const bookingHeaders = ["Service", "Provider", "Status", "Price", "Date"];

</script>

<template>

    <Head title="View User" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton :url="route('admin.users.index')" />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    User Details
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                    <Tabs value="0">
                        <TabList>
                            <Tab v-for="tab in state.tabs" :key="tab.value" :value="tab.value">
                                {{ tab.name }}
                            </Tab>
                        </TabList>

                        <TabPanels>
                            <!-- Profile Information -->
                            <TabPanel value="0">
                                <div class="p-6">
                                    <div class="flex items-start space-x-6">
                                        <div class="flex-shrink-0">
                                            <img :src="user.profile.user_avatar" alt="Profile photo"
                                                class="object-cover w-32 h-32 rounded-full" />
                                        </div>
                                        <div class="flex-1">
                                            <div class="grid grid-cols-2 gap-6">
                                                <div>
                                                    <h3 class="text-lg font-medium text-gray-900">
                                                        Basic Information
                                                    </h3>
                                                    <dl class="mt-4 space-y-4">
                                                        <div>
                                                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                                            <dd class="mt-1 text-sm text-gray-900">
                                                                {{ user.profile.full_name }}
                                                            </dd>
                                                        </div>
                                                        <div>
                                                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                                                            <dd class="mt-1 text-sm text-gray-900">{{ user.email }}
                                                            </dd>
                                                        </div>
                                                        <!-- <div>
                                                            <dt class="text-sm font-medium text-gray-500">Contact</dt>
                                                            <dd class="mt-1 text-sm text-gray-900">
                                                                {{ mockUser.profile.contact }}
                                                            </dd>
                                                        </div> -->
                                                    </dl>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-medium text-gray-900">
                                                        Additional Details
                                                    </h3>
                                                    <dl class="mt-4 space-y-4">
                                                        <div>
                                                            <dt class="text-sm font-medium text-gray-500">Role</dt>
                                                            <dd class="mt-1 text-sm text-gray-900">
                                                                {{user.roles.map((role) => role.name).join(', ')}}
                                                            </dd>
                                                        </div>
                                                        <div>
                                                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                                                            <dd class="mt-1 text-sm text-gray-900">
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full"
                                                                    :class="mockUser.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                                                    {{ user.user_status }}
                                                                </span>
                                                            </dd>
                                                        </div>
                                                        <div>
                                                            <dt class="text-sm font-medium text-gray-500">Join Date</dt>
                                                            <dd class="mt-1 text-sm text-gray-900">
                                                                {{ moment(user.created_at).format('LL') }}
                                                            </dd>
                                                        </div>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </TabPanel>

                            <!-- Bookings History -->
                            <TabPanel value="1">
                                <div class="p-6">
                                    <TableWrapper>
                                        <template #header>
                                            <TableHeader :headers="bookingHeaders" />
                                        </template>

                                        <template #body>
                                            <template v-if="availServices.length">
                                                <tr v-for="booking in availServices" :key="booking.id"
                                                    class="bg-white border-b last:border-b-0">
                                                    <td class="py-4">{{ booking.service.name }}</td>
                                                    <td>{{ booking.service.user.profile.full_name }}</td>
                                                    <td>
                                                        <StatusBadge :status="booking.status" />
                                                    </td>
                                                    <td>₱{{ booking.total_price.toLocaleString() }}</td>
                                                    <td>{{ moment(booking.created_at).format('LL') }}</td>
                                                    <!-- <td>
                                                        <div class="flex gap-x-4">
                                                            <ActionButton type="link" actionType="view" href="#" />
                                                        </div>
                                                    </td> -->
                                                </tr>
                                            </template>
                                            <template v-else>
                                                <tr>
                                                    <td :colspan="bookingHeaders.length">
                                                        <p class="italic text-center">
                                                            No bookings found.
                                                        </p>
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                    </TableWrapper>
                                </div>
                            </TabPanel>

                            <!-- Activity Log -->
                            <!-- <TabPanel value="2">
                                <div class="p-6">
                                    <p class="text-center text-gray-500">
                                        Activity log will be implemented soon.
                                    </p>
                                </div>
                            </TabPanel> -->
                        </TabPanels>
                    </Tabs>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

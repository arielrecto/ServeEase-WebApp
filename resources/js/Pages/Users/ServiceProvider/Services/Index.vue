<script setup>
import { ModalLink } from "@inertiaui/modal-vue";
import { Link, useForm } from "@inertiajs/vue3";
import moment from "moment";
import { ref } from "vue";
import axios from "axios";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TableWrapper from "@/Components/Table/TableWrapper.vue";
import TableHeader from "@/Components/Table/TableHeader.vue";
import TableBody from "@/Components/Table/TableBody.vue";
import ActionButton from "@/Components/ActionButton.vue";
import PaginationLinks from "@/Components/PaginationLinks.vue";
import SearchForm from "@/Components/Form/SearchForm.vue";
import StatusBadge from "@/Components/StatusBadge.vue";
import ModalLinkDialog from "@/Components/Modal/ModalLinkDialog.vue";

defineProps(["services"]);

const headers = ref([
    "Name",
    "Reference #",
    "Weekly Revenue",
    "Status",
    "Created At",
    "Action",
]);
</script>

<template>
    <Head title="Services" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                My Services
            </h2>
            <div>
                <Link
                    :href="route('service-provider.services.create')"
                    class="button-primary"
                    >Add service</Link
                >
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <TableWrapper>
                            <template #header>
                                <TableHeader :headers="headers" />
                            </template>

                            <template #body>
                                <template v-if="services.length !== 0">
                                    <tr
                                        v-for="service in services.data"
                                        :key="service.id"
                                    >
                                        <th>{{ service.name }}</th>
                                        <td class="font-medium">
                                            <Link
                                                v-if="service.service_cart_id"
                                                :href="
                                                    route(
                                                        'service-provider.booking.cart.show',
                                                        service.service_cart_id
                                                    )
                                                "
                                                class="text-primary hover:underline"
                                            >
                                                {{
                                                    service.reference_number ||
                                                    "---"
                                                }}
                                            </Link>
                                            <span v-else>---</span>
                                        </td>
                                        <td class="font-medium">
                                            ₱{{ service.weekly_revenue }}
                                        </td>
                                        <td>
                                            <span
                                                v-if="service.archived_at"
                                                class="px-2 py-1 text-sm text-red-700 bg-red-100 rounded-full"
                                            >
                                                Archived
                                            </span>
                                            <span
                                                v-else
                                                class="px-2 py-1 text-sm text-green-700 bg-green-100 rounded-full"
                                            >
                                                Active
                                            </span>
                                        </td>
                                        <td>
                                            {{
                                                moment(
                                                    service.created_at
                                                ).format("ll")
                                            }}
                                        </td>
                                        <td>
                                            <div class="flex gap-x-4">
                                                <ActionButton
                                                    type="link"
                                                    actionType="view"
                                                    :href="
                                                        route(
                                                            'service-provider.services.show',
                                                            service.id
                                                        )
                                                    "
                                                />
                                                <ModalLinkDialog
                                                    v-if="!service.archived_at"
                                                    :href="
                                                        route(
                                                            'service-provider.services.archive',
                                                            service.id
                                                        )
                                                    "
                                                    class="flex items-center text-red-600 hover:text-red-800"
                                                >
                                                    <i
                                                        class="mr-1 ri-archive-line"
                                                    ></i>
                                                    Archive
                                                </ModalLinkDialog>
                                                <ModalLinkDialog
                                                    v-else
                                                    :href="
                                                        route(
                                                            'service-provider.services.restore',
                                                            service.id
                                                        )
                                                    "
                                                    class="flex items-center text-green-600 hover:text-green-800"
                                                >
                                                    <i
                                                        class="mr-1 ri-arrow-go-back-line"
                                                    ></i>
                                                    Restore
                                                </ModalLinkDialog>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td :colspan="headers.length">
                                            <p class="italic text-center">
                                                No records found.
                                            </p>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </TableWrapper>
                    </div>

                    <PaginationLinks :links="services.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

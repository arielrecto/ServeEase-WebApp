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

defineProps(['services']);

const headers = ref(["Name", "Date Joined", "Action"]);


const search = () => {
    searchForm.get(route("admin.users.index"));
};
</script>

<template>

    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard - Service Provider
            </h2>
            <div>
                <Link :href="route('service-provider.services.create')" class="button-primary">Add service</Link>
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
                                    <tr v-for="service in services.data">
                                        <th>{{ service.name }}</th>
                                        <td>
                                            {{
                                                moment(service.created_at).format(
                                                    "ll"
                                                )
                                            }}
                                        </td>
                                        <td>
                                            <div class="flex gap-x-4">
                                                <ActionButton
                                                    type="link"
                                                    actionType="view"
                                                    href="#"
                                                />
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

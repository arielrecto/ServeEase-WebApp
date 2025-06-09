<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TableWrapper from "@/Components/Table/TableWrapper.vue";
import TableHeader from "@/Components/Table/TableHeader.vue";
import TableBody from "@/Components/Table/TableBody.vue";
import ActionButton from "@/Components/ActionButton.vue";
import PaginationLinks from "@/Components/PaginationLinks.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import moment from "moment";

defineProps({
    providers: Object,
});

const headers = ref([
    "Name",
    "Service",
    "Years of Experience",
    "Date Submitted",
    "Action",
]);
</script>

<template>
    <Head title="Rejected Applications" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Rejected Applications
            </h2>
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
                                <template v-if="providers.data.length !== 0">
                                    <tr v-for="provider in providers.data">
                                        <td>
                                            {{ provider.name }}
                                        </td>
                                        <td>
                                            {{ provider.service_type }}
                                        </td>
                                        <td>{{ provider.experience }}</td>
                                        <td>
                                            {{
                                                moment(
                                                    provider.created_at
                                                ).format(
                                                    "MMMM Do YYYY, h:mm:ss a"
                                                )
                                            }}
                                        </td>
                                        <td>
                                            <div class="flex gap-x-4">
                                                <ActionButton
                                                    type="modal"
                                                    actionType="view"
                                                    modalWidth="lg"
                                                    :href="
                                                        route(
                                                            'admin.applications.show',
                                                            provider.id
                                                        )
                                                    "
                                                    :modalSlideoverEnabled="
                                                        true
                                                    "
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

                    <PaginationLinks :links="providers.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

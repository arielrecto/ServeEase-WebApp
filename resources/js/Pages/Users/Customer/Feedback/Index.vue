<script setup>
import { ModalLink } from "@inertiaui/modal-vue";
import { Link, useForm } from "@inertiajs/vue3";
import moment from "moment";
import { ref } from "vue";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TableWrapper from "@/Components/Table/TableWrapper.vue";
import TableHeader from "@/Components/Table/TableHeader.vue";
import ActionButton from "@/Components/ActionButton.vue";
import PaginationLinks from "@/Components/PaginationLinks.vue";
import SearchForm from "@/Components/Form/SearchForm.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";

defineProps(["feedbacks"]);

const headers = ref([
    "Service",
    "Provider",
    "Rating",
    "Date Submitted",
    "Actions",
]);

const searchForm = useForm({
    searchQuery: null,
});

const search = () => {
    searchForm.get(route("admin.users.index"));
};
</script>

<template>
    <Head title="Reviews" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton :url="route('customer.dashboard')" />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Reviews
                </h2>
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
                                <template v-if="feedbacks.data.length !== 0">
                                    <tr v-for="feedback in feedbacks.data">
                                        <th>{{ feedback.service }}</th>
                                        <td>{{ feedback.provider }}</td>
                                        <td>{{ feedback.rate }}</td>
                                        <td>
                                            {{
                                                moment(
                                                    feedback.created_at
                                                ).format("ll")
                                            }}
                                        </td>
                                        <td>
                                            <div class="flex gap-x-4">
                                                <!-- <ActionButton
                                                    type="link"
                                                    actionType="view"
                                                    href="#"
                                                /> -->

                                                <!-- <ActionButton
                                                    type="link"
                                                    actionType="edit"
                                                    :href="
                                                        route(
                                                            'customer.feedbacks.edit',
                                                            feedback.id
                                                        )
                                                    "
                                                /> -->

                                                <ActionButton
                                                    type="modal"
                                                    actionType="delete"
                                                    :href="
                                                        route(
                                                            'customer.feedbacks.delete',
                                                            feedback.id
                                                        )
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

                    <PaginationLinks :links="feedbacks.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

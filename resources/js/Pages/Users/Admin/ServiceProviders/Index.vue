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

defineProps({
    providers: Object,
});

const headers = ref([
    "Name",
    "Service",
    "Years of Experience",
    "Date Joined",
    "Action",
]);

const searchForm = useForm({
    searchQuery: null,
});

const search = () => {
    searchForm.get(route("admin.service-provider.index"));
};

const ratingOptions = [5, 4, 3, 2, 1];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Service Providers
            </h2>
            <div class="flex items-center gap-x-4">
                <SearchForm
                    @submitted="
                        (query) => {
                            searchForm.searchQuery = query;
                            search();
                        }
                    "
                    placeholder="Search service provider"
                />
                <div>
                    <Link
                        :href="route('admin.applications.index')"
                        class="w-max button-ghost"
                        >Go to applications</Link
                    >
                </div>
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
                                <template v-if="providers.data.length !== 0">
                                    <tr v-for="provider in providers.data">
                                        <td>
                                            {{ provider.name }}
                                        </td>
                                        <td>{{ provider.service }}</td>
                                        <td>{{ provider.experience }}</td>
                                        <td>
                                            {{
                                                moment(
                                                    provider.created_at
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
                                                            'admin.service-provider.show',
                                                            provider.user.id
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

                    <PaginationLinks :links="providers.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

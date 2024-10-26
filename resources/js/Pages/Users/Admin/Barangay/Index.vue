<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TableWrapper from '@/Components/Table/TableWrapper.vue';
import TableHeader from '@/Components/Table/TableHeader.vue';
import TableBody from '@/Components/Table/TableBody.vue';
import ActionButton from '@/Components/ActionButton.vue';
import { ModalLink } from '@inertiaui/modal-vue';
import { Link } from '@inertiajs/vue3';
import moment from 'moment';
import { ref } from 'vue';

defineProps({
    brgys: Object,
});

const headers = ref([
    'Name',
    'Date Created',
    'Action',
]);
</script>

<template>

    <Head title="Barangays" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Barangays</h2>
            <div>
                <Link :href="route('admin.barangays.create')" class="button-primary">Add brgy.</Link>
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
                                <template v-if="brgys.length !== 0">
                                    <tr v-for="brgy in brgys">
                                        <th>{{ brgy.name }}</th>
                                        <td>{{ moment(brgy.created_at).format('ll') }}</td>
                                        <td>
                                            <div class="flex gap-x-4">
                                                <ActionButton type="link" actionType="edit" :href="route('admin.barangays.edit', brgy.id)" :modalSlideoverEnabled="true" />
                                                <ActionButton type="modal" actionType="delete" :href="route('admin.barangays.delete', brgy.id)"/>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td :colspan="headers.length"><p class="italic text-center">No records found.</p></td>
                                    </tr>
                                </template>
                            </template>
                        </TableWrapper>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


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
    providers: Object,
});

const headers = ref([
    'Name',
    'Service',
    'Years of Experience',
    'Date Submitted',
    'Action',
]);

const items = ref([
    {
        id: 1,
        name: 'David Rondina',
        service_type: 'Plumbing',
        experience: '2 years',
        status: 'Pending',
        created_at: 'Oct. 10, 2024',
    },
    {
        id: 2,
        name: 'John Doe',
        service_type: 'Plumbing',
        experience: '2 years',
        status: 'Pending',
        created_at: 'Oct. 10, 2024',
    },
    {
        id: 3,
        name: 'Laufey Lin',
        service_type: 'Plumbing',
        experience: '4 years',
        status: 'Pending',
        created_at: 'Oct. 10, 2024',
    },
])
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Applications</h2>
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
                                <!-- <TableBody :items="providers.data" keyName="id">
                                    <td>
                                        <div class="flex gap-x-4">
                                            <ActionButton type="modal" actionType="view" :href="route('admin.service-provider.show', 1)" modalSlideoverEnabled />
                                            <ActionButton type="modal" actionType="delete" :href="route('admin.service-provider.delete', 1)"/>
                                        </div>
                                    </td>
                                </TableBody> -->
                                <template v-if="providers.data.length !== 0">
                                    <tr v-for="provider in providers.data">
                                        <td>{{ provider.profile.user.name }}</td>
                                        <td>{{ provider.service_type }}</td>
                                        <td>{{ provider.experience }}</td>
                                        <td>{{ moment(provider.created_at).format('ll') }}</td>
                                        <td>
                                            <div class="flex gap-x-4">
                                                <ActionButton type="modal" actionType="view" :href="route('admin.service-provider.show', provider.id)" :modalSlideoverEnabled="true" />
                                                <ActionButton type="modal" actionType="delete" :href="route('admin.service-provider.delete', provider.id)"/>
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


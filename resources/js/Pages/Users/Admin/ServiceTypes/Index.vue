<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TableWrapper from '@/Components/Table/TableWrapper.vue';
import TableHeader from '@/Components/Table/TableHeader.vue';
import TableBody from '@/Components/Table/TableBody.vue';
import ActionButton from '@/Components/ActionButton.vue';
import ModalLinkSlideover from '@/Components/Modal/ModalLinkSlideover.vue';
import { ModalLink } from '@inertiaui/modal-vue';
import { Link } from '@inertiajs/vue3';
import moment from 'moment';
import { ref } from 'vue';

defineProps({
    serviceTypes: Array,
});

const headers = ref([
    'Name',
    'Service',
    'Years of Experience',
    'Date Submitted',
    'Action',
]);

// const items = ref([
//     {
//         id: 1,
//         name: 'David Rondina',
//         service_type: 'Plumbing',
//         experience: '2 years',
//         status: 'Pending',
//         created_at: 'Oct. 10, 2024',
//     },
//     {
//         id: 2,
//         name: 'John Doe',
//         service_type: 'Plumbing',
//         experience: '2 years',
//         status: 'Pending',
//         created_at: 'Oct. 10, 2024',
//     },
//     {
//         id: 3,
//         name: 'Laufey Lin',
//         service_type: 'Plumbing',
//         experience: '4 years',
//         status: 'Pending',
//         created_at: 'Oct. 10, 2024',
//     },
// ])
</script>

<template>

    <Head title="Services" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Services</h2>

            <div>
                <Link :href="route('admin.service-types.create')" class="button-primary">Add service</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-10 overflow-y-auto sm:grid-cols-3">
                    <article v-for="(service, index) in serviceTypes" :key="service.id" class="overflow-hidden transition bg-white rounded-lg shadow hover:shadow-lg">
                        <ModalLinkSlideover :href="route('admin.service-types.show', service.id)">
                            <img
                                alt=""
                                :src="service.thumbnail"
                                class="object-cover w-full h-56"
                            />
                            <div class="p-4 bg-white sm:p-6">
                                <h3 class="mt-0.5 text-lg text-gray-900">{{ service.name }}</h3>

                                <p class="my-2 text-gray-500 line-clamp-3 text-sm/relaxed">
                                    {{ service.description }}
                                </p>
                            </div>
                        </ModalLinkSlideover>
                    </article>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


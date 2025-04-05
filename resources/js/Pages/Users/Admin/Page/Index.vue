<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TableWrapper from "@/Components/Table/TableWrapper.vue";
import TableHeader from "@/Components/Table/TableHeader.vue";
import ActionButton from "@/Components/ActionButton.vue";

// Replace the hardcoded pages array with props
const props = defineProps({
    pages: {
        type: Array,
        default: () => []
    }
});

const headers = [
    "Title",
    "Slug",
    "Actions",
];
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Pages
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
                                <template v-if="pages.data.length !== 0">
                                    <tr v-for="page in pages.data" :key="page.id">
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                            {{ page.name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                            {{ page.slug }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                            <ActionButton type="link" actionType="edit"
                                                :href="route('admin.cms.edit', page.slug)" />
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-sm text-center text-gray-500">
                                            No pages found.
                                        </td>
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

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import moment from 'moment';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    transactions: Object
});

const showRejectModal = ref(false);
const selectedTransaction = ref(null);

const form = useForm({
    status: '',
    remarks: ''
});

const formatCurrency = (amount, currency = 'PHP') => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: currency
    }).format(amount);
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-700',
        approved: 'bg-green-100 text-green-700',
        rejected: 'bg-red-100 text-red-700'
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
};

const handleAction = (transaction, action) => {
    if (action === 'reject') {
        selectedTransaction.value = transaction;
        showRejectModal.value = true;
        return;
    }

    form.status = action;
    form.post(route('service-provider.transactions.update-status', transaction.id), {
        preserveScroll: true
    });
};

const submitReject = () => {
    form.status = 'rejected';
    form.post(route('service-provider.transactions.update-status', selectedTransaction.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showRejectModal.value = false;
            selectedTransaction.value = null;
            form.reset();
        }
    });
};

// Add helper function to get file icon
const getFileIcon = (mimeType) => {
    if (mimeType.startsWith('image/')) return 'fa-image';
    if (mimeType.includes('pdf')) return 'fa-file-pdf';
    return 'fa-file';
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Payment Transactions
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Transactions Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="p-4 text-left text-sm font-medium text-gray-500">Reference</th>
                                        <th class="p-4 text-left text-sm font-medium text-gray-500">Customer</th>
                                        <th class="p-4 text-left text-sm font-medium text-gray-500">Type</th>
                                        <th class="p-4 text-left text-sm font-medium text-gray-500">Amount</th>
                                        <th class="p-4 text-left text-sm font-medium text-gray-500">Proof of Payment</th>
                                        <th class="p-4 text-left text-sm font-medium text-gray-500">Status</th>
                                        <th class="p-4 text-left text-sm font-medium text-gray-500">Date</th>
                                        <th class="p-4 text-left text-sm font-medium text-gray-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="transaction in transactions.data" :key="transaction.id" class="hover:bg-gray-50">
                                        <td class="p-4">
                                            <div class="font-medium">{{ transaction.reference_number }}</div>
                                            <div class="text-sm text-gray-500">
                                                via {{ transaction.payment_account.account_type }}
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            {{ transaction.paid_by?.name }}
                                        </td>
                                        <td class="p-4">
                                            <span class="capitalize">{{ transaction.transaction_type }}</span>
                                        </td>
                                        <td class="p-4 font-medium">
                                            {{ formatCurrency(transaction.amount) }}
                                        </td>
                                        <td class="p-4">
                                            <div v-if="transaction.attachments?.length" class="flex flex-wrap gap-2">
                                                <a v-for="attachment in transaction.attachments"
                                                    :key="attachment.id"
                                                    :href="`/storage/${attachment.file_path}`"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-x-1.5 px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded transition-colors group"
                                                    :title="attachment.file_name"
                                                >
                                                    <i :class="[
                                                        'fa-solid',
                                                        getFileIcon(attachment.mime_type),
                                                        'text-gray-500 group-hover:text-gray-700'
                                                    ]"></i>
                                                    <span class="truncate max-w-[100px]">
                                                        {{ attachment.file_name }}
                                                    </span>
                                                </a>
                                            </div>
                                            <span v-else class="text-sm text-gray-500">
                                                No attachments
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <span :class="[
                                                'px-2 py-1 text-xs rounded-full',
                                                getStatusColor(transaction.status)
                                            ]">
                                                {{ transaction.status }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-sm text-gray-500">
                                            {{ moment(transaction.created_at).format('MMM DD, YYYY h:mm A') }}
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2" v-if="transaction.status === 'pending'">
                                                <button
                                                    @click="handleAction(transaction, 'approved')"
                                                    class="px-3 py-1 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700"
                                                >
                                                    Approve
                                                </button>
                                                <button
                                                    @click="handleAction(transaction, 'reject')"
                                                    class="px-3 py-1 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700"
                                                >
                                                    Reject
                                                </button>
                                            </div>
                                            <div v-else class="text-sm text-gray-500">
                                                {{ transaction.status === 'approved' ? 'Approved' : 'Rejected' }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <Modal :show="showRejectModal" @close="showRejectModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">
                    Reject Payment
                </h3>
                <div class="mt-6">
                    <div v-if="selectedTransaction?.attachments?.length" class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">
                            Proof of Payment
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div v-for="attachment in selectedTransaction.attachments"
                                :key="attachment.id"
                                class="relative group"
                            >
                                <!-- Image Preview -->
                                <div v-if="attachment.mime_type.startsWith('image/')"
                                    class="relative aspect-video rounded-lg overflow-hidden bg-gray-100"
                                >
                                    <img
                                        :src="`/storage/${attachment.file_path}`"
                                        :alt="attachment.file_name"
                                        class="object-cover w-full h-full"
                                    />
                                </div>

                                <!-- PDF/Other File Preview -->
                                <div v-else
                                    class="relative aspect-video rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center"
                                >
                                    <i :class="[
                                        'fa-solid',
                                        getFileIcon(attachment.mime_type),
                                        'text-4xl text-gray-400'
                                    ]"></i>
                                </div>

                                <!-- Filename & Download Link -->
                                <div class="mt-1 flex items-center justify-between">
                                    <span class="text-xs text-gray-500 truncate">
                                        {{ attachment.file_name }}
                                    </span>
                                    <a :href="`/storage/${attachment.file_path}`"
                                        target="_blank"
                                        class="text-xs text-primary hover:text-primary/80"
                                    >
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Reason for Rejection
                        </label>
                        <textarea
                            v-model="form.remarks"
                            class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary"
                            rows="3"
                            placeholder="Enter reason for rejection"
                        ></textarea>
                        <InputError :message="form.errors.remarks" class="mt-2" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            class="px-4 py-2 text-sm border rounded-lg hover:bg-gray-50"
                            @click="showRejectModal = false"
                        >
                            Cancel
                        </button>
                        <PrimaryButton
                            :disabled="form.processing"
                            @click="submitReject"
                        >
                            Confirm Rejection
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

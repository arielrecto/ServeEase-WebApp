<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import HeaderBackButton from '@/Components/HeaderBackButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import SelectInput from '@/Components/Form/SelectInput.vue';

const props = defineProps({
    paymentAccounts: Array
});

const showAddForm = ref(false);
const editingAccount = ref(null);

const form = useForm({
    account_name: '',
    account_number: '',
    account_type: ''
});

const accountTypes = [
    {
        name: 'GCash (E-Wallet)',
        value: 'gcash',
        icon: 'fa-solid fa-wallet',
        description: 'Mobile money service operated by Globe Telecom'
    },
    {
        name: 'PayMaya (E-Wallet)',
        value: 'paymaya',
        icon: 'fa-solid fa-mobile-screen-button',
        description: 'Digital financial services platform by PayMaya Philippines'
    },
    {
        name: 'Bank Account',
        value: 'bank',
        icon: 'fa-solid fa-building-columns',
        description: 'Traditional bank account (e.g. BDO, BPI, Security Bank)'
    }
];

const validateAccountNumber = (type, number) => {
    switch(type) {
        case 'gcash':
            return /^09\d{9}$/.test(number); // Philippines mobile number format
        case 'paymaya':
            return /^09\d{9}$/.test(number);
        case 'bank':
            return /^\d{10,16}$/.test(number); // Common bank account length
        default:
            return true;
    }
};

const submit = () => {
    // Validate account number format
    if (!validateAccountNumber(form.account_type, form.account_number)) {
        form.setError('account_number', 'Invalid account number format');
        return;
    }

    if (editingAccount.value) {
        form.put(route('service-provider.payment-accounts.update', editingAccount.value.id), {
            onSuccess: () => {
                form.reset();
                editingAccount.value = null;
            }
        });
    } else {
        form.post(route('service-provider.payment-accounts.store'), {
            onSuccess: () => {
                form.reset();
                showAddForm.value = false;
            }
        });
    }
};

const editAccount = (account) => {
    editingAccount.value = account;
    form.account_name = account.account_name;
    form.account_number = account.account_number;
    form.account_type = account.account_type;
};

const deleteAccount = (accountId) => {
    if (confirm('Are you sure you want to delete this payment account?')) {
        router.delete(route('service-provider.payment-accounts.destroy', accountId));
    }
};
</script>

<template>
    <Head title="Payment Accounts" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Payment Accounts
                </h2>
                <PrimaryButton @click="showAddForm = true" v-if="!showAddForm && !editingAccount">
                    Add Payment Account
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Add/Edit Form -->
                <div v-if="showAddForm || editingAccount" class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold">
                            {{ editingAccount ? 'Edit Payment Account' : 'Add Payment Account' }}
                        </h3>
                        <p class="mb-6 text-gray-600">Please provide your payment account details accurately to ensure proper transaction processing.</p>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Account Type Selection -->
                            <div>
                                <InputLabel for="account_type" value="Select Payment Method" />
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                                    <div v-for="type in accountTypes" :key="type.value"
                                        @click="form.account_type = type.value"
                                        :class="[
                                            'p-4 border rounded-lg cursor-pointer transition-all',
                                            form.account_type === type.value
                                                ? 'border-primary bg-primary/5 ring-2 ring-primary/20'
                                                : 'hover:border-gray-300'
                                        ]"
                                    >
                                        <div class="flex items-center gap-x-3">
                                            <i :class="[type.icon, 'text-xl', form.account_type === type.value ? 'text-primary' : 'text-gray-500']"></i>
                                            <div>
                                                <h4 class="font-medium">{{ type.name }}</h4>
                                                <p class="text-sm text-gray-600">{{ type.description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <InputError :message="form.errors.account_type" />
                            </div>

                            <!-- Account Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="account_name" value="Account Holder Name" />
                                    <TextInput
                                        id="account_name"
                                        v-model="form.account_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder="Enter the name on the account"
                                        required
                                    />
                                    <p class="mt-1 text-sm text-gray-500">Enter the name exactly as it appears on your account</p>
                                    <InputError :message="form.errors.account_name" />
                                </div>

                                <div>
                                    <InputLabel for="account_number" value="Account Number" />
                                    <TextInput
                                        id="account_number"
                                        v-model="form.account_number"
                                        type="text"
                                        class="mt-1 block w-full"
                                        :placeholder="form.account_type === 'bank' ? 'Enter bank account number' : 'Enter mobile number'"
                                        required
                                    />
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ form.account_type === 'bank'
                                            ? 'Enter your complete bank account number'
                                            : 'Enter your registered mobile number (e.g., 09XXXXXXXXX)'
                                        }}
                                    </p>
                                    <InputError :message="form.errors.account_number" />
                                </div>
                            </div>

                            <div class="flex justify-end gap-x-4 pt-4 border-t">
                                <button
                                    type="button"
                                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                                    @click="editingAccount ? (editingAccount = null) : (showAddForm = false)"
                                >
                                    Cancel
                                </button>
                                <PrimaryButton
                                    :disabled="form.processing || !form.account_type || !form.account_name || !form.account_number"
                                >
                                    {{ editingAccount ? 'Update Account' : 'Save Account' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Payment Accounts List -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Your Payment Accounts</h3>
                        <div v-if="paymentAccounts.length" class="space-y-4">
                            <div v-for="account in paymentAccounts" :key="account.id"
                                class="p-4 border rounded-lg hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-start gap-x-4">
                                        <!-- Account Type Icon -->
                                        <div class="p-3 bg-gray-50 rounded-lg">
                                            <i :class="[
                                                account.account_type === 'gcash' ? 'fa-solid fa-wallet text-blue-500' :
                                                account.account_type === 'paymaya' ? 'fa-solid fa-mobile-screen-button text-purple-500' :
                                                'fa-solid fa-building-columns text-gray-700',
                                                'text-xl'
                                            ]"></i>
                                        </div>

                                        <!-- Account Details -->
                                        <div>
                                            <div class="flex items-center gap-x-2">
                                                <h4 class="font-semibold">
                                                    {{ account.account_type === 'gcash' ? 'GCash' :
                                                    account.account_type === 'paymaya' ? 'PayMaya' :
                                                    'Bank Account'
                                                    }}
                                                </h4>
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">
                                                    Verified
                                                </span>
                                            </div>
                                            <p class="text-gray-600 mt-1">{{ account.account_name }}</p>
                                            <p class="text-gray-600 font-mono">
                                                {{ account.account_type === 'bank'
                                                    ? '••••' + account.account_number.slice(-4)
                                                    : account.account_number.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3')
                                                }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Added on {{ new Date(account.created_at).toLocaleDateString() }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-x-2">
                                        <button
                                            @click="editAccount(account)"
                                            class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Edit Account"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button
                                            @click="deleteAccount(account.id)"
                                            class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Delete Account"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-12 bg-gray-50 rounded-lg">
                            <i class="fa-solid fa-credit-card text-4xl text-gray-400 mb-3"></i>
                            <h4 class="text-lg font-medium text-gray-600 mb-2">No Payment Accounts</h4>
                            <p class="text-gray-500 mb-4">Add your payment accounts to receive payments from customers</p>
                            <PrimaryButton @click="showAddForm = true">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Add Your First Account
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

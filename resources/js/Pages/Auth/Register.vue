<script setup>
import { ref } from 'vue';
import TermsAndConditionModal from '@/Components/TermsAndConditionModal.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    termsAndCondition: Object
});

const showTermsModal = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    acceptTerms: false,
});

const handleTermsAgreement = () => {
    form.acceptTerms = true;
    showTermsModal.value = false;
};

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput id="name" type="text" class="block w-full mt-1" v-model="form.name" required autofocus
                    autocomplete="name" />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput id="email" type="email" class="block w-full mt-1" v-model="form.email" required
                    autocomplete="username" />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput id="password" type="password" class="block w-full mt-1" v-model="form.password" required
                    autocomplete="new-password" />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <TextInput id="password_confirmation" type="password" class="block w-full mt-1"
                    v-model="form.password_confirmation" required autocomplete="new-password" />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4">
                <div class="flex items-center">
                    <input type="checkbox" id="terms" v-model="form.acceptTerms"
                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                    <label for="terms" class="ml-2 text-sm text-gray-600 cursor-pointer">
                        I agree to the
                        <button type="button" @click="showTermsModal = true"
                            class="text-indigo-600 underline hover:text-indigo-800">
                            terms and conditions
                        </button>
                    </label>
                </div>
                <InputError class="mt-2" :message="form.errors.acceptTerms" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <Link :href="route('login')"
                    class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Already registered?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing || !form.acceptTerms">
                    Register
                </PrimaryButton>
            </div>

            <TermsAndConditionModal v-if="showTermsModal" :terms-and-condition="termsAndCondition"
                :show="showTermsModal" @close="showTermsModal = false" @agree="handleTermsAgreement" />
        </form>
    </GuestLayout>
</template>

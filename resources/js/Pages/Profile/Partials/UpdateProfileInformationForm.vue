<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/Form/SelectInput.vue';
import ImageUpload from '@/Components/Form/ImageUpload.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    profile: Object,
    sexChoices: Object,
});

const form = useForm({
    first_name: props.profile?.first_name,
    middle_name: props.profile?.middle_name,
    last_name: props.profile?.last_name,
    address: props.profile?.address,
    gender: props.profile?.gender,
    avatar: null,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your profile information.
            </p>
        </header>

        <form @submit.prevent="form.post(route('profile.updateProfile'))" class="mt-6 space-y-6">
            <div>
                <InputLabel for="first_name" value="First Name" />

                <TextInput id="first_name" type="text" class="block w-full mt-1" v-model="form.first_name" required autocomplete="first_name" />

                <InputError class="mt-2" :message="form.errors.first_name" />
            </div>

            <div>
                <InputLabel for="middle_name" value="Middle Name" isOptional />

                <TextInput id="middle_name" type="text" class="block w-full mt-1" v-model="form.middle_name"
                    autocomplete="middle_name" />

                <InputError class="mt-2" :message="form.errors.middle_name" />
            </div>

            <div>
                <InputLabel for="last_name" value="Last Name" />

                <TextInput id="last_name" type="text" class="block w-full mt-1" v-model="form.last_name" required
                    autocomplete="last_name" />

                <InputError class="mt-2" :message="form.errors.last_name" />
            </div>

            <div>
                <InputLabel for="sex" value="Sex" />

                <SelectInput id="sex" class="block w-full mt-1" v-model="form.gender" required>
                    <option v-for="sex in sexChoices" :key="sex" :value="sex" :selected="sex === profile?.gender">{{ sex }}</option>
                </SelectInput>

                <InputError class="mt-2" :message="form.errors.gender" />
            </div>

            <div>
                <InputLabel for="address" value="Address" />

                <TextInput id="address" type="text" class="block w-full mt-1" v-model="form.address" required
                    autocomplete="address" />

                <InputError class="mt-2" :message="form.errors.address" />
            </div>

            <div>
                <InputLabel for="avatar"
                    value="Profile photo" isOptional />

                <ImageUpload @success="(src) => form.avatar = src" />

                <InputError class="mt-2" :message="form.errors.avatar" />
            </div>


            <!-- <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Link :href="route('verification.send')" method="post" as="button"
                        class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Click here to re-send the verification email.
                    </Link>
                </p>

                <div v-show="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                    A new verification link has been sent to your email address.
                </div>
            </div> -->

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/Form/SelectInput.vue';
import ImageUpload from '@/Components/Form/ImageUpload.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    providerProfile: Object,
    serviceTypes: Array,
});

const form = useForm({
    service: props.providerProfile.service_type,
    experience: props.providerProfile.experience,
    contact: props.providerProfile.contact,
    certificate: null,
});

const submit = () => {
    form.put(route('profile.updateProviderProfile'));
}
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Provider Information</h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your provider information.
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <div>
                <InputLabel for="service" value="Select a service that you offer" />

                <SelectInput id="service" class="block w-full mt-1"
                    v-model="form.service" required>
                    <option v-for="service in serviceTypes" :selected="service === providerProfile.service_type">{{ service }}</option>
                </SelectInput>

                <InputError class="mt-2" :message="form.errors.service" />
            </div>

            <div>
                <InputLabel for="experience"
                    value="Years of experience" />

                <TextInput id="experience" type="text" class="block w-full mt-1"
                    v-model="form.experience" required />

                <InputError class="mt-2" :message="form.errors.experience" />
            </div>

            <div>
                <InputLabel for="contact"
                    value="Contact Number" />

                <TextInput id="contact" type="text" class="block w-full mt-1" v-model="form.contact"
                    required />

                <InputError class="mt-2" :message="form.errors.contact" />
            </div>

            <div>
                <InputLabel for="certificate"
                    value="Credential photo" />

                <ImageUpload @success="(imgSrc) => form.certificate = imgSrc" />

                <InputError class="mt-2" :message="form.errors.certificate" />

                <ul class="mt-2 text-gray-600">
                    The proof image can be any of the following:
                    <li class="list-disc list-inside">Certificates</li>
                    <li class="list-disc list-inside">Valid IDs</li>
                </ul>
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

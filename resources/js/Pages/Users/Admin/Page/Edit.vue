<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import HeaderBackButton from '@/Components/HeaderBackButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
    page: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: props.page.name,
    content: props.page.content
});

const submitForm = () => {
    form.put(route('admin.cms.update', props.page.slug), {
        onSuccess: () => {
            // Handle success, e.g., show a success message or redirect
            console.log('Page updated successfully');
        },
        onError: () => {
            // Handle error, e.g., show an error message
            console.error('Failed to update page');
        }
    });
};

onMounted(() => {
    // Force a re-render of the editor after mount
    nextTick(() => {
        form.content = form.content;
    });
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton :url="route('admin.cms.index')" />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Page: {{ form.name }}
                </h2>
            </div>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submitForm">
                            <!-- Title Input -->
                            <div class="mb-6">
                                <InputLabel for="title" value="Page Title" />
                                <TextInput id="title" type="text" class="block w-full mt-1" v-model="form.name"
                                    required />
                            </div>

                            <!-- Content Editor -->
                            <div class="mb-6">
                                <InputLabel value="Page Content" />
                                <QuillEditor v-model:content="form.content" :options="{
                                    theme: 'snow',
                                    modules: {
                                        toolbar: [
                                            ['bold', 'italic', 'underline', 'strike'],
                                            ['blockquote', 'code-block'],
                                            [{ 'header': 1 }, { 'header': 2 }],
                                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                            [{ 'script': 'sub' }, { 'script': 'super' }],
                                            [{ 'indent': '-1' }, { 'indent': '+1' }],
                                            ['link',],
                                            ['clean']
                                        ]
                                    }
                                }" contentType="html" class="h-64 mb-4" />
                            </div>

                            <!-- Form Buttons -->
                            <div class="flex items-center justify-end gap-4">
                                <PrimaryButton type="submit">
                                    Save Changes
                                </PrimaryButton>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';

const imageSrc = ref(null);

const emit = defineEmits(['success']);

const handleUpload = (e) => {
    const file = e.target.files[0];

    const reader = new FileReader();

    // Get the result after reading data successfully
    reader.onload = function(e) {
        imageSrc.value = reader.result;

        // Send image source to parent
        emit('success', file);
    }

    // Read the selected file as Data URL
    reader.readAsDataURL(file);
}

</script>

<template>
    <div class="flex items-center justify-center w-full">
        <template v-if="imageSrc !== null">
            <div class="flex flex-col w-full gap-5">
                <img :src="imageSrc" alt="Uploaded image" class="w-full h-full">
                <div class="flex items-center justify-center p-2">
                    <button type="button" @click="imageSrc = null" class="btn btn-error btn-sm ">Change</button>
                </div>
            </div>
        </template>
        <label for="dropzone-file" v-if="imageSrc === null"
            class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 hover:bg-gray-100">
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                </svg>
                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                        class="font-semibold">Click to upload</span></p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Max. size of 5MB (.JPG,
                    .JPEG,
                    .PNG)
                </p>
            </div>
            <input id="dropzone-file" @change="handleUpload" name="image" type="file" accept="image/*"
                class="hidden" />
        </label>
    </div>
</template>

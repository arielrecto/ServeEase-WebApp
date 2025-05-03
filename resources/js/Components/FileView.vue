<script setup>
import { computed } from 'vue';

const props = defineProps({
    file: {
        type: Object,
        required: true
    },
    showDelete: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['delete']);

const fileType = computed(() => {
    const type = props.file.mime_type || props.file.type;
    if (type.startsWith('image/')) return 'image';
    if (type.includes('pdf')) return 'pdf';
    if (type.includes('video/')) return 'video';
    if (type.includes('audio/')) return 'audio';
    return 'document';
});

const fileIcon = computed(() => {
    const icons = {
        'image': 'fa-image',
        'pdf': 'fa-file-pdf',
        'video': 'fa-file-video',
        'audio': 'fa-file-audio',
        'document': 'fa-file'
    };
    return icons[fileType.value];
});

const fileSize = computed(() => {
    const size = props.file.file_size || props.file.size;
    if (size < 1024) return `${size} B`;
    if (size < 1024 * 1024) return `${(size / 1024).toFixed(1)} KB`;
    return `${(size / 1024 / 1024).toFixed(1)} MB`;
});

const handleDelete = () => {
    emit('delete', props.file);
};
</script>

<template>
    <div class="relative flex items-center p-3 bg-white rounded-lg border group hover:bg-gray-50">
        <!-- Preview Section -->
        <div class="w-12 h-12 mr-3 flex items-center justify-center">
            <!-- Image Preview -->
            <img v-if="fileType === 'image' && file.file_path" 
                :src="`/storage/${file.file_path}`"
                class="w-full h-full object-cover rounded"
                alt="File preview"
            />
            
            <!-- Icon for other file types -->
            <i v-else 
                :class="['fas', fileIcon, 'text-2xl text-gray-400']"
            ></i>
        </div>

        <!-- File Info -->
        <div class="flex-1 min-w-0">
            <div class="flex items-center">
                <p class="text-sm font-medium text-gray-900 truncate">
                    {{ file.file_name || file.name }}
                </p>
                <span class="ml-2 px-2 py-0.5 text-xs rounded-full" 
                    :class="{
                        'bg-blue-100 text-blue-800': fileType === 'document',
                        'bg-green-100 text-green-800': fileType === 'image',
                        'bg-red-100 text-red-800': fileType === 'pdf',
                        'bg-purple-100 text-purple-800': fileType === 'video',
                        'bg-yellow-100 text-yellow-800': fileType === 'audio'
                    }"
                >
                    {{ fileType }}
                </span>
            </div>
            <p class="text-xs text-gray-500">
                {{ fileSize }}
            </p>
        </div>

        <!-- Actions -->
        <div class="ml-4 flex-shrink-0">
            <!-- Download button for stored files -->
            <a v-if="file.file_path" 
                :href="`/storage/${file.file_path}`" 
                target="_blank"
                class="text-gray-400 hover:text-primary transition-colors"
                title="Download file"
            >
                <i class="fas fa-download"></i>
            </a>

            <!-- Delete button if showDelete is true -->
            <button v-if="showDelete"
                type="button"
                @click="handleDelete"
                class="ml-3 text-gray-400 hover:text-red-500 transition-colors"
                title="Remove file"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Preview Overlay for Images -->
        <div v-if="fileType === 'image' && file.file_path"
            class="absolute inset-0 bg-black/50 items-center justify-center hidden group-hover:flex">
            <a :href="`/storage/${file.file_path}`" 
                target="_blank"
                class="text-white hover:text-primary transition-colors"
                title="View full image"
            >
                <i class="fas fa-expand text-xl"></i>
            </a>
        </div>
    </div>
</template>
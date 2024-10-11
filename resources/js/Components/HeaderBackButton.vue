<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    url: String,
});
</script>

<template>
    <Link :href="url" class="button-ghost">
        <i class="fi fi-br-arrow-left"></i>
    </Link>
</template>

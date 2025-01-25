<script setup>
import { router } from "@inertiajs/vue3";

const props = defineProps(["service"]);

const addToFavorites = () => {
    router.post(route("customer.favorites.add", props.service.id));
};

const removeFromFavorites = () => {
    router.delete(route("customer.favorites.destroy", props.service.id));
};
</script>

<template>
    <div title="Add to Favorites" class="text-center">
        <form
            @submit.prevent="
                () => {
                    if (service.is_added_to_favorites) {
                        removeFromFavorites();
                    } else {
                        addToFavorites();
                    }
                }
            "
        >
            <button
                class="flex items-center justify-center text-3xl"
                :class="
                    service?.is_added_to_favorites
                        ? 'text-yellow-500'
                        : 'text-gray-500 hover:text-gray-700'
                "
            >
                <i
                    :class="
                        service?.is_added_to_favorites
                            ? 'ri-star-fill'
                            : 'ri-star-line'
                    "
                ></i>
                <!-- {{ service.is_added_to_favorites }} -->
            </button>
        </form>
    </div>
</template>

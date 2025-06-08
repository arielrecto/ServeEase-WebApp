<script setup>
import { Link } from "@inertiajs/vue3";
import ModalLinkSlideover from "@/Components/Modal/ModalLinkSlideover.vue";

const props = defineProps({
    service: Object,
    linkType: {
        type: String,
        default: "link",
    },
});
</script>

<template>
    <article class="flex items-center w-full gap-x-2 group">
        <div class="h-32 overflow-hidden aspect-video rounded-xl">
            <img
                alt=""
                :src="service.service_thumbnail"
                class="object-cover w-full h-full"
            />
        </div>
        <div class="w-full">
            <div class="flex items-center justify-between w-full gap-x-2">
                <h3
                    :title="service.name"
                    class="font-medium text-gray-900 line-clamp-1 text-ellipsis"
                >
                    <Link
                        v-if="linkType === 'link'"
                        :href="route('customer.services.show', service.id)"
                        >{{ service.name }}</Link
                    >
                    <ModalLinkSlideover
                        v-if="linkType === 'modal'"
                        :href="route('booking.show', service.id)"
                        maxWidth="xl"
                        >{{ service.name }}</ModalLinkSlideover
                    >
                </h3>

                <div class="w-[20%] text-center text-sm">
                    <i class="text-yellow-500 fa-solid fa-star"></i>
                    {{ service.avg_rate }}
                </div>
            </div>

            <Link
                :href="
                    route(
                        'profile.showProviderProfile',
                        service.user.profile.provider_profile.id
                    )
                "
                class="mt-1 hover:underline"
            >
                {{ service.user.profile.full_name }}
            </Link>

            <div class="flex justify-between mt-1 line-clamp-3 text-sm/relaxed">
                <p class="text-lg">
                    ₱
                    <span class="text-primary"
                        >{{ service.price.toLocaleString() }}
                    </span>
                    <span v-if="service.price_type === 'fixed'">(Fixed)</span>
                </p>
                <span class="text-gray-500"
                    >{{ service.avail_service_count }} booking(s)</span
                >
            </div>
        </div>
    </article>
</template>

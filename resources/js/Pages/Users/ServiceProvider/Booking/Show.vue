<script setup>
import { Modal } from "@inertiaui/modal-vue";
import { Link, useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";
import moment from "moment";

import FeedbackList from "@/Components/Feedbacks/FeedbackList.vue";

const props = defineProps(["service"]);

const activeTab = ref("about");
const ratings = ["5", "4", "3", "2", "1"];

const modalRef = ref(null);
</script>

<template>
    <Modal ref="modalRef">
        <div class="h-[1000px] max-h-[85%] overflow-y-auto pb-10">
            <div
                class="sticky top-0 left-0 py-2 space-y-2 font-semibold bg-white"
            >
                <h2 class="text-lg">{{ service.name }}</h2>
                <span class="text-sm">
                    <i class="text-yellow-500 fa-solid fa-star"></i>
                    4.8
                </span>
                <div>
                    ₱ <span class="text-3xl font-black text-primary">2000</span>
                    <span
                        v-if="service.price_type === 'fixed'"
                        class="text-gray-600"
                    >
                        (Fixed Rate)</span
                    >
                </div>

                <div
                    role="tablist"
                    class="z-0 text-sm font-bold text-center tabs tabs-bordered"
                >
                    <button
                        @click.stop="activeTab = 'about'"
                        role="tab"
                        class="uppercase tab"
                        :class="{ 'tab-active': activeTab === 'about' }"
                    >
                        About
                    </button>
                    <button
                        @click.stop="activeTab = 'reviews'"
                        role="tab"
                        class="uppercase tab"
                        :class="{ 'tab-active': activeTab === 'reviews' }"
                    >
                        Reviews
                    </button>
                </div>
            </div>
            <Transition>
                <section
                    v-if="activeTab === 'about'"
                    class="space-y-4 overflow-y-hidden"
                >
                    <div class="flex flex-col gap-y-5">
                        <div class="overflow-hidden aspect-video">
                            <img
                                :src="
                                    service.thumbnail ??
                                    'https://images.unsplash.com/photo-1631451095765-2c91616fc9e6?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80'
                                "
                                alt="Service thumbnail"
                                class="object-cover"
                            />
                        </div>
                        <div class="flex flex-col gap-y-1">
                            <span class="font-semibold">Description</span>
                            <div class="overflow-y-auto max-h-48">
                                <span class="text-gray-600">{{
                                    service.description
                                }}</span>
                            </div>
                        </div>
                        <div
                            v-if="service.terms_and_conditions"
                            class="flex flex-col gap-y-1"
                        >
                            <span class="font-semibold">Description</span>
                            <div class="overflow-y-auto max-h-48">
                                <span class="text-gray-600">{{
                                    service.terms_and_conditions
                                }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col mt-20 gap-y-3">
                            <span class="text-xl font-semibold"
                                >About the provider</span
                            >
                            <div class="flex flex-col gap-y-1">
                                <div class="flex items-start gap-x-4">
                                    <div
                                        class="w-16 bg-gray-600 rounded-full aspect-square"
                                    ></div>
                                    <div class="flex flex-col space-y-1">
                                        <span class="text-xl">{{
                                            service.user.name
                                        }}</span>
                                        <span
                                            class="text-sm italic text-gray-600"
                                            >{{
                                                service.user.profile
                                                    .provider_profile.contact
                                            }}</span
                                        >
                                        <span
                                            class="text-sm italic text-gray-600"
                                            >Experience:
                                            {{
                                                service.user.profile
                                                    .provider_profile.experience
                                            }}</span
                                        >
                                        <Link
                                            @click="modalRef.close"
                                            :href="
                                                route(
                                                    'profile.showProviderProfile',
                                                    service.user.profile
                                                        .provider_profile.id
                                                )
                                            "
                                            class="underline text-primary"
                                            >Go to Profile</Link
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section v-else-if="activeTab === 'reviews'">
                    <FeedbackList :service="service" />
                </section>
            </Transition>
        </div>

        <div class="sticky bottom-0 left-0 w-full py-2 bg-white">
            <Link
                @click="modalRef.close"
                :href="route('customer.services.show', service.id)"
                class="flex w-full text-white panel-btn bg-primary"
            >
                Book Now
            </Link>
        </div>
    </Modal>
</template>

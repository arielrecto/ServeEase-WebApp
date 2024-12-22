<script setup>
import { reactive } from "vue";
import moment from "moment";
import { Head, usePage, Link } from "@inertiajs/vue3";
import axios from "axios";

import SelectInput from "@/Components/Form/SelectInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FeedbackList from "@/Components/Feedbacks/FeedbackList.vue";
import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";

import { useLoader } from "../../Composables/loader";

const props = defineProps([
    "providerProfile",
    "feedbackCount",
    "user",
    "profile",
    "service",
]);

const { setIsLoading } = useLoader();

const state = reactive({
    feedbacks: [],
    tabs: [
        { name: "Service", value: "0" },
        { name: "Reviews", value: "1" },
    ],
    form: { rating: null },
});

const ratingOptions = [5, 4, 3, 2, 1];

// const fetchFeedbacks = async () => {
//     setIsLoading(true);
//     try {
//         const res = await axios.get(
//             `/customer/services/${props.service.id}/feedback`,
//             {
//                 params: { rating: state.form.rating },
//             }
//         );

//         state.feedbacks = [...res.data];
//     } catch (e) {
//         console.log(e);
//     }
//     setIsLoading(false);
// };

// (() => {
//     fetchFeedbacks();
// })();
</script>

<template>
    <Head title="Provider Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Provider Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                    <div
                        class="flex flex-col items-start justify-between gap-5 md:flex-row"
                    >
                        <div class="flex items-center gap-x-6">
                            <div
                                class="w-16 h-16 bg-gray-300 rounded-full q md:w-28 md:h-28"
                            ></div>
                            <div class="space-y-1">
                                <div class="text-xl">{{ user.name }}</div>
                                <div class="text-sm italic text-gray-600">
                                    {{ providerProfile.contact }}
                                </div>
                                <div class="text-sm italic text-gray-600">
                                    Experience: {{ providerProfile.experience }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-x-4">
                            <div
                                class="w-full h-auto p-6 border border-gray-300 rounded-lg md:w-72"
                            >
                                <div>
                                    <span> Finished Transactions </span>
                                </div>
                                <div class="text-2xl font-black text-primary">
                                    {{ service.finished_bookings_count }}
                                </div>
                            </div>

                            <div
                                class="w-full h-auto p-6 border border-gray-300 rounded-lg md:w-72"
                            >
                                <div>
                                    <span> Total transactions </span>
                                </div>
                                <div class="text-2xl font-black text-primary">
                                    {{ service.bookings_count }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <Tabs value="0">
                        <TabList>
                            <Tab v-for="tab in state.tabs" :value="tab.value">{{
                                tab.name
                            }}</Tab>
                        </TabList>
                        <TabPanels>
                            <TabPanel value="0">
                                <div
                                    v-if="service"
                                    class="flex flex-col gap-y-4 md:grid md:grid-cols-3 gap-x-8"
                                >
                                    <div
                                        class="order-2 col-span-2 space-y-6 md:order-1"
                                    >
                                        <div class="mb-12 space-y-2">
                                            <h1 class="text-2xl font-bold">
                                                {{ service.name }}
                                            </h1>
                                            <span
                                                class="inline-block mr-3 text-sm"
                                            >
                                                <i
                                                    class="text-yellow-500 fa-solid fa-star"
                                                ></i>
                                                {{ service.avg_rate }} ({{
                                                    feedbackCount
                                                }})
                                            </span>
                                        </div>

                                        <div
                                            class="overflow-hidden aspect-video"
                                        >
                                            <img
                                                :src="
                                                    service.thumbnail ??
                                                    'https://images.unsplash.com/photo-1631451095765-2c91616fc9e6?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80'
                                                "
                                                alt="Service thumbnail"
                                                class="object-cover"
                                            />
                                        </div>

                                        <div>
                                            <Link href="#" class="text-primary"
                                                ><i class="ri-edit-2-line"></i>
                                                Edit details</Link
                                            >
                                        </div>

                                        <div class="flex flex-col gap-y-1">
                                            <span class="text-gray-600"
                                                >Description</span
                                            >
                                            <div
                                                class="overflow-y-auto max-h-48"
                                            >
                                                <span class="text-black">{{
                                                    service.description
                                                }}</span>
                                            </div>
                                        </div>
                                        <div
                                            v-if="service.terms_and_conditions"
                                            class="flex flex-col gap-y-1"
                                        >
                                            <span class="text-gray-600"
                                                >Terms & conditions</span
                                            >
                                            <div
                                                class="overflow-y-auto max-h-48"
                                            >
                                                <span class="text-black">{{
                                                    service.terms_and_conditions
                                                }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="h-[20vh] flex flex-col gap-y-4 items-center justify-center"
                                >
                                    <div class="text-gray-600">
                                        No service found.
                                    </div>
                                    <Link
                                        :href="
                                            route(
                                                'service-provider.services.create'
                                            )
                                        "
                                        class="button-primary"
                                        >Click to create one</Link
                                    >
                                </div>
                            </TabPanel>

                            <TabPanel value="1">
                                <div class="flex flex-col mb-6 space-y-1">
                                    <div
                                        class="inline-block mr-3 text-2xl font-bold"
                                    >
                                        <i
                                            class="text-yellow-500 fa-solid fa-star"
                                        ></i>
                                        {{ service.avg_rate }} / 5
                                    </div>
                                    <div class="text-gray-500">
                                        {{ feedbackCount }} total reviews
                                    </div>
                                </div>
                                <!-- <SelectInput
                                    v-model="state.form.rating"
                                    class="mt-1 text-black"
                                    :placeholder="'Rating'"
                                    required
                                    @update-value="fetchFeedbacks"
                                >
                                    <option
                                        v-for="rating in ratingOptions"
                                        :key="rating"
                                        :value="rating"
                                    >
                                        {{ rating }}
                                    </option>
                                </SelectInput> -->

                                <FeedbackList :feedbacks="feedbacks" />
                                <!-- <div
                                    class="py-2 pr-5 space-y-5 divide-y divide-gray-300"
                                >
                                    <div
                                        v-for="n in 3"
                                        class="flex pt-2 gap-x-4"
                                    >
                                        <div>
                                            <div
                                                class="w-8 bg-gray-600 rounded-full aspect-square"
                                            ></div>
                                        </div>
                                        <div class="space-y-1">
                                            <div>
                                                <span
                                                    class="inline-block mr-3 text-primary"
                                                    >John Doe</span
                                                >
                                                <span
                                                    class="inline-block mr-3 text-sm"
                                                >
                                                    <i
                                                        class="text-yellow-500 fa-solid fa-star"
                                                    ></i>
                                                    {{
                                                        Math.floor(
                                                            Math.random() * 5 +
                                                                1
                                                        )
                                                    }}
                                                </span>
                                                <span
                                                    class="text-sm italic text-gray-600"
                                                    >{{
                                                        moment().format("ll")
                                                    }}</span
                                                >
                                            </div>
                                            <div
                                                class="overflow-y-auto leading-relaxed max-h-28"
                                            >
                                                Why bother with the movement of
                                                the train, their high heels like
                                                polished hooves against the gray
                                                metal of the Villa bespeak a
                                                turning in, a denial of the
                                                bright void beyond the hull. Her
                                                cheekbones flaring scarlet as
                                                Wizard’s Castle burned, forehead
                                                drenched with azure when Munich
                                                fell to the Tank War, mouth
                                                touched with hot gold as a paid
                                                killer in the coffin for
                                                Armitage’s call. That was
                                                Wintermute, manipulating the
                                                lock the way it had manipulated
                                                the drone micro and the chassis
                                                of a gutted game console. The
                                                two surviving Founders of Zion
                                                were old men, old with the
                                                surgery, he found himself
                                                thinking, while sweat coursed
                                                down his ribs, when you could
                                                just carry the thing for what it
                                                was a handgun and nine rounds of
                                                ammunition, and as he made his
                                                way down Shiga from the sushi
                                                stall he cradled it in his
                                                sleep, and wake alone in the
                                                human system. Now this quiet
                                                courtyard, Sunday afternoon,
                                                this girl with a ritual lack of
                                                urgency through the arcs and
                                                passes of their dance, point
                                                passing point, as the men waited
                                                for an opening. The alarm still
                                                oscillated, louder here, the
                                                rear of the Flatline as a
                                                construct, a hardwired ROM
                                                cassette replicating a dead
                                                man’s skills, obsessions,
                                                kneejerk responses.
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            </TabPanel>
                        </TabPanels>
                    </Tabs>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

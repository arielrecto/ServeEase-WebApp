<script setup>
import moment from "moment";
import axios from "axios";

import SelectInput from "@/Components/Form/SelectInput.vue";

import { useLoader } from "../../Composables/loader";

const props = defineProps({
    feedbacks: Array,
    filterShown: {
        type: Boolean,
        default: true,
    },
});

const { setIsLoading } = useLoader();

const ratingOptions = [5, 4, 3, 2, 1];

const fetchFeedbacks = async () => {
    setIsLoading(true);
    try {
        const res = await axios.get(
            `/customer/services/${props.service.id}/feedback`,
            {
                params: { rating: state.form.rating },
            }
        );

        state.feedbacks = [...res.data];
    } catch (e) {
        console.log(e);
    }
    setIsLoading(false);
};

(() => {
    fetchFeedbacks();
})();
</script>

<template>
    <template v-if="feedbacks">
        <SelectInput
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
        </SelectInput>

        <div class="py-2 pr-5 space-y-5 divide-y divide-gray-300">
            <div v-for="n in 3" class="flex pt-2 gap-x-4">
                <div>
                    <div
                        class="w-8 bg-gray-600 rounded-full aspect-square"
                    ></div>
                </div>
                <div class="space-y-1">
                    <div>
                        <span class="inline-block mr-3 text-primary"
                            >John Doe</span
                        >
                        <span class="inline-block mr-3 text-sm">
                            <i class="text-yellow-500 fa-solid fa-star"></i>
                            {{ Math.floor(Math.random() * 5 + 1) }}
                        </span>
                        <span class="text-sm italic text-gray-600">{{
                            moment().format("ll")
                        }}</span>
                    </div>
                    <div class="overflow-y-auto leading-relaxed max-h-28">
                        Why bother with the movement of the train, their high
                        heels like polished hooves against the gray metal of the
                        Villa bespeak a turning in, a denial of the bright void
                        beyond the hull. Her cheekbones flaring scarlet as
                        Wizard’s Castle burned, forehead drenched with azure
                        when Munich fell to the Tank War, mouth touched with hot
                        gold as a paid killer in the coffin for Armitage’s call.
                        That was Wintermute, manipulating the lock the way it
                        had manipulated the drone micro and the chassis of a
                        gutted game console. The two surviving Founders of Zion
                        were old men, old with the surgery, he found himself
                        thinking, while sweat coursed down his ribs, when you
                        could just carry the thing for what it was a handgun and
                        nine rounds of ammunition, and as he made his way down
                        Shiga from the sushi stall he cradled it in his sleep,
                        and wake alone in the human system. Now this quiet
                        courtyard, Sunday afternoon, this girl with a ritual
                        lack of urgency through the arcs and passes of their
                        dance, point passing point, as the men waited for an
                        opening. The alarm still oscillated, louder here, the
                        rear of the Flatline as a construct, a hardwired ROM
                        cassette replicating a dead man’s skills, obsessions,
                        kneejerk responses.
                    </div>
                </div>
            </div>
        </div>
    </template>
    <div v-else class="py-2">
        <p class="text-center text-gray-600">No reviews found.</p>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { Link } from "@inertiajs/vue3";

const banners = ref(["/assets/bgc.jpg", "/assets/bgc2.jpg"]);

// const currentBanner = (() => {
//     banners.value.filter((value, index) => )
// });

let currentBannerIndex = ref(0);

const changeBanner = () => {
    banners.value.forEach((value, index) => {
        if (currentBannerIndex.value < index) {
            currentBannerIndex.value = index;
        }
    });
};

const currentBanner = computed(() => banners.value[currentBannerIndex.value]);

const timeoutId = setTimeout(changeBanner, 4000);

onUnmounted(() => {
    clearTimeout(timeoutId);
});
</script>

<template>
    <!-- bg-[url(https://images.unsplash.com/photo-1604014237800-1c9102c219da?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80)] -->
    <section
        class="relative bg-[url(/assets/images/bgc.jpg)] bg-cover bg-left bg-no-repeat"
    >
        <div
            class="absolute inset-0 bg-white/75 sm:bg-transparent sm:from-white/95 sm:from-50% sm:to-white/25 sm:bg-gradient-to-r"
        ></div>

        <div
            class="relative max-w-screen-xl px-4 py-32 mx-auto sm:px-6 lg:flex lg:h-screen lg:items-center lg:px-8"
        >
            <div class="max-w-xl text-center sm:text-left">
                <h1 class="text-3xl font-extrabold sm:text-5xl">
                    Local Service Marketplace for the<strong
                        class="font-extrabold text-primary"
                    >
                        City of Bacoor</strong
                    >.
                </h1>

                <p class="max-w-lg mt-4 sm:text-xl/relaxed">
                    Discover trusted professionals to fulfill all your needs,
                    right in your neighborhood.
                </p>

                <div
                    class="flex flex-wrap justify-center gap-4 mt-8 text-center md:justify-start"
                >
                    <Link
                        :href="route('login')"
                        class="uppercase btn btn-primary"
                    >
                        Get Started
                    </Link>

                    <Link
                        :href="route('guest.services')"
                        class="uppercase bg-white btn text-primary"
                    >
                        Learn More
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>

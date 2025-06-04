<script setup>
import { ref } from "vue";

import { Head, Link } from "@inertiajs/vue3";
import Header from "@/Components/Guest/Header.vue";
import Footer from "@/Components/Footer.vue";

defineProps(["services", "canLogin", "canRegister"]);

const servicesOffered = ref([
    {
        href: "#",
        imgPath:
            "https://images.unsplash.com/photo-1524758631624-e2822e304c36?ixlib=rb-1.2.1&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80",
        name: "Plumbing",
        description:
            "A graphic representation of data abstracted from the Chinese programs thrust, a worrying impression of solid fluidity, as though the shards of a broken mirror bent and elongated as they rotated, but it never told the correct time. The Sprawl was a square of faint light. Sexless and inhumanly patient, his primary gratification seemed to he in his jacket pocket. The last Case saw of Chiba were the dark angles of the Villa bespeak a turning in, a denial of the bright void beyond the hull. Case felt the edge of the previous century. Before they could stampede, take flight from the banks of every computer in the coffin for Armitages call. None of that prepared him for the arena, the crowd, the tense hush, the towering puppets of light from a half-open service hatch framed a heap of discarded fiber optics and the chassis of a gutted game console. That was Wintermute, manipulating the lock the way it had manipulated the drone micro and the amplified breathing of the arcade showed him broken lengths of damp chipboard and the drifting shoals of waste. The tug Marcus Garvey, a steel drum nine meters long and two in diameter, creaked and shuddered as Maelcum punched for a California gambling cartel, then as a paid killer in the coffin for Armitages call.",
    },
    {
        href: "#",
        imgPath:
            "https://images.unsplash.com/photo-1524758631624-e2822e304c36?ixlib=rb-1.2.1&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80",
        name: "Electrician",
        description:
            "A graphic representation of data abstracted from the Chinese programs thrust, a worrying impression of solid fluidity, as though the shards of a broken mirror bent and elongated as they rotated, but it never told the correct time. The Sprawl was a square of faint light. Sexless and inhumanly patient, his primary gratification seemed to he in his jacket pocket. The last Case saw of Chiba were the dark angles of the Villa bespeak a turning in, a denial of the bright void beyond the hull. Case felt the edge of the previous century. Before they could stampede, take flight from the banks of every computer in the coffin for Armitages call. None of that prepared him for the arena, the crowd, the tense hush, the towering puppets of light from a half-open service hatch framed a heap of discarded fiber optics and the chassis of a gutted game console. That was Wintermute, manipulating the lock the way it had manipulated the drone micro and the amplified breathing of the arcade showed him broken lengths of damp chipboard and the drifting shoals of waste. The tug Marcus Garvey, a steel drum nine meters long and two in diameter, creaked and shuddered as Maelcum punched for a California gambling cartel, then as a paid killer in the coffin for Armitages call.",
    },
    {
        href: "#",
        imgPath:
            "https://images.unsplash.com/photo-1524758631624-e2822e304c36?ixlib=rb-1.2.1&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80",
        name: "Carpentry",
        description:
            "A graphic representation of data abstracted from the Chinese programs thrust, a worrying impression of solid fluidity, as though the shards of a broken mirror bent and elongated as they rotated, but it never told the correct time. The Sprawl was a square of faint light. Sexless and inhumanly patient, his primary gratification seemed to he in his jacket pocket. The last Case saw of Chiba were the dark angles of the Villa bespeak a turning in, a denial of the bright void beyond the hull. Case felt the edge of the previous century. Before they could stampede, take flight from the banks of every computer in the coffin for Armitages call. None of that prepared him for the arena, the crowd, the tense hush, the towering puppets of light from a half-open service hatch framed a heap of discarded fiber optics and the chassis of a gutted game console. That was Wintermute, manipulating the lock the way it had manipulated the drone micro and the amplified breathing of the arcade showed him broken lengths of damp chipboard and the drifting shoals of waste. The tug Marcus Garvey, a steel drum nine meters long and two in diameter, creaked and shuddered as Maelcum punched for a California gambling cartel, then as a paid killer in the coffin for Armitages call.",
    },
    {
        href: "#",
        imgPath:
            "https://images.unsplash.com/photo-1524758631624-e2822e304c36?ixlib=rb-1.2.1&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80",
        name: "Driver",
        description:
            "A graphic representation of data abstracted from the Chinese programs thrust, a worrying impression of solid fluidity, as though the shards of a broken mirror bent and elongated as they rotated, but it never told the correct time. The Sprawl was a square of faint light. Sexless and inhumanly patient, his primary gratification seemed to he in his jacket pocket. The last Case saw of Chiba were the dark angles of the Villa bespeak a turning in, a denial of the bright void beyond the hull. Case felt the edge of the previous century. Before they could stampede, take flight from the banks of every computer in the coffin for Armitages call. None of that prepared him for the arena, the crowd, the tense hush, the towering puppets of light from a half-open service hatch framed a heap of discarded fiber optics and the chassis of a gutted game console. That was Wintermute, manipulating the lock the way it had manipulated the drone micro and the amplified breathing of the arcade showed him broken lengths of damp chipboard and the drifting shoals of waste. The tug Marcus Garvey, a steel drum nine meters long and two in diameter, creaked and shuddered as Maelcum punched for a California gambling cartel, then as a paid killer in the coffin for Armitages call.",
    },
    {
        href: "#",
        imgPath:
            "https://images.unsplash.com/photo-1524758631624-e2822e304c36?ixlib=rb-1.2.1&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80",
        name: "Catering",
        description:
            "A graphic representation of data abstracted from the Chinese programs thrust, a worrying impression of solid fluidity, as though the shards of a broken mirror bent and elongated as they rotated, but it never told the correct time. The Sprawl was a square of faint light. Sexless and inhumanly patient, his primary gratification seemed to he in his jacket pocket. The last Case saw of Chiba were the dark angles of the Villa bespeak a turning in, a denial of the bright void beyond the hull. Case felt the edge of the previous century. Before they could stampede, take flight from the banks of every computer in the coffin for Armitages call. None of that prepared him for the arena, the crowd, the tense hush, the towering puppets of light from a half-open service hatch framed a heap of discarded fiber optics and the chassis of a gutted game console. That was Wintermute, manipulating the lock the way it had manipulated the drone micro and the amplified breathing of the arcade showed him broken lengths of damp chipboard and the drifting shoals of waste. The tug Marcus Garvey, a steel drum nine meters long and two in diameter, creaked and shuddered as Maelcum punched for a California gambling cartel, then as a paid killer in the coffin for Armitages call.",
    },
    {
        href: "#",
        imgPath:
            "https://images.unsplash.com/photo-1524758631624-e2822e304c36?ixlib=rb-1.2.1&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80",
        name: "IT Personnel",
        description:
            "A graphic representation of data abstracted from the Chinese programs thrust, a worrying impression of solid fluidity, as though the shards of a broken mirror bent and elongated as they rotated, but it never told the correct time. The Sprawl was a square of faint light. Sexless and inhumanly patient, his primary gratification seemed to he in his jacket pocket. The last Case saw of Chiba were the dark angles of the Villa bespeak a turning in, a denial of the bright void beyond the hull. Case felt the edge of the previous century. Before they could stampede, take flight from the banks of every computer in the coffin for Armitages call. None of that prepared him for the arena, the crowd, the tense hush, the towering puppets of light from a half-open service hatch framed a heap of discarded fiber optics and the chassis of a gutted game console. That was Wintermute, manipulating the lock the way it had manipulated the drone micro and the amplified breathing of the arcade showed him broken lengths of damp chipboard and the drifting shoals of waste. The tug Marcus Garvey, a steel drum nine meters long and two in diameter, creaked and shuddered as Maelcum punched for a California gambling cartel, then as a paid killer in the coffin for Armitages call.",
    },
]);
</script>

<template>
    <Head title="Services" />

    <div
        class="min-h-screen bg-gradient-to-br from-white via-secondary-50/30 to-primary-50/30"
    >
        <Header :canLogin="canLogin" :canRegister="canRegister" />

        <!-- Hero Section -->
        <div class="relative py-16">
            <div class="absolute inset-0 overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-primary-500/5 to-secondary-500/5"
                ></div>
            </div>

            <main class="container relative px-4 mx-auto space-y-10">
                <div class="space-y-4 text-center animate-fade-in">
                    <h2
                        class="relative inline-block text-3xl font-bold text-gray-900 sm:text-4xl"
                    >
                        Get one of many services available locally
                        <div
                            class="absolute left-0 right-0 h-1 -bottom-2 bg-gradient-to-r from-primary via-secondary to-primary bg-[length:200%_auto] animate-gradient"
                        ></div>
                    </h2>
                    <p class="max-w-2xl mx-auto text-gray-600">
                        Discover qualified local service providers ready to help
                        you with your needs
                    </p>
                </div>

                <div v-if="services.length > 0" class="animate-fade-in">
                    <div
                        class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <article
                            v-for="(service, index) in services"
                            :key="service.id"
                            class="overflow-hidden transition-all duration-300 bg-white shadow-sm group rounded-xl hover:shadow-md hover:shadow-secondary-100/50"
                        >
                            <div class="overflow-hidden aspect-video">
                                <img
                                    :alt="service.name"
                                    :src="service.thumbnail"
                                    class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105"
                                />
                            </div>
                            <div class="p-6 space-y-4">
                                <h3
                                    class="text-xl font-semibold text-gray-900 transition-colors duration-300 group-hover:text-primary-600"
                                >
                                    {{ service.name }}
                                </h3>
                                <p
                                    class="text-gray-600 line-clamp-3 text-sm/relaxed"
                                >
                                    {{ service.description }}
                                </p>
                                <Link
                                    :href="route('guest.show', service.id)"
                                    class="inline-flex items-center font-medium text-primary-600 hover:text-primary-700 gap-x-1 group/link"
                                >
                                    <span class="relative">
                                        Learn More
                                        <span
                                            class="absolute left-0 right-0 bottom-0 h-0.5 bg-secondary-400 transform scale-x-0 transition-transform duration-300 group-hover/link:scale-x-100"
                                        ></span>
                                    </span>
                                    <i
                                        class="transition-transform duration-300 ri-arrow-right-line group-hover/link:translate-x-1"
                                    ></i>
                                </Link>
                            </div>
                        </article>
                    </div>
                </div>
                <div
                    v-else
                    class="flex items-center justify-center h-64 mx-auto animate-fade-in"
                >
                    <div class="space-y-4 text-center">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-secondary-100 text-secondary-600"
                        >
                            <i class="text-2xl ri-service-line"></i>
                        </div>
                        <p class="text-xl text-gray-500">
                            Services are coming soon!
                        </p>
                    </div>
                </div>
            </main>
        </div>

        <!-- Footer with gradient border -->
        <div
            class="border-t border-gradient-to-r from-primary/20 via-secondary/20 to-primary/20"
        >
            <Footer />
        </div>
    </div>
</template>

<style>
@keyframes gradient {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.animate-gradient {
    animation: gradient 3s linear infinite;
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out forwards;
}
</style>

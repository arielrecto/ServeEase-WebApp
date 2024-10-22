<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const servicesOffered = ref([
    {
        href: '#',
        imgPath: 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?ixlib=rb-1.2.1&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80',
        name: 'Plumbing',
        description: 'A graphic representation of data abstracted from the Chinese programs thrust, a worrying impression of solid fluidity, as though the shards of a broken mirror bent and elongated as they rotated, but it never told the correct time. The Sprawl was a square of faint light. Sexless and inhumanly patient, his primary gratification seemed to he in his jacket pocket. The last Case saw of Chiba were the dark angles of the Villa bespeak a turning in, a denial of the bright void beyond the hull. Case felt the edge of the previous century. Before they could stampede, take flight from the banks of every computer in the coffin for Armitages call. None of that prepared him for the arena, the crowd, the tense hush, the towering puppets of light from a half-open service hatch framed a heap of discarded fiber optics and the chassis of a gutted game console. That was Wintermute, manipulating the lock the way it had manipulated the drone micro and the amplified breathing of the arcade showed him broken lengths of damp chipboard and the drifting shoals of waste. The tug Marcus Garvey, a steel drum nine meters long and two in diameter, creaked and shuddered as Maelcum punched for a California gambling cartel, then as a paid killer in the coffin for Armitages call.',
    },
    {
        href: '#',
        imgPath: 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?ixlib=rb-1.2.1&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80',
        name: 'Electrician',
        description: 'A graphic representation of data abstracted from the Chinese programs thrust, a worrying impression of solid fluidity, as though the shards of a broken mirror bent and elongated as they rotated, but it never told the correct time. The Sprawl was a square of faint light. Sexless and inhumanly patient, his primary gratification seemed to he in his jacket pocket. The last Case saw of Chiba were the dark angles of the Villa bespeak a turning in, a denial of the bright void beyond the hull. Case felt the edge of the previous century. Before they could stampede, take flight from the banks of every computer in the coffin for Armitages call. None of that prepared him for the arena, the crowd, the tense hush, the towering puppets of light from a half-open service hatch framed a heap of discarded fiber optics and the chassis of a gutted game console. That was Wintermute, manipulating the lock the way it had manipulated the drone micro and the amplified breathing of the arcade showed him broken lengths of damp chipboard and the drifting shoals of waste. The tug Marcus Garvey, a steel drum nine meters long and two in diameter, creaked and shuddered as Maelcum punched for a California gambling cartel, then as a paid killer in the coffin for Armitages call.',
    },
    {
        href: '#',
        imgPath: 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?ixlib=rb-1.2.1&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80',
        name: 'Carpentry',
        description: 'A graphic representation of data abstracted from the Chinese programs thrust, a worrying impression of solid fluidity, as though the shards of a broken mirror bent and elongated as they rotated, but it never told the correct time. The Sprawl was a square of faint light. Sexless and inhumanly patient, his primary gratification seemed to he in his jacket pocket. The last Case saw of Chiba were the dark angles of the Villa bespeak a turning in, a denial of the bright void beyond the hull. Case felt the edge of the previous century. Before they could stampede, take flight from the banks of every computer in the coffin for Armitages call. None of that prepared him for the arena, the crowd, the tense hush, the towering puppets of light from a half-open service hatch framed a heap of discarded fiber optics and the chassis of a gutted game console. That was Wintermute, manipulating the lock the way it had manipulated the drone micro and the amplified breathing of the arcade showed him broken lengths of damp chipboard and the drifting shoals of waste. The tug Marcus Garvey, a steel drum nine meters long and two in diameter, creaked and shuddered as Maelcum punched for a California gambling cartel, then as a paid killer in the coffin for Armitages call.',
    },
    {
        href: '#',
        imgPath: 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?ixlib=rb-1.2.1&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80',
        name: 'Driver',
        description: 'A graphic representation of data abstracted from the Chinese programs thrust, a worrying impression of solid fluidity, as though the shards of a broken mirror bent and elongated as they rotated, but it never told the correct time. The Sprawl was a square of faint light. Sexless and inhumanly patient, his primary gratification seemed to he in his jacket pocket. The last Case saw of Chiba were the dark angles of the Villa bespeak a turning in, a denial of the bright void beyond the hull. Case felt the edge of the previous century. Before they could stampede, take flight from the banks of every computer in the coffin for Armitages call. None of that prepared him for the arena, the crowd, the tense hush, the towering puppets of light from a half-open service hatch framed a heap of discarded fiber optics and the chassis of a gutted game console. That was Wintermute, manipulating the lock the way it had manipulated the drone micro and the amplified breathing of the arcade showed him broken lengths of damp chipboard and the drifting shoals of waste. The tug Marcus Garvey, a steel drum nine meters long and two in diameter, creaked and shuddered as Maelcum punched for a California gambling cartel, then as a paid killer in the coffin for Armitages call.',
    },
    {
        href: '#',
        imgPath: 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?ixlib=rb-1.2.1&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80',
        name: 'Catering',
        description: 'A graphic representation of data abstracted from the Chinese programs thrust, a worrying impression of solid fluidity, as though the shards of a broken mirror bent and elongated as they rotated, but it never told the correct time. The Sprawl was a square of faint light. Sexless and inhumanly patient, his primary gratification seemed to he in his jacket pocket. The last Case saw of Chiba were the dark angles of the Villa bespeak a turning in, a denial of the bright void beyond the hull. Case felt the edge of the previous century. Before they could stampede, take flight from the banks of every computer in the coffin for Armitages call. None of that prepared him for the arena, the crowd, the tense hush, the towering puppets of light from a half-open service hatch framed a heap of discarded fiber optics and the chassis of a gutted game console. That was Wintermute, manipulating the lock the way it had manipulated the drone micro and the amplified breathing of the arcade showed him broken lengths of damp chipboard and the drifting shoals of waste. The tug Marcus Garvey, a steel drum nine meters long and two in diameter, creaked and shuddered as Maelcum punched for a California gambling cartel, then as a paid killer in the coffin for Armitages call.',
    },
    {
        href: '#',
        imgPath: 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?ixlib=rb-1.2.1&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80',
        name: 'IT Personnel',
        description: 'A graphic representation of data abstracted from the Chinese programs thrust, a worrying impression of solid fluidity, as though the shards of a broken mirror bent and elongated as they rotated, but it never told the correct time. The Sprawl was a square of faint light. Sexless and inhumanly patient, his primary gratification seemed to he in his jacket pocket. The last Case saw of Chiba were the dark angles of the Villa bespeak a turning in, a denial of the bright void beyond the hull. Case felt the edge of the previous century. Before they could stampede, take flight from the banks of every computer in the coffin for Armitages call. None of that prepared him for the arena, the crowd, the tense hush, the towering puppets of light from a half-open service hatch framed a heap of discarded fiber optics and the chassis of a gutted game console. That was Wintermute, manipulating the lock the way it had manipulated the drone micro and the amplified breathing of the arcade showed him broken lengths of damp chipboard and the drifting shoals of waste. The tug Marcus Garvey, a steel drum nine meters long and two in diameter, creaked and shuddered as Maelcum punched for a California gambling cartel, then as a paid killer in the coffin for Armitages call.',
    },
])
</script>

<template>
    <section class="container py-8 mx-auto space-y-10">
        <h2 class="text-2xl font-bold text-center">Get one of many services available locally</h2>

        <div class="grid grid-cols-3 gap-10">
            <div v-for="(service, index) in servicesOffered" :key="index">
                <article class="overflow-hidden transition bg-white rounded-lg shadow hover:shadow-lg">
                    <img
                        alt=""
                        :src="service.imgPath"
                        class="object-cover w-full h-56"
                    />

                    <div class="p-4 bg-white sm:p-6">
                        <h3 class="mt-0.5 text-lg text-gray-900">{{ service.name }}</h3>

                        <p class="my-2 text-gray-500 line-clamp-3 text-sm/relaxed">
                            {{ service.description }}
                        <!-- Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae dolores, possimus
                        pariatur animi temporibus nesciunt praesentium dolore sed nulla ipsum eveniet corporis quidem,
                        mollitia itaque minus soluta, voluptates neque explicabo tempora nisi culpa eius atque
                        dignissimos. Molestias explicabo corporis voluptatem? -->
                        </p>
                        <Link :href="service.href" class="flex items-center text-primary gap-x-1"><span class="hover:underline decoration-2 underline-offset-4 ">Learn More</span><i class="ri-arrow-right-line"></i></Link>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>

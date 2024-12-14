<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue';


const props = defineProps({
    service : Object
});

</script>

<template>

    <Head title="Home" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Home</h2>
        </template>


        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 flex flex-col gap-5">
                <div class="bg-white rounded-lg p-5 shadow-lg flex flex-col gap-5">
                    <h1 class="text-4xl font-bold text-primary capitalize border-b w-full py-2">
                        {{ service.name }}
                    </h1>
                    <div class="flex justify-center">
                        <img :src="service.thumbnail" alt="" srcset="" class="w-1/2 aspect-auto object-center">
                    </div>


                    <div class="flex flex-col gap-2 border-t py-10">
                        <div class="flex justify-between">
                            <h1 class="text-xl font-bold capitalize">
                                Provider : {{ service.user.name }}
                            </h1>
                            <h1 class="text-xl font-bold capitalize">
                                Rate : ₱ {{ service.price }} / {{ service.price_type }}
                            </h1>
                        </div>

                        <h1>
                            Description
                        </h1>
                        <p class="min-h-32 bg-gray-50 p-2">
                            {{ service.description }}
                        </p>
                        <h1>
                            Terms & Conditions
                        </h1>
                        <p class="min-h-32 bg-gray-50 p-2">
                            {{ service.term_and_condition }}
                        </p>
                    </div>


                    <div class="flex justify-end">
                        <Link :href="route('customer.services.avail.create', service.id)" class="btn btn-primary">Avail</Link>
                    </div>
                </div>
            </div>
        </div>





    </AuthenticatedLayout>
</template>

<!-- <template>
    <h1>
        Customer dashboard
    </h1>


    <Link href="/logout" method="POST">Log out</Link>
</template> -->

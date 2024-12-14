<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue';


const page = usePage();

const authUser = ref(page.props.auth.user)

const isNotServiceProvider = computed(() => {
    return !authUser.value?.roles?.some(role => role.name === 'service provider');
});

const services = computed(() => page.props.services)


console.log(services.value)

</script>

<template>

    <Head title="Home" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Home</h2>
        </template>


        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 flex flex-col gap-5">
                <template v-if="isNotServiceProvider">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <Link :href="route('customer.service-provider.create')" class="button-primary">Service
                            Provider
                            Application</Link>
                        </div>
                    </div>
                </template>
                <div v-if="services.length > 0">
                    <div class="grid items-center grid-cols-1 gap-10 mb-4 overflow-y-auto sm:grid-cols-3">
                        <article v-for="(service, index) in services" :key="service.id"
                            class="overflow-hidden transition bg-white rounded-lg shadow hover:shadow-lg">
                            <img alt="" :src="service.thumbnail" class="object-cover w-full h-56" />
                            <div class="p-4 bg-white sm:p-6">
                                <div class="flex items-center justify-between">
                                    <h3 class="mt-0.5 text-lg text-gray-900">{{ service.name }}</h3>
                                    <p class="text-xs text-end">
                                        provider : {{ service.user.name }}
                                    </p>
                                </div>


                                <p class="my-2 text-gray-500 line-clamp-3 text-sm/relaxed">

                                    {{ service.description }}
                                </p>

                                <Link :href="route('customer.services.show', service.id)"
                                    class="flex items-center text-primary gap-x-1">
                                <span class="hover:underline decoration-2 underline-offset-4 ">Learn More</span><i
                                    class="ri-arrow-right-line"></i></Link>
                            </div>
                        </article>
                    </div>
                </div>
                <div v-else class="flex items-center justify-center h-64 mx-auto">
                    <p class="text-xl text-center text-gray-500">Services are coming soon!</p>
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

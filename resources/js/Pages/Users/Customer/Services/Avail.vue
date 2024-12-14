<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, usePage, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';


const props = defineProps({
    service: Object
});


const form = useForm({
    startDate : null,
    endDate : null,
    total : null,
    service : props.service.id,
    remark : null,
})


const submit = () => {
    form.post(route('customer.services.avail.store'), {
        onFinish: () => form.reset(),
    });
}
</script>

<template>

    <Head title="Home" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Home</h2>
        </template>


        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 flex flex-col gap-5">
                <div class="bg-white rounded-lg p-5 shadow-lg flex gap-5">
                    <div class="bg-gray-50 rounded-lg p-5 shadow-lg flex flex-col gap-2 w-1/3">
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
                        </div>
                    </div>
                    <form  @submit.prevent="submit" class="grow flex flex-col gap-2">
                        <div class="grid grid-cols-2 grid-flow-row gap-2">
                            <div>
                                <InputLabel for="name" value="Start Date" />

                                <TextInput id="name" type="date" class="block w-full mt-1" v-model="form.startDate" autofocus required />

                                <InputError class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="name" value="End Date" />

                                <TextInput id="name" type="date" v-model="form.endDate" class="block w-full mt-1" autofocus required />

                                <InputError class="mt-2" />
                            </div>
                        </div>
                        <div>
                                <InputLabel for="name" value="total" />

                                <TextInput id="name" type="number" v-model="form.total" class="block w-full mt-1" autofocus required />

                                <InputError class="mt-2" />
                            </div>

                            <div>
                                    <InputLabel for="description" value="Remark"  />

                                    <textarea id="description" v-model="form.remark"
                                        class="block h-32 w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary resize-none"
                                        required></textarea>

                                    <!-- <TextInput id="description" type="text" class="block w-full mt-1" v-model="form.description"
                                        required /> -->

                                    <InputError class="mt-2"  />
                                </div>




                        <div class="flex justify-end">
                            <button class="btn btn-primary">Avail</button>
                        </div>
                    </form>



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

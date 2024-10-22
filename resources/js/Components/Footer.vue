<script setup>
import moment from 'moment';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="bg-white">
        <div class="max-w-screen-xl px-4 py-8 mx-auto sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div class="flex justify-center sm:justify-start">
                    <Link href="/">
                        <p class="text-2xl font-black text-gray-900 uppercase">Serv<span class="text-primary">Ease</span></p>
                    </Link>
                </div>

                <p class="mt-4 text-sm text-center text-gray-500 lg:mt-0 lg:text-right">
                Copyright &copy; {{ moment().format('YYYY') }}. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</template>

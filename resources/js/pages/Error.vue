<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import type { User } from '@/types';

defineProps<{
    appName: string;
    user: User | null;
    statusCode: number;
    message: string;
}>();

const images: Record<number, string> = {
    400: "/assets/illustrations/4.svg",
    401: "/assets/illustrations/12.svg",
    403: "/assets/illustrations/5.svg",
    404: "/assets/illustrations/15.svg",
    405: "/assets/illustrations/14.svg",
    408: "/assets/illustrations/17.svg",
    429: "/assets/illustrations/2.svg",
    500: "/assets/illustrations/16.svg",
    502: "/assets/illustrations/16.svg",
    503: "/assets/illustrations/11.svg",
    504: "/assets/illustrations/17.svg",
};

</script>

<template>
    <Head :title="statusCode.toString()" />
    <div class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a] lg:justify-center lg:p-8">
        <div class="h-14.5 hidden lg:block"></div>
        <div class="flex w-full items-center justify-center opacity-100 lg:grow">
            <main class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row">
                <div class="flex-1 rounded-bl-lg rounded-br-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] lg:rounded-br-none lg:rounded-tl-lg lg:p-20">
                    <h1 class="text-xl font-medium">{{ $t(`http.${statusCode}`) }}</h1>
                    <h3 class="mb-1 text-sm font-light">{{ statusCode }}</h3>
                    <p class="my-2 text-[#706f6c] dark:text-[#A1A09A]" v-html="$t(`http_message.${statusCode}`)"></p>
                    <Link :href="route('dashboard')" class="inline-block rounded-sm border border-black bg-[#1b1b18] px-5 py-1.5 text-sm leading-normal text-white hover:border-black hover:bg-black dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:border-white dark:hover:bg-white mt-8">
                        {{ $t('action.back') }}
                    </Link>
                </div>
                <div class="relative -mb-px aspect-335/376 flex items-center justify-center w-full shrink-0 overflow-hidden rounded-t-lg bg-gray-100 lg:-ml-px lg:mb-0 lg:aspect-auto lg:w-[438px] lg:rounded-r-lg lg:rounded-t-none">
                    <img :src="images[statusCode] ?? `/assets/illustrations/16.svg`" width="40%" :alt="appName">
                </div>
            </main>
        </div>
        <div class="h-14.5 hidden lg:block"></div>
    </div>
</template>

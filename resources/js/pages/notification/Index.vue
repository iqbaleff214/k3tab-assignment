<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Paginate, Notification } from '@/types';
import { Head } from '@inertiajs/vue3';
import { timeAgo } from '@/lib/utils';
import { useI18n } from 'vue-i18n';
import { useNotificationStore } from '@/store/notification';
import { onMounted } from 'vue';

defineProps<{ items: Paginate<Notification>; }>();

const { t, locale } = useI18n();
const { setUnreadNotificationCount } = useNotificationStore();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.notification',
        href: route('notification.index'),
    },
];

onMounted(() => {
    setUnreadNotificationCount(0);
});
</script>

<template>
    <Head :title="t('menu.notification')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
            <div class="flex flex-col w-full text-sm" v-if="items.data.length">
                <a :href="item.data.link ?? '#'"
                    class="flex items-start gap-x-5 group w-full min-h-[40px] border-b-1 border-b-gray-200 hover:bg-gray-50 p-4"
                    :class="{ 'bg-teal-50 hover:bg-teal-100': item.read_at === null }"
                    v-for="item in items.data" :key="item.id">
                    <div><i class="pi pi-bell mt-2 text-gray-500"></i></div>
                    <div class="flex-1 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <h3 class="font-semibold">{{ item.data.title }}</h3>
                            <small class="text-gray-500">{{ timeAgo(item.created_at, locale) }}</small>
                        </div>
                        <p v-html="item.data.message"></p>
                    </div>
                </a>
            </div>
        </div>
    </AppLayout>
</template>

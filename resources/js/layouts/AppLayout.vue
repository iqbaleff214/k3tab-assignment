<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType, NotificationData, SharedData } from '@/types';
import { onMounted, onUnmounted, ref, watchEffect } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast, Toast } from 'primevue';
import { useI18n } from 'vue-i18n';
import { echo } from '@/echo';
import { useNotificationStore } from '@/store/notification';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage<SharedData>();
const toast = useToast();
const audioNotification = new Audio('/assets/sounds/livechat-129007.mp3');

const timeOutRef = ref<number|null>(null);
const { locale, t } = useI18n();
const {
    setUnreadNotificationCount,
    addUnreadNotificationCount,
} = useNotificationStore();

watchEffect( () => {
    const flash = page.props.flash;
    timeOutRef.value = setTimeout(() => {
        if (flash?.success) {
            toast.add({
                severity: 'success',
                summary: t('label.success'),
                detail: flash?.success,
                life: 3000,
            });
        } else if (flash?.error) {
            toast.add({
                severity: 'error',
                summary: t('label.fail'),
                detail: flash?.error,
                life: 3000,
            });
        }
    }, 50);
});

onMounted(() => {
    locale.value = page.props.auth.user.locale;
    setUnreadNotificationCount(page.props.unreadNotification);
    echo.private(`App.Models.User.${page.props.auth.user.id}`)
        .notification((response: NotificationData) => {
            audioNotification.play().catch(err => {
                console.log(err);
            }).finally(() => {
                addUnreadNotificationCount();
                toast.add({
                    severity: 'info',
                    summary: response.title,
                    detail: response.message.replace(/<[^>]*>/g, ''),
                    life: 3000,
                });
            });
        });
});

onUnmounted(() => {
    if (timeOutRef.value !== null) {
        clearTimeout(timeOutRef.value);
    }
    echo.leave(`App.Models.User.${page.props.auth.user.id}`);
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Toast />
        <slot />
    </AppLayout>
</template>

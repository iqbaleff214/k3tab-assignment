<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType, SharedData } from '@/types';
import { onMounted, onUnmounted, ref, watchEffect } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast, Toast } from 'primevue';
import { useI18n } from 'vue-i18n';
import { echo } from '@/echo';

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
    echo.private('jokowi')
        .listen("HidupJokowi", (response: any) => {
            audioNotification.play().catch(err => {
                console.log(err);
            }).finally(() => {
                console.log(response);
            });
        });
});

onUnmounted(() => {
    if (timeOutRef.value !== null) {
        clearTimeout(timeOutRef.value);
    }
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Toast />
        <slot />
    </AppLayout>
</template>

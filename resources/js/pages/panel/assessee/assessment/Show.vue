<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.assessment',
        href: route('assessee.assessment.index'),
    },
];

const { t } = useI18n();
</script>

<template>
    <Head :title="t('menu.assessment')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

        </div>
    </AppLayout>
</template>

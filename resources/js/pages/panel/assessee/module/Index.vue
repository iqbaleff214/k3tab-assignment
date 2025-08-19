<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, FilterColumn, Module } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { Card, InputText } from 'primevue';
import Filter from '@/components/Filter.vue';
import { dateHumanSmartFormat, parseUserAgent } from '@/lib/utils';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.dashboard',
        href: route('dashboard'),
    },
];

const { t } = useI18n();

defineProps<{
    items: Module[];
}>();

const filterForm = useForm<{ [key: string]: any; filters: Record<string, FilterColumn> }>({
    filters: {
        code: {
            value: null,
            matchMode: 'contains',
            label: 'field.code',
            canChange: true,
        },
        title: {
            value: null,
            matchMode: 'contains',
            label: 'field.title',
            canChange: true,
        },
        slug: {
            value: null,
            matchMode: 'contains',
            label: 'field.slug',
            canChange: true,
        },
        minimum_score: {
            value: null,
            matchMode: 'equals',
            label: 'field.minimum_score',
            canChange: true,
        },
        created_at: {
            value: null,
            matchMode: 'dateBetween',
            label: 'field.created_at',
            canChange: true,
        },
    }
});

const searchByCode = (value: string | undefined): void => {
    const url = new URL(window.location.href);
    url.search = '';
    filterForm.get(url.toString(), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <Head :title="t('menu.dashboard')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <header class="flex items-center justify-between">
                <div>
                    <h3 class="mb-0.5 text-base font-medium">{{ $t('menu.module') }}</h3>
                    <p class="text-sm text-muted-foreground">{{ $t('label.menu_subtitle', { menu: $t('menu.module') }) }}</p>
                </div>
                <div class="flex gap-x-2">
                    <div>
                        <InputText
                            placeholder="Search by code" :fluid="true" v-model="filterForm.filters.code.value" size="small"
                            type="text" autocomplete="off" @update:model-value="searchByCode" />
                    </div>
                    <Filter :form="filterForm" />
                </div>
            </header>


            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div v-for="item in items" :key="item.id">
                    <Card>
                        <template #title>
                            <div class="w-full flex flex-col gap-0">
                                <div class="flex justify-between gap-x-2 items-center">
                                    <h3 class="text-md text-amber-500 hover:underline cursor-pointer">{{ item.code }}</h3>
                                    <small class="text-sm font-light text-gray-500" :title="t('field.read_at')" v-if="item.assessees?.length">
                                        {{ dateHumanSmartFormat(item.assessees?.[0]?.read_at ?? '') }}
                                    </small>
                                </div>
                                <small :title="item.title" class="w-full line-clamp-1 font-light text-gray-500 text-sm">{{ item.title }}</small>
                            </div>
                        </template>
                        <template #content>
                            <hr>
                            <div class="flex flex-col gap-y-1 my-4">
                                <div class="grid lg:grid-cols-2 gap-2.5 text-sm">
                                    <div class="capitalize text-gray-500 ">{{ t('field.duration_estimation') }}</div>
                                    <div class="lg:text-end font-medium">{{ t('label.n_minute', { n: item.duration_estimation }) }}</div>
                                </div>
                                <div class="grid lg:grid-cols-2 gap-2.5 text-sm">
                                    <div class="capitalize text-gray-500 ">{{ t('field.questions_count') }}</div>
                                    <div class="lg:text-end font-medium">{{ t('label.n_question', { n: item.questions_count }) }}</div>
                                </div>
                                <div class="grid lg:grid-cols-2 gap-2.5 text-sm">
                                    <div class="capitalize text-gray-500 ">{{ t('field.minimum_score') }}</div>
                                    <div class="lg:text-end font-medium">{{ item.minimum_score }}</div>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

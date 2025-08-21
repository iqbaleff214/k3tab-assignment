<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, FilterColumn, Module } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';
import { Card, InputText, Button } from 'primevue';
import Filter from '@/components/Filter.vue';
import PostTestModal from '@/pages/panel/assessee/module/test/PostTestModal.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.module',
        href: route('assessee.module.index'),
    },
];

const { t } = useI18n();
const testModal = ref();

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
                                    <Link class="text-md text-amber-500 hover:underline cursor-pointer" :href="route('assessee.module.show', item.id)" :title="t('label.view_detail')">
                                        {{ item.code }}
                                    </Link>
                                    <span class="bg-gray-100 px-2 py-0.5 text-xs rounded-lg" v-if="item.assessees?.length">{{ t(`label.${item.assessees?.[0]?.status}`) }}</span>
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
                                    <div class="lg:text-end font-medium">{{ t('label.n_question', { n: item.available_questions_count }) }}</div>
                                </div>
                                <div class="grid lg:grid-cols-2 gap-2.5 text-sm">
                                    <div class="capitalize text-gray-500 ">{{ t('field.minimum_score') }}</div>
                                    <div class="lg:text-end font-medium">{{ item.minimum_score }}</div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-1">
                                <Button
                                    v-if="item.assessees?.length && item.assessees?.[0]?.status !== 'competent' && item.available_questions_count > 0"
                                    size="small" :disabled="!item.assessees?.length" :variant="item.assessees?.[0]?.is_doing_test ? 'outlined' : ''"
                                    @click="() => testModal?.open(item)"
                                    :label="t(item.assessees?.[0]?.is_doing_test ? 'action.resume_the_test' : (item.assessees?.[0]?.status === 'not_competent' ? 'action.retake_the_test' : 'action.start_the_test'))" />
                                <small v-if="!item.assessees?.length && item.available_questions_count > 0" class="text-gray-500">{{ t('label.you_have_not_accessed_the_module') }}</small>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>

            <PostTestModal ref="testModal" />
        </div>
    </AppLayout>
</template>

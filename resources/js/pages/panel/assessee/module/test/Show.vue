<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Module, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { dateHumanFormatWithTime, dateHumanSmartFormat, formatBytes } from '@/lib/utils';
import { Button } from 'primevue';
import axios from 'axios';

const props = defineProps<{
    item: Module;
}>();
const page = usePage<SharedData>();
const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.module',
        href: route('assessee.module.index'),
    },
    {
        title: props.item.code ?? props.item.title,
        href: route('assessee.module.show', props.item.id),
    },
];
</script>

<template>
    <Head :title="t('menu.module')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex justify-between items-baseline">
                <div class="flex flex-col items-baseline gap-2.5">
                    <h1 class="text-2xl">{{ item.title }}</h1>
                    <div class="flex gap-2 text-sm font-medium text-gray-500">
                        <span v-tooltip.bottom="t('label.duration_estimation')">{{ t('label.n_minute', { n: item.duration_estimation }) }}</span>
                        <span>•</span>
                        <span v-tooltip.bottom="t('label.total_question')">{{ t('label.n_question', { n: item.questions_count }) }}</span>
                        <span>•</span>
                        <span v-tooltip.bottom="t('label.minimum_score')"> <i class="pi pi-star-fill text-amber-500" style="font-size: 0.75rem"></i> ≥{{ item.minimum_score }}</span>
                    </div>
                    <div class="text-sm font-light text-gray-500" v-html="item.description"></div>
                </div>
                <span
                    v-tooltip="t('label.created_at', { date: dateHumanFormatWithTime(item.created_at, 0, page.props.auth.user.locale) })"
                    class="text-sm text-gray-500">
                    {{ dateHumanSmartFormat(item.created_at, page.props.auth.user.locale) }}
                </span>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 flex flex-col gap-6">
                    <div>
                        <h3 class="font-medium text-amber-600">{{ t('field.equipment_required') }}</h3>
                        <p class="wrap-break-word text-black" :class="{ 'select-none blur-sm': item.assessees?.[0]?.is_doing_test }" v-html="item.equipment_required ?? '-'"></p>
                    </div>
                    <div>
                        <h3 class="font-medium text-amber-600">{{ t('field.procedure') }}</h3>
                        <p class="wrap-break-word text-black" :class="{ 'select-none blur-sm': item.assessees?.[0]?.is_doing_test }" v-html="item.procedure ?? '-'"></p>
                    </div>
                    <div>
                        <h3 class="font-medium text-amber-600">{{ t('field.reference') }}</h3>
                        <p class="wrap-break-word text-black" :class="{ 'select-none blur-sm': item.assessees?.[0]?.is_doing_test }" v-html="item.reference ?? '-'"></p>
                    </div>
                    <div>
                        <h3 class="font-medium text-amber-600">{{ t('field.performance') }}</h3>
                        <p class="wrap-break-word text-black" :class="{ 'select-none blur-sm': item.assessees?.[0]?.is_doing_test }" v-html="item.performance ?? '-'"></p>
                    </div>
                    <div>
                        <div class="flex justify-between items-baseline mb-2">
                            <h3 class="font-medium text-amber-600">{{ t('field.media') }} <span class="text-xs text-gray-400">{{ item.media?.length ?? 0 }}</span></h3>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            <div v-for="media in item.media" :key="media.path_url" class="relative group w-full h-36 rounded-md overflow-hidden border-gray-100 border-1"
                                 :class="{ 'bg-center bg-cover bg-no-repeat shadow-md': !media.path_url.endsWith('pdf'), 'bg-white shadow-md flex items-center justify-center': media.path_url.endsWith('pdf') }"
                                 :style="{ backgroundImage: !media.path_url.endsWith('pdf') ? `url('${media.path_url}')` : 'none' }">
                                <i class="pi pi-file-pdf text-gray-300" v-if="media.path_url.endsWith('pdf')" style="font-size: 2rem"></i>
                                <div class="absolute inset-0 backdrop-blur-sm bg-white/50 flex flex-col items-start justify-between p-4 text-gray-800 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 space-y-2">
                                    <div class="text-start w-full" :class="{ 'text-gray-500': item.assessees?.[0]?.downloaded_files?.includes(media.path_url) }">
                                        <p :title="media.filename" class="break-words font-medium line-clamp-3">
                                            {{ media.filename }}
                                        </p>
                                        <p class="text-xs font-light">{{ formatBytes(media.size) }}</p>
                                    </div>

                                    <div class="flex gap-2 justify-end w-full">
                                        <Button
                                            v-tooltip="t('action.download')" icon="pi pi-download" size="small" variant="text" severity="secondary"
                                            :disabled="item.assessees?.[0]?.is_doing_test" @click="() => downloadFile(media.id, media.filename)" rounded></Button>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500" v-if="!item.media?.length">{{ t('label.no_data_available', { data: t('field.media') }) }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-6">
                    <div>
                        <div class="flex justify-between items-baseline mb-2">
                            <h3 class="font-medium text-amber-600">{{ t('label.evaluation') }}</h3>
                        </div>
                        <div class="flex flex-col gap-2">
                            <p class="text-sm text-gray-500">{{ t('label.you_have_not_taken_post_test') }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

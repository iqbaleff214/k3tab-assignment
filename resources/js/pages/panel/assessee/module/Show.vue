<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PostTestModal from '@/pages/panel/assessee/module/test/PostTestModal.vue';
import type { BreadcrumbItem, Module, PostTest, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { formatBytes, isHttpUrl } from '@/lib/utils';
import { Button, Image } from 'primevue';
import axios from 'axios';
import { ref } from 'vue';

interface Props {
    item: Module;
    result_histories: PostTest[];
}

const props = defineProps<Props>();
const page = usePage<SharedData>();
const { t } = useI18n();
const testModal = ref();

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

const downloadFile = (id: number, filename: string) => {
    axios.post(route('assessee.module.download', { module: props.item.id, media: id }), {
        filename,
    }).then((response) => {
        const a = document.createElement('a');
        a.href = window.URL.createObjectURL(new Blob([response.data]));
        a.download = filename;
        a.click();
        a.remove();
    });
};

</script>

<template>
    <Head :title="t('menu.module')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex justify-between items-baseline">
                <div class="flex flex-col items-start gap-2.5">
                    <h1 class="text-2xl">{{ item.title }}</h1>
                    <div class="flex gap-2 text-sm font-medium text-gray-500">
                        <span v-tooltip.bottom="t('label.duration_estimation')">{{ t('label.n_minute', { n: item.duration_estimation }) }}</span>
                        <span>•</span>
                        <span v-tooltip.bottom="t('label.total_question')">{{ t('label.n_question', { n: item.available_questions_count }) }}</span>
                        <span>•</span>
                        <span v-tooltip.bottom="t('label.minimum_score')"> <i class="pi pi-star-fill text-amber-500" style="font-size: 0.75rem"></i> ≥{{ item.minimum_score }}</span>
                    </div>
                    <div class="text-sm font-light text-gray-500" v-html="item.description"></div>
                </div>
                <div class="flex flex-col items-end gap-2.5">
                    <div class="flex gap-x-2 items-center">
                        <span class="bg-gray-100 px-2 py-0.5 text-xs rounded-md" v-if="item.assessees?.length">{{ t(`label.${item.assessees?.[0]?.status}`) }}</span>
                        <Button
                            v-if="(item.assessees?.length || item.assessees?.[0]?.status !== 'competent') && item.available_questions_count > 0"
                            v-tooltip.bottom="t(item.assessees?.[0]?.is_doing_test ? 'action.resume_the_test' : 'action.start_the_test')"
                            :icon="item.assessees?.[0]?.is_doing_test ? 'pi pi-replay' : 'pi pi-play'" size="small" variant="text" severity="secondary"
                            @click="() => testModal?.open(item)" rounded />
                    </div>
                </div>
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
                        <div class="flex justify-between items-end mb-2">
                            <h3 class="font-medium text-amber-600">{{ t('label.post_test_result') }}</h3>
                            <div class="flex flex-col items-end">
                                <small class="font-light text-gray-500">{{ t('label.latest_score') }}</small>
                                <h3 class="font-medium">{{ result_histories?.[0]?.score }}</h3>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <template v-if="result_histories?.[0]?.answers">
                                <div v-for="(answer, index) in result_histories?.[0]?.answers"
                                     :class="{ '': item.assessees?.[0]?.is_doing_test, 'select-none blur-sm': item.assessees?.[0]?.is_doing_test }"
                                     :key="index" class="flex flex-col gap-1.5 bg-slate-50 rounded-xl py-4 px-4">
                                    <div class="flex justify-between items-start">
                                        <div class="text-sm font-medium flex-1" v-if="!isHttpUrl(answer.question)">{{ answer.question }}</div>
                                        <div v-else>
                                            <Image :src="answer.question" preview :alt="answer.question" class="w-16 h-16 rounded-full mt-2" />
                                        </div>
                                        <div class="flex flex-col justify-end gap-1">
                                            <span class="px-2 py-0.5 text-xs rounded-lg" :class="{ 'bg-green-100 text-green-600': answer.is_correct, 'bg-red-100 text-red-600': !answer.is_correct }">
                                                {{ t( answer.is_correct ? 'label.correct' : 'label.incorrect' ) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <small class="text-xs text-gray-500">{{ $t('field.answer') }}</small>
                                        <div class="my-0 py-0">
                                            <p class="text-sm" v-if="!isHttpUrl(answer.answer ?? '')">{{ answer.answer ?? '' }}</p>
                                            <div v-else>
                                                <Image :src="answer.answer ?? ''" preview :alt="answer.answer ?? ''" class="w-16 h-16 rounded-full mt-2" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <p class="text-sm text-gray-500" v-else>{{ t('label.you_have_not_taken_post_test') }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <PostTestModal ref="testModal" />
    </AppLayout>
</template>

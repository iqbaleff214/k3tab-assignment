<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import FormModal from '@/pages/module/Index/FormModal.vue';
import QuestionModal from '@/pages/module/question/FormModal.vue';
import MediaModal from '@/pages/module/Index/UploadModal.vue';
import type { BreadcrumbItem, Media, Module, Question, SharedData } from '@/types';
import { Head, usePage, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useConfirm, ConfirmPopup, Button, Image } from 'primevue';
import { ref } from 'vue';
import { dateHumanFormatWithTime, dateHumanSmartFormat, downloadFile, formatBytes, isHttpUrl } from '@/lib/utils';

interface Props {
    item: Module;
    next: number | null;
    prev: number | null;
    total: number;
    index: number;
}

const props = defineProps<Props>();
const page = usePage<SharedData>();
const confirm = useConfirm();
const modal = ref();
const questionModal = ref();
const mediaModal = ref();
const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.module',
        href: route('admin.module.index'),
    },
    {
        title: props.item.title,
        href: route('admin.module.show', props.item.id),
    },
];

const destroy = (event: MouseEvent, item: Module) => {
    confirm.require({
        target: event.currentTarget as HTMLElement,
        message: t('label.are_you_sure_delete'),
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: t('action.cancel'),
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: t('action.delete'),
            severity: 'danger',
        },
        accept: () => {
            router.delete(route('admin.module.destroy', {
                module: item.id, next: props.next, prev: props.prev,
            }), {
                preserveScroll: true,
                preserveState: true,
            });
        },
    });
};

const destroyQuestion = (event: MouseEvent, item: Module, question: Question) => {
    confirm.require({
        target: event.currentTarget as HTMLElement,
        message: t('label.are_you_sure_delete'),
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: t('action.cancel'),
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: t('action.delete'),
            severity: 'danger',
        },
        accept: () => {
            router.delete(route('admin.module.question.destroy', {
                module: item.id, question: question.id,
            }), {
                preserveScroll: true,
                preserveState: true,
            });
        },
    });
};

const destroyMedia = (event: MouseEvent, item: Module, media: Media) => {
    confirm.require({
        target: event.currentTarget as HTMLElement,
        message: t('label.are_you_sure_delete'),
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: t('action.cancel'),
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: t('action.delete'),
            severity: 'danger',
        },
        accept: () => {
            router.delete(route('admin.module.remove-file', {
                module: item.id, media: media.id,
            }), {
                preserveScroll: true,
                preserveState: true,
            });
        },
    });
};
</script>

<template>
    <Head :title="t('menu.module')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex justify-between">
                <div class="flex gap-x-1 items-center">
                    <Link :href="route('admin.module.index')">
                        <Button
                            v-tooltip.bottom="t('action.back')"
                            icon="pi pi-arrow-left" size="small" variant="text" severity="secondary"
                            rounded></Button>
                    </Link>
                    <Button
                        v-tooltip.bottom="t('action.delete')"
                        icon="pi pi-trash" size="small" variant="text" severity="danger"
                        @click="destroy($event, item)" rounded></Button>
                    <span class="text-gray-300">|</span>
                </div>

                <div class="flex gap-x-1 items-center">
                    <div class="text-sm text-gray-500">{{ t('label.show_of', { index, total }) }}</div>
                    <Link :href="route('admin.module.show', prev ?? '')" :disabled="prev === null">
                        <Button
                            v-tooltip.bottom="t('label.older')"
                            :disabled="prev === null"
                            icon="pi pi-chevron-left" size="small" variant="text" severity="secondary"
                             rounded></Button>
                    </Link>
                    <Link :href="route('admin.module.show', next ?? '')" :disabled="next === null">
                        <Button
                            v-tooltip.bottom="t('label.newer')"
                            :disabled="next === null"
                            icon="pi pi-chevron-right" size="small" variant="text" severity="secondary"
                             rounded></Button>
                    </Link>
                    <Button
                        v-tooltip.bottom="t('action.edit')"
                        icon="pi pi-pencil" size="small" variant="text" severity="secondary"
                        @click="() => modal?.open(item)" rounded></Button>
                </div>
            </div>

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
                        <p class="wrap-break-word text-black" v-html="item.equipment_required ?? '-'"></p>
                    </div>
                    <div>
                        <h3 class="font-medium text-amber-600">{{ t('field.procedure') }}</h3>
                        <p class="wrap-break-word text-black" v-html="item.procedure ?? '-'"></p>
                    </div>
                    <div>
                        <h3 class="font-medium text-amber-600">{{ t('field.reference') }}</h3>
                        <p class="wrap-break-word text-black" v-html="item.reference ?? '-'"></p>
                    </div>
                    <div>
                        <h3 class="font-medium text-amber-600">{{ t('field.performance') }}</h3>
                        <p class="wrap-break-word text-black" v-html="item.performance ?? '-'"></p>
                    </div>
                </div>
                <div class="flex flex-col gap-6">
                    <div>
                        <div class="flex justify-between items-baseline mb-2">
                            <h3 class="font-medium text-amber-600">{{ t('field.media') }} <span class="text-xs text-gray-400">{{ item.media?.length ?? 0 }}</span></h3>
                            <Button
                                v-tooltip.bottom="t('action.upload')"
                                icon="pi pi-cloud-upload" size="small" variant="text" severity="secondary"
                                @click="() => mediaModal?.open(item)" rounded></Button>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="media in item.media" :key="media.path_url" class="relative group w-full h-36 rounded-md overflow-hidden border-gray-100 border-1"
                                 :class="{ 'bg-center bg-cover bg-no-repeat shadow-md': !media.path_url.endsWith('pdf'), 'bg-white shadow-md flex items-center justify-center': media.path_url.endsWith('pdf') }"
                                 :style="{ backgroundImage: !media.path_url.endsWith('pdf') ? `url('${media.path_url}')` : 'none' }">
                                <i class="pi pi-file-pdf text-gray-300" v-if="media.path_url.endsWith('pdf')" style="font-size: 2rem"></i>
                                <div class="absolute inset-0 backdrop-blur-sm bg-white/50 flex flex-col items-start justify-between p-4 text-gray-800 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 space-y-2">
                                    <div class="text-start w-full">
                                        <p class="break-words font-medium line-clamp-3" :title="media.filename">{{ media.filename }}</p>
                                        <p class="text-xs font-light">{{ formatBytes(media.size) }}</p>
                                    </div>

                                    <div class="flex gap-2 justify-end w-full">
                                        <Button
                                            v-tooltip="$t('action.download')" icon="pi pi-download" size="small" variant="text" severity="secondary"
                                            @click="() => downloadFile(media.path_url, media.filename)" rounded></Button>
                                        <Button
                                            v-tooltip="$t('action.delete')" icon="pi pi-trash" size="small" variant="text" severity="danger"
                                            @click="destroyMedia($event, item, media)" rounded></Button>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500" v-if="!item.media?.length">{{ t('label.no_data_available', { data: t('field.media') }) }}</p>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-baseline mb-2">
                            <h3 class="font-medium text-amber-600">{{ t('field.questions') }} <span class="text-xs text-gray-400">{{ item.questions?.length ?? 0 }}</span></h3>
                            <Button
                                v-tooltip.bottom="t('action.new_menu', { menu: t('field.question') })"
                                icon="pi pi-plus" size="small" variant="text" severity="secondary"
                                @click="() => questionModal?.open(item)" rounded></Button>
                        </div>
                        <div class="flex flex-col gap-2">
                            <div v-for="question in item.questions" :key="question.id" class="flex flex-col gap-1.5 bg-slate-50 rounded-xl py-2 px-4">
                                <div class="flex justify-between items-center">
                                    <div class="text-sm font-medium flex-1" v-if="!isHttpUrl(question.question)">{{ question.question }}</div>
                                    <div v-else>
                                        <Image :src="question.question" preview :alt="question.title" class="w-16 h-16 rounded-full mt-2" />
                                    </div>
                                    <div class="flex">
                                        <Button
                                            v-tooltip.bottom="t('action.edit')"
                                            icon="pi pi-pencil" size="small" variant="text" severity="secondary"
                                            @click="() => questionModal?.open(item, question)" rounded></Button>
                                        <Button
                                            v-tooltip.bottom="t('action.delete')"
                                            icon="pi pi-trash" size="small" variant="text" severity="danger"
                                            @click="destroyQuestion($event, item, question)" rounded></Button>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <small class="text-xs text-gray-500">{{ $t('field.answer') }}</small>
                                    <div class="ms-2 my-0 py-0">
                                        <p class="text-sm" v-if="!isHttpUrl(question.choices[question.correct_answer_index])">{{ question.choices[question.correct_answer_index] }}</p>
                                        <div v-else>
                                            <Image :src="question.choices[question.correct_answer_index]" preview :alt="question.title" class="w-16 h-16 rounded-full mt-2" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500" v-if="!item.questions?.length">{{ t('label.no_data_available', { data: t('field.questions') }) }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <ConfirmPopup />

        <FormModal ref="modal" />
        <QuestionModal ref="questionModal" />
        <MediaModal ref="mediaModal" />
    </AppLayout>
</template>

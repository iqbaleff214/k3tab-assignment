<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import type { ApprovalFlow, BreadcrumbItem, SharedData } from '@/types';

import FormModal from '@/pages/configuration/ApprovalFlow/FormModal.vue';
import PreviewModal from '@/pages/configuration/ApprovalFlow/PreviewModal.vue';

import AppLayout from '@/layouts/AppLayout.vue';
import ConfigLayout from '@/layouts/ConfigurationLayout.vue';
import { Button, Card, ConfirmPopup, useConfirm } from 'primevue';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';
import { getDepth } from '@/lib/utils';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'menu.approval_flow',
        href: route('configuration.approval-flow.index'),
    },
];

defineProps<{ items: ApprovalFlow[]; subjects: string[]; }>();

const page = usePage<SharedData>();
const confirm = useConfirm();
const { t } = useI18n();

const formModal = ref();
const previewModal = ref();

const destroy = (event: MouseEvent, item: ApprovalFlow) => {
    confirm.require({
        target: event.currentTarget as HTMLElement,
        message: t('label.are_you_sure_delete'),
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: t('action.cancel'),
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: t('action.delete'),
            severity: 'danger',
        },
        accept: () => {
            router.delete(route('configuration.approval-flow.destroy', item.id), {
                preserveScroll: true,
                preserveState: true,
            });
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('menu.approval_flow')" />

        <ConfigLayout>
            <div class="space-y-6">
                <HeadingSmall :title="$t('menu.approval_flow')" :description="$t('label.approval_flow_subtitle')" />

                <div class="grid gap-4 md:grid-cols-2">
                    <div v-for="item in items" :key="item.id">
                        <Card>
                            <template #title>
                                <div class="flex justify-between gap-5">
                                    <div class="flex flex-col gap-1">
                                        <div class="font-semibold wrap-normal">{{ $t(`menu.${item.subject}`) }}</div>
                                        <small class="text-gray-500 font-light text-sm">{{ $t('label.need_n_approval', { total: getDepth(item, 'children') }) }}</small>
                                    </div>
                                    <div class="flex gap-1">
                                        <Button
                                            icon="pi pi-eye"
                                            size="small"
                                            variant="outlined"
                                            @click="() => previewModal?.open(item)" v-if="page.props.auth.allow.view_approval_flow"
                                            rounded></Button>
                                        <Button
                                            icon="pi pi-trash"
                                            size="small"
                                            variant="outlined"
                                            severity="danger"
                                            @click="destroy($event, item)" v-if="page.props.auth.allow.delete_approval_flow"
                                            rounded></Button>
                                    </div>
                                </div>
                            </template>
                            <template #content>
                                <div class="flex justify-between mt-5">

                                </div>
                            </template>
                        </Card>
                    </div>
                    <button
                        @click="() => formModal?.open(null, null, null)" v-if="page.props.auth.allow.add_approval_flow"
                        class="min-h-[122px] h-full cursor-pointer rounded-xl bg-white shadow-sm hover:bg-gray-50">
                        <i class="pi pi-plus text-gray-500" style="font-size: 1.25rem"></i>
                    </button>
                </div>

                <ConfirmPopup />

                <FormModal ref="formModal" :subjects />
                <PreviewModal ref="previewModal" />
            </div>
        </ConfigLayout>
    </AppLayout>
</template>

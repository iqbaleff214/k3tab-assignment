<script setup lang="ts">
import { ref } from 'vue';
import type { APIResponse, ApprovalFlow, SharedData } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

import { Button, Dialog, OrganizationChart, useConfirm, ConfirmPopup, Popover } from 'primevue';
import FormModal from '@/pages/configuration/ApprovalFlow/FormModal.vue';

const confirm = useConfirm();
const { t } = useI18n();
const visible = ref<boolean>(false);
const item = ref<ApprovalFlow>();
const selectedNode = ref<ApprovalFlow>();
const page = usePage<SharedData>();
const formModal = ref();
const menuPopup = ref();

const open = (i: ApprovalFlow) => {
    visible.value = true;
    item.value = i;
};

const close = () => {
    visible.value = false
    item.value = undefined;
};

const reload = () => {
    if (!item.value)
        return;
    fetch(route('json.approval-flow.show', item.value.id))
        .then(res => res.json())
        .then((res: APIResponse<ApprovalFlow>) => {
            if (res.error || !res.data)
                return;

            item.value = res.data;
        });
};

const destroy = (event: MouseEvent, item?: ApprovalFlow) => {
    if (!item)
        return;

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
            router.delete(route('configuration.approval-flow.destroy', item.id), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: reload,
            });
        },
    });
};

const togglePopup = (e: any, node: ApprovalFlow | undefined) => {
    selectedNode.value = node;
    menuPopup.value?.toggle(e);
};

defineExpose({ open });
</script>

<template>
    <Dialog
        v-model:visible="visible" modal @after-hide="close" maximizable
        :header="$t('menu.approval_flow') + ' - ' + $t(`menu.${item?.subject ?? ''}`)" :style="{ width: '50rem' }">
        <div class="p-5">
            <OrganizationChart :value="item" key="id">
                <template #default="{ node }: { node: ApprovalFlow }">
                    <div class="font-medium text-lg min-w-[200px] min-h-[40px]">
                        {{ node.role?.name }}
                    </div>

                    <div class="absolute top-0.5 right-0.5 flex -gap-1.5" v-if="page.props.auth.allow.edit_approval_flow || page.props.auth.allow.delete_approval_flow">
                        <Button
                            icon="pi pi-ellipsis-v" size="small" severity="secondary" variant="text"
                            @click="togglePopup($event, node)" rounded></Button>
                    </div>
                    <div class="absolute -bottom-4 left-1/2 -translate-x-1/2" v-if="page.props.auth.allow.add_approval_flow">
                        <Button
                            icon="pi pi-plus" size="small" severity="secondary"
                            @click="() => formModal?.open(null, item?.subject, node.id)"
                            rounded></Button>
                    </div>
                </template>
            </OrganizationChart>
        </div>
    </Dialog>

    <FormModal
        ref="formModal" @close="reload"
        :subjects="[item?.subject ?? '']" />
    <ConfirmPopup />
    <Popover ref="menuPopup">
        <div class="list-none p-0 m-0 flex flex-col">
            <button
                @click="() => formModal?.open(selectedNode, null, null)" v-if="page.props.auth.allow.edit_approval_flow"
                class="flex items-center text-xs gap-2.5 p-2 hover:bg-gray-100 cursor-pointer rounded min-w-[125px]">
                <i class="pi pi-pencil" style="font-size: 0.75rem"></i>
                <span class="font-semibold">{{ t('action.edit') }}</span>
            </button>
            <button
                @click="destroy($event, selectedNode)" v-if="page.props.auth.allow.delete_approval_flow"
                class="flex items-center text-xs gap-2.5 p-2 hover:bg-gray-100 cursor-pointer rounded min-w-[125px] text-red-500">
                <i class="pi pi-trash" style="font-size: 0.75rem"></i>
                <span class="font-semibold">{{ t('action.delete') }}</span>
            </button>
        </div>
    </Popover>
</template>

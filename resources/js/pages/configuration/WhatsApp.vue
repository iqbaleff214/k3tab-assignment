<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import type { BreadcrumbItem, WhatsAppDevice } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import ConfigLayout from '@/layouts/ConfigurationLayout.vue';
import ConnectModal from '@/pages/configuration/WhatsApp/ConnectModal.vue';
import FormModal from '@/pages/configuration/WhatsApp/FormModal.vue';
import { Button, Card, ConfirmPopup, useConfirm } from 'primevue';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'menu.whatsapp',
        href: route('configuration.whatsapp.index'),
    },
];

interface WhatsAppDeviceItem extends WhatsAppDevice {
    loading: boolean;
}

defineProps<{ items: WhatsAppDeviceItem[]; availability: boolean }>();

const confirm = useConfirm();
const loading = ref<boolean>(false);
const connectModal = ref();
const formModal = ref();
const { t } = useI18n();

const connect = (item: WhatsAppDeviceItem) => {
    item.loading = true;
    router.post(route('configuration.whatsapp.connect', { token: item.token }), {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            item.loading = false;
        },
    });
};

const disconnect = (item: WhatsAppDeviceItem) => {
    item.loading = true;
    router.post(route('configuration.whatsapp.disconnect', { token: item.token }), {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            item.loading = false;
        },
    });
};

const reload = () => {
    loading.value = true;
    router.reload({
        preserveUrl: true,
        replace: true,
        only: ['items'],
        onSuccess: () => {
            loading.value = false;
        }
    });
};

const destroy = (event: MouseEvent, item: WhatsAppDevice) => {
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
            router.delete(route('configuration.whatsapp.destroy', item.id), {
                preserveScroll: true,
                preserveState: true,
            });
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('menu.whatsapp')" />

        <ConfigLayout>
            <div class="space-y-6">
                <HeadingSmall :title="$t('menu.whatsapp')" :description="$t('label.whatsapp_subtitle')" />

                <div class="grid gap-4 md:grid-cols-2" v-if="availability">
                    <div v-for="item in items" :key="item.id">
                        <Card>
                            <template #title>
                                <div class="flex justify-between">
                                    <div class="flex flex-col gap-0">
                                        <div class="flex items-baseline gap-x-2">
                                            <div class="text-sm font-semibold">{{ item.name }}</div>
                                            <i
                                                v-tooltip="$t('label.connected')"
                                                v-if="item.loggedIn"
                                                class="pi pi-verified text-emerald-500"
                                                style="font-size: 0.8rem"></i>
                                        </div>
                                        <small class="text-gray-500 font-light text-sm">{{ item.jid ? item.jid.split(':')[0] : '-' }}</small>
                                    </div>
                                    <div class="flex gap-1">
                                        <Button
                                            :icon="`pi pi-refresh ${loading ? 'animate-spin' : ''}`"
                                            size="small"
                                            :disabled="loading"
                                            variant="outlined"
                                            severity="secondary"
                                            @click="reload"
                                            rounded></Button>
                                        <Button
                                            icon="pi pi-trash"
                                            size="small"
                                            variant="outlined"
                                            severity="danger"
                                            @click="destroy($event, item)"
                                            rounded></Button>
                                    </div>
                                </div>
                            </template>
                            <template #content>
                                <div class="flex justify-between mt-5">
                                    <div>
                                        <Button
                                            icon="pi pi-qrcode" v-if="item.connected && item.qrcode && !item.loggedIn"
                                            size="small" variant="outlined" severity="success"
                                            @click="() => connectModal?.open(item)"
                                            rounded></Button>
                                    </div>
                                    <div>
                                        <Button
                                            v-if="!item.connected" :loading="item.loading"
                                            :label="$t('action.connect')" size="small"
                                            severity="success" :disabled="item.connected || item.loading"
                                            @click="() => connect(item)"></Button>
                                        <Button
                                            v-else  :loading="item.loading" :disabled="item.loading"
                                            :label="$t('action.disconnect')" size="small" severity="danger"
                                            @click="() => disconnect(item)"></Button>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>
                    <button
                        @click="() => formModal?.open()"
                        class="h-[143px] cursor-pointer rounded-2xl bg-white shadow-md hover:bg-gray-50">
                        <i class="pi pi-plus text-gray-500" style="font-size: 1.25rem"></i>
                    </button>
                </div>
                <p v-else class="text-red-500">{{ $t('label.whatsapp_not_available') }}</p>

                <ConfirmPopup />

                <ConnectModal ref="connectModal" />
                <FormModal ref="formModal" />
            </div>
        </ConfigLayout>
    </AppLayout>
</template>

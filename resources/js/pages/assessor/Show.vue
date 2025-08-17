<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import FormModal from '@/pages/assessor/Index/FormModal.vue';
import { type BreadcrumbItem, type Role, SharedData, type User as UserBase } from '@/types';
import { Head, usePage, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useConfirm, ConfirmPopup, Button } from 'primevue';
import { ref } from 'vue';
import { dateHumanFormatWithTime, dateHumanSmartFormat } from '@/lib/utils';
import PhoneNumber from '@/components/PhoneNumber.vue';

interface User extends UserBase {
    roles: Role[];
}

interface Props {
    item: User;
    next: number | null;
    prev: number | null;
    total: number;
    index: number;
    roles: string[];
}

const props = defineProps<Props>();
const page = usePage<SharedData>();
const confirm = useConfirm();
const modal = ref();
const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.assessor',
        href: route('assessor.index'),
    },
    {
        title: props.item.name,
        href: route('assessor.show', props.item.id),
    },
];

const destroy = (event: MouseEvent, item: User) => {
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
            router.delete(route('assessor.destroy', {
                assessor: item.id, next: props.next, prev: props.prev,
            }), {
                preserveScroll: true,
                preserveState: true,
            });
        },
    });
};
</script>

<template>
    <Head :title="$t('menu.assessor')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex justify-between">
                <div class="flex gap-x-1 items-center">
                    <Link :href="route('assessor.index')">
                        <Button
                            v-tooltip.bottom="$t('action.back')"
                            icon="pi pi-arrow-left" size="small" variant="text" severity="secondary"
                            rounded></Button>
                    </Link>
                    <Button
                        v-tooltip.bottom="$t('action.delete')"
                        icon="pi pi-trash" size="small" variant="text" severity="danger"
                        @click="destroy($event, item)" rounded></Button>
                    <span class="text-gray-300">|</span>
                </div>

                <div class="flex gap-x-1 items-center">
                    <div class="text-sm text-gray-500">{{ $t('label.show_of', { index, total }) }}</div>
                    <Link :href="route('assessor.show', prev ?? '')" :disabled="prev === null">
                        <Button
                            v-tooltip.bottom="$t('label.older')"
                            :disabled="prev === null"
                            icon="pi pi-chevron-left" size="small" variant="text" severity="secondary"
                             rounded></Button>
                    </Link>
                    <Link :href="route('assessor.show', next ?? '')" :disabled="next === null">
                        <Button
                            v-tooltip.bottom="$t('label.newer')"
                            :disabled="next === null"
                            icon="pi pi-chevron-right" size="small" variant="text" severity="secondary"
                             rounded></Button>
                    </Link>
                    <Button
                        v-tooltip.bottom="$t('action.edit')"
                        icon="pi pi-pencil" size="small" variant="text" severity="secondary"
                        @click="() => modal?.open(item)" rounded></Button>
                </div>
            </div>

            <div class="flex justify-between items-baseline">
                <div class="flex flex-col items-baseline gap-2.5">
                    <h1 class="text-2xl">{{ item.name }}</h1>
                    <div class="flex gap-2 text-sm font-light text-gray-500">
                        <a :href="`mailto:${item.email}`" class="hover:underline text-amber-500">
                            {{ item.email }}
                        </a>
                        <span class="text-gray-300" v-if="item.phone">|</span>
                        <PhoneNumber
                            :phone="item.phone" :international_phone="item.international_phone"
                            :has_whatsapp="item.has_whatsapp" />
                    </div>
                </div>
                <span
                    v-tooltip="$t('label.joined_date', { date: dateHumanFormatWithTime(item.created_at, 0, page.props.auth.user.locale) })"
                    class="text-sm text-gray-500">
                    {{ dateHumanSmartFormat(item.created_at, page.props.auth.user.locale) }}
                </span>
            </div>

        </div>

        <ConfirmPopup />

        <FormModal ref="modal" />
    </AppLayout>
</template>

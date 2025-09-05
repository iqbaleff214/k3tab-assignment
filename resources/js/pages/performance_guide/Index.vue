<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import type {
    BreadcrumbItem, FilterColumn, Paginate, SharedData, PerformanceGuide, Module
} from '@/types';
import { Head, router, usePage, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import {
    useConfirm, ConfirmPopup, Button, InputText
} from 'primevue';
import FormModal from '@/pages/performance_guide/Index/FormModal.vue';
import Filter from '@/components/Filter.vue';
import DataTable from '@/components/ui/table/DataTable.vue';
import { dateHumanFormatWithTime } from '@/lib/utils';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.performance_guide',
        href: route('admin.performance-guide.index'),
    },
];

const props = defineProps<{
    items: Paginate<PerformanceGuide>;
    modules: Module[];
}>();

const page = usePage<SharedData>();
const { t, locale } = useI18n();

const modal = ref();
const confirm = useConfirm();

const filterForm = useForm<{ [key: string]: any; filters: Record<string, FilterColumn> }>({
    filters: {
        skill_number: {
            value: null,
            matchMode: 'contains',
            label: 'field.skill_number',
            canChange: true,
        },
        title: {
            value: null,
            matchMode: 'contains',
            label: 'field.title',
            canChange: true,
        },
        module_id: {
            value: null,
            matchMode: 'equals',
            label: 'menu.module_skill',
            canChange: true,
            options: props.modules.map(module => ({ code: module.id, label: `${module.code}` })),
        },
        created_at: {
            value: null,
            matchMode: 'dateBetween',
            label: 'field.created_at',
            canChange: true,
        },
    }
});

const destroy = (event: MouseEvent, item: PerformanceGuide) => {
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
            router.delete(route('admin.performance-guide.destroy', item.id), {
                preserveScroll: true,
                preserveState: true,
            });
        },
    });
};

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
    <Head :title="t('menu.performance_guide')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <header class="flex items-center justify-between">
                <div>
                    <h3 class="mb-0.5 text-base font-medium">{{ t('menu.performance_guide') }}</h3>
                    <p class="text-sm text-muted-foreground">{{ t('label.menu_subtitle', { menu: t('menu.performance_guide') }) }}</p>
                </div>
                <div class="flex gap-x-2">
                    <div>
                        <InputText
                            placeholder="Search by skill number" :fluid="true" v-model="filterForm.filters.skill_number.value" size="small"
                            type="text" autocomplete="off" @update:model-value="searchByCode" />
                    </div>
                    <Button
                        :label="t('action.new_menu', { menu: t('menu.performance_guide') })"
                        @click="() => modal?.open(null)" size="small" />
                    <Filter :form="filterForm" />
                </div>
            </header>

            <DataTable
                name="admin_performance_guide_table" :selection="false" :items="items.data">
                <Column field="skill_number" header="field.skill_number" :sortable="true" :visible="true">
                    <template #body="{ row }: { row: PerformanceGuide }">
                        <a target="_blank" :href="route('admin.performance-guide.print', row.skill_number)" class="hover:underline text-amber-500">
                            {{ row.skill_number }}
                        </a>
                    </template>
                </Column>
                <Column field="title" header="field.title" :visible="true" />
                <Column field="created_at" header="field.created_at" :sortable="true" :visible="false">
                    <template #body="{ row }: { row: PerformanceGuide }">
                        {{ dateHumanFormatWithTime(row.created_at, 0, locale) }}
                    </template>
                </Column>
                <template #action="{ item }: { item: PerformanceGuide }">
                    <div class="flex gap-x-1.5">
                        <Button
                            v-tooltip.bottom="$t('action.edit')"
                            icon="pi pi-pencil" size="small" variant="text" severity="secondary"
                            @click="(e) => { e.preventDefault(); modal?.open(item); }" rounded></Button>
                        <Button
                            v-tooltip.bottom="$t('action.delete')"
                            icon="pi pi-trash" size="small" variant="text" severity="danger"
                            @click="destroy($event, item)" rounded></Button>
                    </div>
                </template>
            </DataTable>

            <Pagination :paginator="items" />

            <ConfirmPopup />

            <FormModal :modules ref="modal" />
        </div>
    </AppLayout>
</template>

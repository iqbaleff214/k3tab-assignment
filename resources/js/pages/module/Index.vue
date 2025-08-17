<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import type {
    BreadcrumbItem, FilterColumn, Paginate, SharedData, Module
} from '@/types';
import { Head, router, usePage, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import {
    useConfirm, ConfirmPopup, Button, InputText
} from 'primevue';
import FormModal from '@/pages/module/Index/FormModal.vue';
import Filter from '@/components/Filter.vue';
import DataTable from '@/components/ui/table/DataTable.vue';
import { dateHumanFormatWithTime } from '@/lib/utils';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.module',
        href: route('admin.module.index'),
    },
];

defineProps<{
    items: Paginate<Module>;
}>();

const page = usePage<SharedData>();
const { t, locale } = useI18n();

const selected = ref<Module[]>([]);
const modal = ref();
const confirm = useConfirm();

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

const destroy = (event: MouseEvent, item: Module | null) => {
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
            const url = !item ?
                route('admin.module.mass-destroy') :
                route('admin.module.destroy', item?.id);

            router.delete(url, {
                preserveScroll: true,
                preserveState: true,
                data: { ids: selected.value.map(i => i.id) },
                onSuccess: () => {
                    if (!item)
                        selected.value = [];
                }
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
    <Head :title="$t('menu.module')" />

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
                    <template v-if="selected.length > 0">
                        <Button
                            :disabled="items.total === 1"
                            icon="pi pi-trash" @click="destroy($event, null)"
                            :label="$t('action.delete')" size="small" severity="danger" />
                    </template>
                    <template v-else>
                        <Button
                            @click="() => modal?.open(null)"
                            :label="$t('action.new_menu', { menu: $t('menu.module') })" size="small" />
                    </template>
                    <Filter :form="filterForm" />
                </div>
            </header>

            <DataTable
                name="admin_module_skill_table" :selection="true"
                v-model:selected="selected"
                :items="items.data">
                <Column field="code" header="field.code" :sortable="true">
                    <template #body="{ row }: { row: Module }">
                        <Link :href="route('admin.module.show', row.id)" class="hover:underline text-amber-500">
                            {{ row.code }}
                        </Link>
                    </template>
                </Column>
                <Column field="title" header="field.title" :sortable="true" />
                <Column field="minimum_score" header="field.minimum_score" :sortable="true" :visible="false" />
                <Column field="questions_count" header="field.questions_count" :sortable="true" :visible="false">
                    <template #body="{ row }: { row: Module }">
                        {{ row.questions_count }} <span class="text-gray-500"> / {{ row.questions?.length || 0 }}</span>
                        <i class="pi pi-question-circle text-gray-500 ms-1" style="font-size: 0.65rem" v-tooltip="$t('label.out_of_questions_only_display', { total: row.questions?.length || 0, count: row.questions_count })"></i>
                    </template>
                </Column>
                <Column field="duration_estimation" header="field.duration_estimation" :sortable="true" :visible="false">
                    <template #body="{ row }: { row: Module }">
                        <span class="text-xs text-gray-500" v-if="!row.duration_estimation" v-text="$t('label.no_estimation')"></span>
                        <span v-else v-text="$t('label.n_minute', row.duration_estimation, { n: row.duration_estimation })"></span>
                    </template>
                </Column>
                <Column field="created_at" header="field.created_at" :sortable="true" :visible="false">
                    <template #body="{ row }: { row: Module }">
                        {{ dateHumanFormatWithTime(row.created_at, 0, locale) }}
                    </template>
                </Column>
                <template #action="{ item }: { item: Module }">
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

            <FormModal ref="modal" />
        </div>
    </AppLayout>
</template>

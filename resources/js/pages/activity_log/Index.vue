<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import Filter from '@/components/Filter.vue';
import DataTable from '@/components/ui/table/DataTable.vue';
import DetailModal from '@/pages/activity_log/Index/DetailModal.vue';
import type {
    ActivityLog as ActivityLogBase, BreadcrumbItem, FilterColumn,
    Paginate, SharedData, User
} from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Button, Column, ConfirmPopup } from 'primevue';
import { dateHumanFormatWithTime } from '@/lib/utils';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';

interface ActivityLog extends ActivityLogBase {
    causer: User;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.activity_log',
        href: route('activity-log.index'),
    },
];

const props = defineProps<{
    items: Paginate<ActivityLog>;
    modules: string[];
}>();

const modal = ref();
const page = usePage<SharedData>();
const { t } = useI18n();

const filterForm = useForm<{ [key: string]: any; filters: Record<string, FilterColumn> }>({
    filters: {
        description: {
            value: null,
            matchMode: 'contains',
            label: 'field.description',
            canChange: true,
        },
        log_name: {
            value: null,
            matchMode: 'equals',
            label: 'field.module',
            canChange: true,
            options: props.modules.map(module => ({
                label: t(`menu.${module}`),
                code: 'App\\Models\\' + module.split('_')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(''),
            })),
        },
        created_at: {
            value: null,
            matchMode: 'dateBetween',
            label: 'field.created_at',
            canChange: true,
        },
    }
});

</script>

<template>
    <Head :title="$t('menu.activity_log')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <header class="flex items-center justify-between">
                <div>
                    <h3 class="mb-0.5 text-base font-medium">{{ $t('menu.activity_log') }}</h3>
                    <p class="text-sm text-muted-foreground">{{ $t('label.menu_subtitle', { menu: $t('menu.activity_log') }) }}</p>
                </div>
                <div class="flex gap-x-2">
                    <Filter :form="filterForm" />
                </div>
            </header>

            <DataTable
                name="activity_log_table" :selection="false"
                :items="items.data">
                <Column field="created_at" header="field.created_at" :sortable="true">
                    <template #body="{ row }: { row: ActivityLog }">
                        {{ dateHumanFormatWithTime(row.created_at, 0, page.props.auth.user.locale) }}
                    </template>
                </Column>
                <Column field="description" header="field.description">
                    <template #body="{ row }: { row: ActivityLog }">
                        <div v-html="row.description"></div>
                    </template>
                </Column>
                <Column field="module" header="field.module">
                    <template #body="{ row }: { row: ActivityLog }">
                        {{ $t('menu.' + row.module) }}
                    </template>
                </Column>
                <Column field="causer.name" header="menu.user">
                    <template #body="{ row }: { row: ActivityLog }">
                        {{ row.causer.name }}
                    </template>
                </Column>
                <template #action="{ item }: { item: ActivityLog }">
                    <div class="flex gap-x-1.5">
                        <Button
                            v-tooltip.bottom="$t('action.view')"
                            icon="pi pi-eye" size="small" variant="text" severity="secondary"
                            @click="(e) => { e.preventDefault(); modal?.open(item); }" rounded></Button>
                    </div>
                </template>
            </DataTable>

            <Pagination :paginator="items" />

            <ConfirmPopup />

            <DetailModal ref="modal" />
        </div>
    </AppLayout>
</template>

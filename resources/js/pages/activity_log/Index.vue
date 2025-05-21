<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import type {
    ActivityLog as ActivityLogBase, BreadcrumbItem, FilterColumn,
    Paginate, SharedData, User
} from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Column, ConfirmPopup } from 'primevue';
import { dateHumanFormatWithTime } from '@/lib/utils';
import Filter from '@/components/Filter.vue';
import DataTable from '@/components/ui/table/DataTable.vue';

interface ActivityLog extends ActivityLogBase {
    causer: User;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.activity_log',
        href: route('activity-log.index'),
    },
];

defineProps<{
    items: Paginate<ActivityLog>;
}>();

const page = usePage<SharedData>();

const filterForm = useForm<{ [key: string]: any; filters: Record<string, FilterColumn> }>({
    filters: {
        description: {
            value: null,
            matchMode: 'contains',
            label: 'field.description',
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
            </DataTable>

            <Pagination :paginator="items" />

            <ConfirmPopup />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type {
    Assessment,
    BreadcrumbItem,
    FilterColumn,
    Paginate, PerformanceGuide, User
} from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';
import { Button, ConfirmPopup, InputText, useConfirm } from 'primevue';
import Filter from '@/components/Filter.vue';
import Pagination from '@/components/Pagination.vue';
import AssignmentSubmissionModal from '@/pages/panel/assessee/assessment/Index/AssignmentSubmissionModal.vue';
import { dateHumanFormatWithTime } from '@/lib/utils';
import DataTable from '@/components/ui/table/DataTable.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.assessment',
        href: route('assessee.assessment.index'),
    },
];

const statuses = ['pending', 'scheduled', 'completed', 'cancelled'];

const props = defineProps<{
    items: Paginate<Assessment>;
    assessors: User[];
    guides: PerformanceGuide[];
}>();

const { t, locale } = useI18n();

const modal = ref();
const confirm = useConfirm();

const filterForm = useForm<{ [key: string]: any; filters: Record<string, FilterColumn> }>({
    filters: {
        'guide.skill_number': {
            value: null,
            matchMode: 'equals',
            label: 'field.skill_number',
            canChange: true,
        },
        status: {
            value: null,
            matchMode: 'equals',
            label: 'field.status',
            canChange: true,
            options: statuses.map(s => ({ code: s, label: t(`label.${s}`) }))
        },
        scheduled_at: {
            value: null,
            matchMode: 'dateBetween',
            label: 'field.scheduled_at',
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

const destroy = (event: MouseEvent, item: Assessment) => {
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
            router.delete(route('assessee.assessment.destroy', item.id), {
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
    <Head :title="t('menu.assessment')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <header class="flex items-center justify-between">
                <div>
                    <h3 class="mb-0.5 text-base font-medium">{{ t('menu.assessment') }}</h3>
                    <p class="text-sm text-muted-foreground">{{ t('label.menu_subtitle', { menu: t('menu.assessment') }) }}</p>
                </div>
                <div class="flex gap-x-2">
                    <div>
                        <InputText
                            placeholder="Search by skill number" :fluid="true" v-model="filterForm.filters['guide.skill_number'].value" size="small"
                            type="text" autocomplete="off" @update:model-value="searchByCode" />
                    </div>
                    <Button
                        :label="t('action.submit_menu', { menu: t('menu.assessment') })"
                        @click="modal?.open" size="small" />
                    <Filter :form="filterForm" />
                </div>
            </header>

            <DataTable
                name="assessee_assessment_table" :selection="false" :items="items.data">
                <Column field="skill_number" header="field.skill_number" :sortable="false" :visible="true">
                    <template #body="{ row }: { row: Assessment }">
                        <span class="text-amber-500">{{ row.guide?.skill_number }}</span>
                    </template>
                </Column>
                <Column field="assessor" header="field.assessor" :sortable="true" :visible="true">
                    <template #body="{ row }: { row: Assessment }">
                        <div class="flex flex-col">
                            <span>{{ row.assessor?.name }}</span>
                            <a :href="`mailto:${row.assessor?.email}`" class="text-gray-500 hover:underline hover:text-amber-500">
                                {{ row.assessor?.email }}
                            </a>
                        </div>
                    </template>
                </Column>
                <Column field="status" header="field.status" :sortable="true" :visible="true">
                    <template #body="{ row }: { row: Assessment }">
                        <span class="px-2 py-0.5 text-xs rounded-lg font-medium" :class="{
                            'bg-yellow-100 text-yellow-600': row.status === 'pending',
                            'bg-blue-100 text-blue-600': row.status === 'scheduled',
                            'bg-emerald-100 text-emerald-600': row.status === 'completed',
                            'bg-red-100 text-red-600': row.status === 'cancelled',
                        }">
                            {{ t(`label.${row.status}`) }}
                        </span>
                    </template>
                </Column>
                <Column field="scheduled_at" header="field.scheduled_at" :sortable="true" :visible="true">
                    <template #body="{ row }: { row: Assessment }">
                        {{ dateHumanFormatWithTime(row.scheduled_at, 0, locale) }}
                    </template>
                </Column>
                <Column field="created_at" header="field.created_at" :sortable="true" :visible="false">
                    <template #body="{ row }: { row: Assessment }">
                        {{ dateHumanFormatWithTime(row.created_at, 0, locale) }}
                    </template>
                </Column>
                <template #action="{ item }: { item: Assessment }">
                    <div class="flex gap-x-1.5">
                        <Button
                            v-tooltip.bottom="t('action.delete')" v-if="item.status === 'pending'"
                            icon="pi pi-trash" size="small" variant="text" severity="danger"
                            @click="destroy($event, item)" rounded></Button>
                    </div>
                </template>
            </DataTable>

            <Pagination :paginator="items" />

            <ConfirmPopup />

            <AssignmentSubmissionModal
                :assessors :guides ref="modal" />
        </div>
    </AppLayout>
</template>

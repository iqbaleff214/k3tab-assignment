<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type {
    Assessment,
    BreadcrumbItem,
    FilterColumn, Module,
    Paginate, PostTest, User
} from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';
import { Button, ConfirmPopup, InputText, useConfirm } from 'primevue';
import Filter from '@/components/Filter.vue';
import Pagination from '@/components/Pagination.vue';
import ViewModal from '@/pages/assessment/Index/ViewModal.vue';
import { dateHumanFormatWithTime } from '@/lib/utils';
import DataTable from '@/components/ui/table/DataTable.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.assessment',
        href: route('admin.assessment.index'),
    },
];

const props = defineProps<{
    items: Paginate<PostTest>;
    modules: Module[];
}>();

const { t, locale } = useI18n();

const viewModal = ref();
const confirm = useConfirm();

const filterForm = useForm<{ [key: string]: any; filters: Record<string, FilterColumn> }>({
    filters: {
        module_id: {
            value: null,
            matchMode: 'equals',
            label: 'menu.module',
            canChange: true,
            options: props.modules.map(s => ({ code: s.id, label: s.code ?? s.title })),
        },
        is_passed: {
            value: null,
            matchMode: 'equals',
            label: 'field.is_passed',
            canChange: true,
            options: [
                { code: '1', label: t('label.yes') },
                { code: '0', label: t('label.no') },
            ],
        },
        created_at: {
            value: null,
            matchMode: 'dateBetween',
            label: 'field.created_at',
            canChange: true,
        },
    }
});

const destroy = (event: MouseEvent, item: PostTest) => {
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
            router.delete(route('admin.post-test.destroy', item.id), {
                preserveScroll: true,
                preserveState: true,
            });
        },
    });
};

</script>

<template>
    <Head :title="t('menu.post_test')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <header class="flex items-center justify-between">
                <div>
                    <h3 class="mb-0.5 text-base font-medium">{{ t('menu.post_test') }}</h3>
                    <p class="text-sm text-muted-foreground">{{ t('label.menu_subtitle', { menu: t('menu.post_test') }) }}</p>
                </div>
                <div class="flex gap-x-2">
                    <Filter :form="filterForm" />
                </div>
            </header>

            <DataTable
                name="admin_post_test_table" :selection="false" :items="items.data">
                <Column field="module_id" header="menu.module" :sortable="true" :visible="false">
                    <template #body="{ row }: { row: PostTest }">
                        {{ row.module?.code }}
                    </template>
                </Column>
                <Column field="assessee" header="field.assessee" :sortable="true" :visible="true">
                    <template #body="{ row }: { row: PostTest }">
                        <div class="flex flex-col">
                            <span>{{ row.assessee?.name }}</span>
                            <a :href="`mailto:${row.assessee?.email}`" class="text-gray-500 hover:underline hover:text-amber-500">
                                {{ row.assessee?.email }}
                            </a>
                        </div>
                    </template>
                </Column>
                <Column field="score" header="field.score" :sortable="true" :visible="true" />
                <Column field="minimum_score" header="field.minimum_score" :sortable="true" :visible="true" />
                <Column field="is_passed" header="field.is_passed" :sortable="true" :visible="true">
                    <template #body="{ row }: { row: PostTest }">
                        <span class="px-2 py-0.5 text-xs rounded-lg font-medium" :class="{
                            'bg-yellow-100 text-yellow-600': row.status === 'pending',
                            'bg-blue-100 text-blue-600': row.status === 'scheduled',
                            'bg-emerald-100 text-emerald-600': row.is_passed,
                            'bg-red-100 text-red-600': !row.is_passed,
                        }">
                            {{ t(row.is_passed ? 'label.yes' : 'label.no') }}
                        </span>
                    </template>
                </Column>
                <Column field="created_at" header="field.created_at" :sortable="true" :visible="false">
                    <template #body="{ row }: { row: PostTest }">
                        {{ dateHumanFormatWithTime(row.created_at, 0, locale) }}
                    </template>
                </Column>
                <template #action="{ item }: { item: PostTest }">
                    <div class="flex gap-x-1.5">
                        <Button
                            v-tooltip.bottom="t('action.delete')"
                            icon="pi pi-trash" size="small" variant="text" severity="danger"
                            @click="destroy($event, item)" rounded></Button>
                    </div>
                </template>
            </DataTable>

            <Pagination :paginator="items" />

            <ConfirmPopup />

            <ViewModal ref="viewModal" />
        </div>
    </AppLayout>
</template>

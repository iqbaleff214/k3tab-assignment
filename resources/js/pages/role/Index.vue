<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import FormModal from '@/pages/role/Index/FormModal.vue';
import type {
    BreadcrumbItem, Paginate, Role as RoleBase,
    Permission, SharedData, FilterColumn,
} from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import {
    useConfirm, ConfirmPopup, Button,
} from 'primevue';
import Filter from '@/components/Filter.vue';
import DataTable from '@/components/ui/table/DataTable.vue';
import { dateHumanFormatWithTime } from '@/lib/utils';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.role',
        href: route('role.index'),
    },
];

interface Role extends RoleBase {
    permissions: Permission[];
}

const props = defineProps<{
    items: Paginate<Role>;
    modules: string[];
    actions: string[];
    skips: string[];
    permissions: string[];
}>();

const page = usePage<SharedData>();
const { t, locale } = useI18n();

const selected = ref<Role[]>([]);
const modal = ref();
const confirm = useConfirm();

const filterForm = useForm<{ [key: string]: any; filters: Record<string, FilterColumn> }>({
    filters: {
        name: {
            value: null,
            matchMode: 'contains',
            label: 'field.name',
            canChange: true,
        },
        'permissions.name': {
            value: null,
            matchMode: 'equals',
            label: 'field.permission',
            canChange: false,
            options: props.permissions,
        },
        created_at: {
            value: null,
            matchMode: 'dateBetween',
            label: 'field.created_at',
            canChange: true,
        },
    }
});

const destroy = (event: MouseEvent, item: Role | null) => {
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
                route('role.mass-destroy') :
                route('role.destroy', item?.id);

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
</script>

<template>
    <Head :title="$t('menu.role')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <header class="flex items-center justify-between">
                <div>
                    <h3 class="mb-0.5 text-base font-medium">{{ $t('menu.role') }}</h3>
                    <p class="text-sm text-muted-foreground">{{ $t('label.menu_subtitle', { menu: $t('menu.role') }) }}</p>
                </div>
                <div class="flex gap-x-2">
                    <template v-if="selected.length > 0">
                        <Button
                            v-if="page.props.auth.allow.delete_role"
                            :disabled="items.total === 1"
                            icon="pi pi-trash" @click="destroy($event, null)"
                            :label="$t('action.delete')" size="small" severity="danger" />
                    </template>
                    <template v-else>
                        <Button
                            v-if="page.props.auth.allow.add_role"
                            @click="() => modal?.open(null)"
                            :label="$t('action.new_menu', { menu: $t('menu.role') })" size="small" />
                    </template>
                    <Filter :form="filterForm" />
                </div>
            </header>


            <DataTable
                name="role_table" :selection="true"
                v-model:selected="selected"
                :items="items.data">
                <Column field="name" header="field.name" :sortable="true" />
                <Column field="permissions.name" header="field.permission">
                    <template #body="{ row }: { row: Role }">
                        <div class=" flex flex-wrap gap-2 items-center justify-start max-w-[400px]">
                            <span
                                v-for="permission in row.permissions" :key="permission.id"
                                class="inline-block px-2 py-1 text-xs bg-emerald-100 text-emerald-800 rounded">
                                {{ permission.name }}
                            </span>
                        </div>
                    </template>
                </Column>
                <Column field="created_at" header="field.created_at" :sortable="true">
                    <template #body="{ row }: { row: Role }">
                        {{ dateHumanFormatWithTime(row.created_at, 0, locale) }}
                    </template>
                </Column>
                <template #action="{ item }: { item: Role }">
                    <div class="flex gap-x-1.5">
                        <Button
                            v-tooltip.bottom="$t('action.edit')"
                            v-if="page.props.auth.allow.edit_role"
                            icon="pi pi-pencil" size="small" variant="text" severity="secondary"
                            @click="(e) => { e.preventDefault(); modal?.open(item); }" rounded></Button>
                        <Button
                            v-tooltip.bottom="$t('action.delete')"
                            v-if="page.props.auth.allow.delete_role"
                            icon="pi pi-trash" size="small" variant="text" severity="danger"
                            @click="destroy($event, item)" rounded></Button>
                    </div>
                </template>
            </DataTable>

            <Pagination :paginator="items" />

            <ConfirmPopup />

            <FormModal
                :modules :actions :skips
                ref="modal" />
        </div>
    </AppLayout>
</template>

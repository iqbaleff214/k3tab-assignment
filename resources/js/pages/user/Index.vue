<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import type { BreadcrumbItem, Paginate, Role, SharedData, User as UserBase } from '@/types';
import { Head, router, Link, usePage } from '@inertiajs/vue3';
import { FilterMatchMode } from '@primevue/core/api';
import { onMounted, ref, watch } from 'vue';
import {
    useConfirm, ConfirmPopup, DataTable, Column,
    Button, InputText, Tag, Select,
} from 'primevue';
import FormModal from '@/pages/user/Index/FormModal.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User',
        href: route('user.index'),
    },
];

interface User extends UserBase {
    roles: Role[];
}

defineProps<{
    items: Paginate<User>;
    roles: string[];
}>();

const page = usePage<SharedData>();

const selected = ref<User[]>([]);
const filters = ref({});
const modal = ref();
const confirm = useConfirm();

const destroy = (event: MouseEvent, item: User | null) => {
    confirm.require({
        target: event.currentTarget as HTMLElement,
        message: 'Are you sure you want to delete?',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger',
        },
        accept: () => {
            const url = !item ?
                route('user.mass-destroy') :
                route('user.destroy', item?.id);

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
const initFilter = () => {
    filters.value = {
        name: { value: null, matchMode: FilterMatchMode.CONTAINS },
        email: { value: null, matchMode: FilterMatchMode.EQUALS },
        'roles.name': { value: null, matchMode: FilterMatchMode.EQUALS },
    };
};

watch(filters, (newFilters) => {
    router.reload({
        only: ['items'],
        data: { filter: newFilters },
        replace: true,
    });
}, { deep: true });

onMounted(initFilter);
</script>

<template>
    <Head title="User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <header class="flex items-center justify-between">
                <div>
                    <h3 class="mb-0.5 text-base font-medium">User</h3>
                    <p class="text-sm text-muted-foreground">Manage user account</p>
                </div>
                <div class="flex gap-x-2">
                    <template v-if="selected.length > 0">
                        <Button
                            v-if="page.props.auth.allow.delete_user"
                            :disabled="items.total === 1"
                            icon="pi pi-trash" @click="destroy($event, null)"
                            label="Delete" size="small" severity="danger" />
                    </template>
                    <template v-else>
                        <Button
                            v-if="page.props.auth.allow.add_user"
                            @click="() => modal?.open(null)"
                            label="New User" size="small" />
                    </template>
                    <Button
                        @click="router.reload({ only: ['items'], data: { filter: null }, replace: true, onSuccess: initFilter })"
                        icon="pi pi-filter-slash" severity="secondary" size="small" />
                </div>
            </header>

            <DataTable
                :value="items.data"
                :global-filter-fields="['name', 'email']"
                v-model:selection="selected"
                v-model:filters="filters"
                table-style="min-width: 50rem"
                filter-display="menu" scrollable
                lazy striped-rows show-gridlines>
                <Column selection-mode="multiple" header-style="width: 3rem" />
                <Column field="name" header="Name">
                    <template #body="{ data }: { data: User }">
                        <Link :href="route('user.show', data.id)" class="hover:text-teal-600">{{ data.name }}</Link>
                    </template>
                    <template #filter="{ filterModel }">
                        <InputText v-model="filterModel.value" type="text" placeholder="Search by name" />
                    </template>
                </Column>
                <Column field="email" header="Email">
                    <template #body="{ data }: { data: User }">
                        <a :href="`mailto:${data.email}`" class="hover:text-teal-600">{{ data.email }}</a>
                    </template>
                    <template #filter="{ filterModel }">
                        <InputText v-model="filterModel.value" type="text" placeholder="Search by email" />
                    </template>
                </Column>
                <Column field="roles.name" header="Role">
                    <template #body="{ data }: { data: User }">
                        <Tag
                            v-for="role in data.roles" :key="role.id"
                            :value="role.name" class="me-1" />
                    </template>
                    <template #filter="{ filterModel }">
                        <Select v-model="filterModel.value" :options="roles" placeholder="Search by role" />
                    </template>
                </Column>
                <Column class="w-24 !text-end">
                    <template #body="{ data }: { data: User }">
                        <div class="flex gap-x-2 items-center justify-center">
                            <Button
                                v-if="page.props.auth.allow.edit_user"
                                icon="pi pi-pencil" size="small" variant="outlined" severity="secondary"
                                @click="() => modal?.open(data)" rounded></Button>
                            <Button
                                v-if="page.props.auth.allow.delete_user"
                                :disabled="items.total === 1"
                                icon="pi pi-trash" size="small" variant="outlined" severity="danger"
                                @click="destroy($event, data)" rounded></Button>
                        </div>
                    </template>
                </Column>
                <template #empty>No users found.</template>
            </DataTable>

            <Pagination :paginator="items" />

            <ConfirmPopup />

            <FormModal
                :roles ref="modal" />
        </div>
    </AppLayout>
</template>

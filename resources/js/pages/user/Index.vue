<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import type {
    BreadcrumbItem, FilterColumn, Paginate, Role,
    SharedData, User as UserBase,
} from '@/types';
import { Head, router, usePage, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import {
    useConfirm, ConfirmPopup, Button
} from 'primevue';
import FormModal from '@/pages/user/Index/FormModal.vue';
import Filter from '@/components/Filter.vue';
import DataTable from '@/components/ui/table/DataTable.vue';
import { dateHumanFormatWithTime } from '@/lib/utils';
import PhoneNumber from '@/components/PhoneNumber.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.user',
        href: route('user.index'),
    },
];

interface User extends UserBase {
    roles: Role[];
}

const props = defineProps<{
    items: Paginate<User>;
    roles: string[];
}>();

const page = usePage<SharedData>();
const { t, locale } = useI18n();

const selected = ref<User[]>([]);
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
        email: {
            value: null,
            matchMode: 'contains',
            label: 'field.email',
            canChange: true,
        },
        phone: {
            value: null,
            matchMode: 'contains',
            label: 'field.phone',
            canChange: true,
        },
        'roles.name': {
            value: null,
            matchMode: 'equals',
            label: 'field.role',
            canChange: false,
            options: props.roles,
        },
        created_at: {
            value: null,
            matchMode: 'dateBetween',
            label: 'field.joined_at',
            canChange: true,
        },
    }
});

const destroy = (event: MouseEvent, item: User | null) => {
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
</script>

<template>
    <Head :title="$t('menu.user')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <header class="flex items-center justify-between">
                <div>
                    <h3 class="mb-0.5 text-base font-medium">{{ $t('menu.user') }}</h3>
                    <p class="text-sm text-muted-foreground">{{ $t('label.menu_subtitle', { menu: $t('menu.user') }) }}</p>
                </div>
                <div class="flex gap-x-2">
                    <template v-if="selected.length > 0">
                        <Button
                            v-if="page.props.auth.allow.delete_user"
                            :disabled="items.total === 1"
                            icon="pi pi-trash" @click="destroy($event, null)"
                            :label="$t('action.delete')" size="small" severity="danger" />
                    </template>
                    <template v-else>
                        <Button
                            v-if="page.props.auth.allow.add_user"
                            @click="() => modal?.open(null)"
                            :label="$t('action.new_menu', { menu: $t('menu.user') })" size="small" />
                    </template>
                    <Filter :form="filterForm" />
                </div>
            </header>

            <DataTable
                name="user_table" :selection="true"
                v-model:selected="selected"
                :items="items.data">
                <Column field="name" header="field.name" :sortable="true">
                    <template #body="{ row }: { row: User }">
                        <Link :href="route('user.show', row.id)" class="hover:underline hover:text-emerald-500">
                            {{ row.name }}
                        </Link>
                    </template>
                </Column>
                <Column field="email" header="field.email" :sortable="true">
                    <template #body="{ row }: { row: User }">
                        <a :href="`mailto:${row.email}`" class="hover:underline hover:text-emerald-500">
                            {{ row.email }}
                        </a>
                    </template>
                </Column>
                <Column field="phone" header="field.phone" :sortable="true">
                    <template #body="{ row }: { row: User }">
                        <PhoneNumber :has_whatsapp="row.has_whatsapp" :phone="row.phone" :international_phone="row.international_phone" />
                    </template>
                </Column>
                <Column field="roles.name" header="field.role">
                    <template #body="{ row }: { row: User }">
                        <div class=" flex gap-x-2 items-center justify-start">
                            <span
                                v-for="role in row.roles" :key="role.id"
                                class="inline-block px-2 py-1 text-xs bg-emerald-100 text-emerald-800 rounded">
                                {{ role.name }}
                            </span>
                        </div>
                    </template>
                </Column>
                <Column field="created_at" header="field.joined_at" :sortable="true">
                    <template #body="{ row }: { row: User }">
                        {{ dateHumanFormatWithTime(row.created_at, 0, locale) }}
                    </template>
                </Column>
                <template #action="{ item }: { item: User }">
                    <div class="flex gap-x-1.5">
                        <Link v-if="item.id === page.props.auth.user.id" :href="route('profile.edit')">
                            <Button
                                icon="pi pi-pencil" size="small" variant="text" severity="secondary"
                                v-tooltip.bottom="$t('action.edit')" rounded></Button>
                        </Link>
                        <Button
                            v-tooltip.bottom="$t('action.edit')"
                            v-else-if="page.props.auth.allow.edit_user"
                            icon="pi pi-pencil" size="small" variant="text" severity="secondary"
                            @click="(e) => { e.preventDefault(); modal?.open(item); }" rounded></Button>
                        <Button
                            v-tooltip.bottom="$t('action.delete')"
                            v-if="page.props.auth.allow.delete_user"
                            :disabled="item.id === page.props.auth.user.id"
                            icon="pi pi-trash" size="small" variant="text" severity="danger"
                            @click="destroy($event, item)" rounded></Button>
                    </div>
                </template>
            </DataTable>

            <Pagination :paginator="items" />

            <ConfirmPopup />

            <FormModal
                :roles ref="modal" />
        </div>
    </AppLayout>
</template>

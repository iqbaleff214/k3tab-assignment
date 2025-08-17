<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import type {
    BreadcrumbItem, FilterColumn, Paginate, SharedData, User,
} from '@/types';
import { Head, router, usePage, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import {
    useConfirm, ConfirmPopup, Button, InputText
} from 'primevue';
import FormModal from '@/pages/assessee/Index/FormModal.vue';
import Filter from '@/components/Filter.vue';
import DataTable from '@/components/ui/table/DataTable.vue';
import { dateHumanFormatWithTime } from '@/lib/utils';
import PhoneNumber from '@/components/PhoneNumber.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.assessee',
        href: route('assessee.index'),
    },
];

defineProps<{
    items: Paginate<User>;
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
        created_at: {
            value: null,
            matchMode: 'dateBetween',
            label: 'field.created_at',
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
                route('assessee.mass-destroy') :
                route('assessee.destroy', item?.id);

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

const searchUser = (value: string | undefined): void => {
    const url = new URL(window.location.href);
    url.search = '';
    filterForm.get(url.toString(), {
        preserveScroll: true,
        preserveState: true,
    });
};

</script>

<template>
    <Head :title="$t('menu.assessee')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <header class="flex items-center justify-between">
                <div>
                    <h3 class="mb-0.5 text-base font-medium">{{ $t('menu.assessee') }}</h3>
                    <p class="text-sm text-muted-foreground">{{ $t('label.menu_subtitle', { menu: $t('menu.assessee') }) }}</p>
                </div>
                <div class="flex gap-x-2">
                    <div>
                        <InputText
                            placeholder="Search by name" :fluid="true" v-model="filterForm.filters.name.value" size="small"
                            type="text" autocomplete="off" @update:model-value="searchUser" />
                    </div>
                    <template v-if="selected.length > 0">
                        <Button
                            :disabled="items.total === 1" v-if="page.props.auth.user.type === 'admin'"
                            icon="pi pi-trash" @click="destroy($event, null)"
                            :label="$t('action.delete')" size="small" severity="danger" />
                    </template>
                    <template v-else>
                        <Button
                            @click="() => modal?.open(null)" v-if="page.props.auth.user.type === 'admin'"
                            :label="$t('action.new_menu', { menu: $t('menu.assessee') })" size="small" />
                    </template>
                    <Filter :form="filterForm" />
                </div>
            </header>

            <DataTable
                name="assessee_table" :selection="page.props.auth.user.type === 'admin'"
                v-model:selected="selected"
                :items="items.data">
                <Column field="name" header="field.name" :sortable="true">
                    <template #body="{ row }: { row: User }">
                        <Link :href="route('assessee.show', row.id)" class="hover:underline text-amber-500">
                            {{ row.name }}
                        </Link>
                    </template>
                </Column>
                <Column field="email" header="field.email" :sortable="true">
                    <template #body="{ row }: { row: User }">
                        <a :href="`mailto:${row.email}`" class="hover:underline text-amber-500">
                            {{ row.email }}
                        </a>
                    </template>
                </Column>
                <Column field="phone" header="field.phone" :sortable="true">
                    <template #body="{ row }: { row: User }">
                        <PhoneNumber :has_whatsapp="row.has_whatsapp" :phone="row.phone" :international_phone="row.international_phone" />
                    </template>
                </Column>
                <Column field="created_at" header="field.created_at" :sortable="true">
                    <template #body="{ row }: { row: User }">
                        {{ dateHumanFormatWithTime(row.created_at, 0, locale) }}
                    </template>
                </Column>
                <template #action="{ item }: { item: User }">
                    <div class="flex gap-x-1.5">
                        <Button
                            v-tooltip.bottom="$t('action.edit')" v-if="page.props.auth.user.type === 'admin'"
                            icon="pi pi-pencil" size="small" variant="text" severity="secondary"
                            @click="(e) => { e.preventDefault(); modal?.open(item); }" rounded></Button>
                        <Button
                            v-tooltip.bottom="$t('action.delete')" v-if="page.props.auth.user.type === 'admin'"
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

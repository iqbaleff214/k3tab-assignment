<script setup lang="ts">
import { onMounted, ref, Slots, useSlots, watch } from 'vue';
import type { TableItem, TableColumnItem } from '@/types';
import { Button, Checkbox, Popover } from 'primevue';
import { router } from '@inertiajs/vue3';

const props = defineProps<TableItem>();
const emit = defineEmits(['update:selected']);
const selected = ref<(string|number)[]>([]);
const slots = useSlots();
const columns = ref<TableColumnItem[]>([]);
const sortField = ref<{ field: string; order: 'asc' | 'desc' }>({
    field: '', order: 'asc',
});
const filterPopup = ref();

const selectAll = (): void => {
    if (selected.value.length === props.items.length) {
        selected.value = [];
    } else {
        selected.value = props.items;
    }
};

const save = (): void => {
    const data = columns.value.map(({ field, isVisible }) => ({ field, isVisible}));
    localStorage.setItem(props.name, JSON.stringify(data));
};

watch(selected, (value) => {
    emit('update:selected', value);
});

const sort = (field: string): void => {
    if (sortField.value.field === field) {
        sortField.value.order = sortField.value.order === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value.order = 'asc';
    }
    sortField.value.field = field;
    const url = new URL(window.location.href);
    url.searchParams.set('sorts', `${field}:${sortField.value.order}`);
    router.get(url.toString(), {}, {
        preserveState: true,
        preserveScroll: true,
    });
}

const sortFieldIcon = (field: string): string => {
    if (sortField.value.field === field) {
        return sortField.value.order === 'asc' ? 'pi pi-sort-amount-up text-emerald-500' : 'pi pi-sort-amount-down text-emerald-500';
    }
    return 'pi pi-sort-alt';
}

onMounted(() => {
    columns.value = (slots.default?.() ?? []).map(
        (column: any): TableColumnItem => ({
            field: column.props.field,
            header: column.props.header,
            slot: column.children as Slots,
            isVisible: column.props.visible ?? true,
            isSortable: column.props.sortable ?? false,
        }),
    );

    const filterRaw = localStorage.getItem(props.name);
    if (filterRaw) {
        const filterData: {field: string; isVisible: boolean;}[] = JSON.parse(filterRaw);
        columns.value.forEach((col) => {
            col.isVisible = filterData.find((d) => d.field === col.field)?.isVisible ?? true;
        });
    }
});
</script>

<template>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-gray-700 whitespace-nowrap">
            <thead>
            <tr class="bg-gray-100 dark:bg-gray-900 text-xs uppercase text-gray-500 dark:text-gray-300 border-b border-gray-200">
                <th class="px-3 py-2 text-left" v-if="selection">
                    <Checkbox
                        @click="selectAll"
                        :value="selected?.length === items?.length && selected?.length > 0"
                        :disabled="items.length === 0" binary />
                </th>
                <template v-for="col in columns" :key="col.field">
                    <th class="px-3 py-2 text-left" v-if="col.isVisible">
                        <div class="flex justify-between items-center">
                            <div v-html="$t(col.header)" :class="{ 'font-semibold': col.field === sortField.field }"></div>
                            <Button
                                v-tooltip.bottom="$t('label.sort_field_by', { field: $t(col.header) })"
                                v-if="col.isSortable" :icon="sortFieldIcon(col.field)"
                                :disabled="items.length === 0"
                                size="small" variant="text" severity="secondary"
                                @click="sort(col.field)" rounded></Button>
                        </div>
                    </th>
                </template>
                <th class="px-3 py-2 text-center min-w-[60px]">
                    <Button
                        icon="pi pi-objects-column" size="small" variant="text" severity="secondary"
                        @click="(e) => filterPopup?.toggle(e)" rounded></Button>
                    <Popover ref="filterPopup">
                        <div class="flex flex-col gap-1 w-[15rem]">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-sm">{{ $t('label.toggle_column') }}</span>
                                <div class="flex gap-x-1">
                                    <Button
                                        v-tooltip.bottom="$t('action.select_all')"
                                        :disabled="selected.length === items.length"
                                        icon="pi pi-list-check" size="small" variant="text" severity="secondary"
                                        @click="() => columns.forEach((c) => (c.isVisible = true))" rounded></Button>
                                    <Button
                                        v-tooltip.bottom="$t('action.save')"
                                        icon="pi pi-save" size="small" variant="text" severity="secondary"
                                        @click="save" rounded></Button>
                                </div>
                            </div>
                            <ul class="list-none p-0 m-0 flex flex-col">
                                <li class="flex items-center gap-2" v-for="col in columns" :key="col.field">
                                    <Checkbox
                                        :disabled="col.isVisible && columns.filter((c) => c.isVisible).length < 2"
                                        v-model="col.isVisible" size="small"
                                        :input-id="`toggle_column_${col.field}`" binary />
                                    <label :for="`toggle_column_${col.field}`">{{ $t(col.header) }}</label>
                                </li>
                            </ul>
                        </div>
                    </Popover>
                </th>
            </tr>
            </thead>
            <tbody>
            <template v-if="items.length > 0">
                <tr class="border-b border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800" v-for="item in items" :key="item.id">
                    <td class="px-3 py-2" v-if="selection">
                        <Checkbox
                            :value="item"
                            v-model="selected" />
                    </td>
                    <template v-for="col in columns" :key="col.field">
                        <td v-if="col.isVisible" class="px-3 py-2 dark:text-gray-300">
                            <template v-if="col.slot?.body">
                                <component :is="col.slot?.body" :row="item" />
                            </template>
                            <span v-else>{{ item[col.field] }}</span>
                        </td>
                    </template>
                    <td class="px-3 py-2 space-x-1 w-[60px]">
                        <slot name="action" :item="item"></slot>
                    </td>
                </tr>
            </template>
            <tr v-else>
                <td :colspan="columns?.filter((c) => c.isVisible)?.length + (selection ? 2 : 1)" class="px-3 py-2">
                    <slot name="empty">
                        <p class="text-center text-sm text-gray-600">{{ $t('label.no_data_available') }}</p>
                    </slot>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import {
    Button, Drawer, FloatLabel, InputText, MultiSelect,
    DatePicker,
} from 'primevue';
import type { InertiaForm } from '@inertiajs/vue3';
import type { FilterColumn } from '@/types';
import qs from 'qs';

interface Form {
    [key: string]: any;
    filters: Record<string, FilterColumn>;
}

const props = defineProps<{
    form: InertiaForm<Form>;
}>();

const visible = ref<boolean>(false);

const load = () => {
    const params = qs.parse(window.location.search, { ignoreQueryPrefix: true });
    for (const key in props.form.filters) {
        // eslint-disable-next-line vue/no-mutating-props
        props.form.filters[key].value = params['filters']?.[key]?.['value'] ?? null;
    }
};

const apply = () => {
    const url = new URL(window.location.href);
    url.search = '';
    props.form.get(url.toString(), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            visible.value = false;
            load();
        },
    });
};

const reset = () => {
    for (const key in props.form.filters) {
        // eslint-disable-next-line vue/no-mutating-props
        props.form.filters[key].value = null;
    }
    apply();
};

const isNotEmpty = (value: any): boolean => {
    if (value === '' || value === 0 || value === null || value === undefined)
        return false;

    return !(Array.isArray(value) && value.length === 0);
}

onMounted(load);
</script>

<template>
    <Button
        :severity="Object.values(form.filters).some(item => isNotEmpty(item.value)) ? 'primary' : 'secondary'"
        @click="() => visible = !visible" icon="pi pi-filter" size="small" />

    <Drawer v-model:visible="visible" position="right">
        <template #header>
            <div class="flex items-center gap-2">
                <span class="font-bold">{{ $t('label.filter') }}</span>
            </div>
        </template>

        <form class="h-full flex flex-col justify-between" @submit.prevent="apply">
            <div class="flex flex-col gap-6 pt-2 pb-8">
                <div v-for="(column, key) in form.filters" :key="column.label" class="flex gap-x-1">
                    <div class="flex-1">
                        <FloatLabel variant="on">
                            <MultiSelect
                                v-if="column.options && column.options.length > 0 && typeof column.options[0] === 'string'"
                                v-model="form.filters[key].value" :options="column.options" filter show-clear :max-selected-labels="3"
                                :input-id="`filter_column_${column.label.replaceAll('.', '_')}`" :fluid="true"
                            />
                            <MultiSelect
                                v-else-if="column.options && column.options.length > 0 && typeof column.options[0] === 'object'"
                                v-model="form.filters[key].value" :options="column.options" filter show-clear
                                option-value="code" option-label="label" :max-selected-labels="3"
                                :input-id="`filter_column_${column.label.replaceAll('.', '_')}`" :fluid="true"
                            />
                            <DatePicker
                                v-else-if="column.matchMode.includes('date')"
                                :manual-input="false" date-format="dd/mm/yy"
                                :selection-mode="column.matchMode.includes('Between') ? 'range' : 'single'"
                                :input-id="`filter_column_${column.label.replaceAll('.', '_')}`" :fluid="true"
                                v-model="form.filters[key].value" />
                            <InputText
                                v-else
                                :fluid="true" :id="`filter_column_${column.label.replaceAll('.', '_')}`"
                                v-model="form.filters[key].value" type="text" autocomplete="off" />
                            <label :for="`filter_column_${column.label.replaceAll('.', '_')}`">
                                {{ $t(column.label) }}
                            </label>
                        </FloatLabel>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <Button
                    :label="$t('action.reset')" icon="pi pi-filter-slash" @click="reset"
                    class="flex-auto" size="small" severity="danger" text></Button>
                <Button
                    :label="$t('action.filter')" type="submit"
                    class="flex-auto" size="small" outlined></Button>
            </div>
        </form>

    </Drawer>
</template>

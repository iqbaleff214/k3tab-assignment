<script setup lang="ts">
import type {
    ActivityLog as ActivityLogBase, APIResponse,
    CursorPaginate, SharedData, User
} from '@/types';
import { Dialog, Button, Skeleton, Timeline } from 'primevue';
import { onMounted, ref } from 'vue';
import { dateHumanFormatWithTime } from '@/lib/utils';
import { usePage } from '@inertiajs/vue3';

interface ActivityLog extends ActivityLogBase {
    causer: User;
}

const page = usePage<SharedData>();
const loading = ref<boolean>(false);
const items = ref<ActivityLog[]>([]);
const nextCursor = ref<string | null>(null);
const prevCursor = ref<string | null>(null);

const visible = ref<boolean>(false);
const props = defineProps<{
    module: string;
    id: string | number;
}>();

const load = (cursor?: string | null) => {
    loading.value = true;
    fetch(route('json.activity-log.index', {...props, cursor}))
        .then(response => response.json())
        .then((data: APIResponse<CursorPaginate<ActivityLog>>) => {
            if (data.error || !data.data)
                return;

            items.value = data.data.data;
            nextCursor.value = data.data.next_cursor;
            prevCursor.value = data.data.prev_cursor;
        }).finally(() => loading.value = false);
};

onMounted(load);
</script>

<template>
    <Button
        v-tooltip.bottom="$t('menu.activity_log')"
        icon="pi pi-history" size="small" variant="text" severity="secondary"
        @click="() => visible = true" rounded></Button>

    <Dialog
        v-model:visible="visible" modal
        :header="$t('menu.activity_log')" :style="{ width: '50rem' }">
        <Timeline :value="items" class="w-full" align="alternate" v-if="!loading">
            <template #marker="{ item }: { item: ActivityLog }">
                <span class="flex w-8 h-8 bg-emerald-500 text-white items-center justify-center rounded-full z-10 shadow-sm">
                    <i :class="item.icon" style="font-size: 0.75rem"></i>
                </span>
            </template>
            <template #content="{ item, index }: { item: ActivityLog, index: number }">
                <div class="bg-card text-card-foreground flex flex-col gap-3 rounded-2xl border p-3 shadow-xs">
                    <p v-html="item.description"></p>
                    <div class="flex flex-col">
                        <small class="text-xs text-gray-500 font-semibold">{{ item.causer.name }}</small>
                        <small class="text-xs text-gray-500">{{ dateHumanFormatWithTime(item.created_at, 0, page.props.auth.user.locale) }}</small>
                    </div>
                    <div v-if="!Array.isArray(item.properties)"
                        class="text-card-foreground flex flex-col gap-2 rounded-lg border p-3 shadow-xs bg-gray-50">
                        <div v-for="(newValue, field) in item.properties['attributes']" :key="field" class="flex flex-col items-start">
                            <small class="text-xs text-gray-500">{{ $t(`field.${field}`) }}</small>
                            <div class="flex gap-1.5 items-center">
                                <template v-if="item.properties['old']?.[field]">
                                    <h3 class="font-medium text-sm">
                                        {{ item.properties['old'][field] }}
                                    </h3>
                                    <i class="pi pi-arrow-right" style="font-size: 0.5rem"></i>
                                </template>
                                <h3 class="font-medium text-sm text-emerald-600">
                                    {{ newValue }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </Timeline>
        <Timeline :value="Array.from({ length: 15 }, (_, i) => i + 1)" align="alternate" v-else>
            <template #marker>
                <Skeleton shape="circle" size="32px"></Skeleton>
            </template>
            <template #content>
                <Skeleton height="100px" border-radius="16px" />
            </template>
        </Timeline>

        <template #footer>
            <div class="flex justify-end gap-2">
                <Button
                    v-tooltip.bottom="$t('label.newer')"
                    :disabled="prevCursor === null" @click="load(prevCursor)"
                    icon="pi pi-chevron-left" size="small" variant="text" severity="secondary"
                    rounded></Button>
                <Button
                    v-tooltip.bottom="$t('label.older')"
                    :disabled="nextCursor === null" @click="load(nextCursor)"
                    icon="pi pi-chevron-right" size="small" variant="text" severity="secondary"
                    rounded></Button>
            </div>
        </template>
    </Dialog>
</template>

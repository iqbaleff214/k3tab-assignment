<script setup lang="ts">
import type { ActivityLog as ActivityLogBase, User } from '@/types';
import { ref } from 'vue';
import { dateHumanFormatWithTime } from '@/lib/utils';
import { Dialog } from 'primevue';
import { useI18n } from 'vue-i18n';

interface ActivityLog extends ActivityLogBase {
    causer: User;
}

const { locale } = useI18n();
const visible = ref<boolean>(false);
const item = ref<ActivityLog|null>(null);

const open = (activityLog: ActivityLog) => {
    visible.value = true;
    item.value = activityLog;
}

const close = () => {
    visible.value = false
    item.value = null;
}

defineExpose({
    open,
})
</script>

<template>
    <Dialog
        v-model:visible="visible" modal @after-hide="close"
        :header="$t('menu.activity_log')" :style="{ width: '50rem' }">
        <div class="flex flex-col gap-3" v-if="item">
            <p v-html="item.description"></p>
            <div class="flex flex-col">
                <small class="text-xs text-gray-500 font-semibold">{{ item.causer.name }}</small>
                <small class="text-xs text-gray-500">{{ dateHumanFormatWithTime(item.created_at, 0, locale) }}</small>
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
    </Dialog>
</template>

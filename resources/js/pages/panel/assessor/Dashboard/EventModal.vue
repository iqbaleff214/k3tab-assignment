<script setup lang="ts">
import { ref } from 'vue';
import type { CalendarEvent } from '@/types';
import {
    Dialog, Button
} from 'primevue';
import { useI18n } from 'vue-i18n';

const visible = ref<boolean>(false);
const selectedEvent = ref<CalendarEvent>();
const { t } = useI18n();

const open = (event: CalendarEvent) => {
    visible.value = true;
    selectedEvent.value = event;
};

const close = () => {
    visible.value = false;
};

defineExpose({
    open,
});
</script>

<template>
    <Dialog
        v-model:visible="visible" modal maximizable
        @after-hide="close" :header="selectedEvent?.title ?? t('menu.assessment')" :style="{ width: '30rem' }">
        <div class="flex flex-col gap-6 pt-2 pb-8">
            {{ selectedEvent?.extendedProps?.detail }}
        </div>

        <div class="flex justify-end gap-2">
            <Button v-if="selectedEvent?.extendedProps?.detail.status === 'scheduled'"
                type="button" size="small" :label="t('action.cancel')"
                severity="secondary" @click="close" />
        </div>
    </Dialog>
</template>

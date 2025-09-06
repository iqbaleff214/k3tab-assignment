<script setup lang="ts">
import { ref } from 'vue';
import type { Assessment, AssessmentSchedule } from '@/types';
import {
    Dialog, Button, DatePicker, FloatLabel, Message, RadioButton
} from 'primevue';
import { useI18n } from 'vue-i18n';
import { useForm } from '@inertiajs/vue3';

const visible = ref<boolean>(false);
const selectedAssessment = ref<Assessment>();
const selectedSchedule = ref<AssessmentSchedule>();
const { t } = useI18n();

const form = useForm<{
    [key: string]: any;
    scheduled_at: Date | null;
    assessment_scheduled_id: number | null;
}>({
    scheduled_at: null,
    assessment_scheduled_id: null,
});

const open = (assessment: Assessment) => {
    visible.value = true;
    selectedAssessment.value = assessment;
};

const close = () => {
    visible.value = false;
};

const setSchedule = (schedule: AssessmentSchedule) => {
    form.scheduled_at = new Date(schedule.proposed_date);
    form.assessment_scheduled_id = schedule.id;
    selectedSchedule.value = schedule;
};

const submit = () => {
    form.post(route('assessor.assessment.proposal', { assessment: selectedAssessment.value?.id }), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: close,
    });
};

const reject = () => {
    form
        .transform(() => ({ scheduled_at: null, assessment_schedule_id: null }))
        .post(route('assessor.assessment.proposal', { assessment: selectedAssessment.value?.id }), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: close,
    });
};

defineExpose({
    open,
});
</script>

<template>
    <Dialog
        v-model:visible="visible" modal
        @after-hide="close" :header="selectedAssessment?.guide?.skill_number ?? t('menu.assessment')" :style="{ width: '30rem' }">
        <form @submit.prevent="submit">
            <div class="flex flex-col gap-6 pt-2 pb-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="flex items-center gap-2" v-for="schedule in selectedAssessment?.schedules" :key="schedule.id">
                        <RadioButton
                            v-model="form.assessment_scheduled_id"
                            :input-id="`radio_${schedule.id}`" name="assessment_scheduled_id"
                            :value="schedule.id" @value-change="() => setSchedule(schedule)" />
                        <label :for="`radio_${schedule.id}`">
                            {{ new Date(schedule.proposed_date).toLocaleDateString("en-GB", { day: "2-digit", month: "2-digit" }) }}
                        </label>
                    </div>
                </div>
                <div>
                    <FloatLabel variant="on">
                        <DatePicker
                            :min-date="new Date(selectedSchedule?.proposed_date)"
                            :max-date="new Date(new Date(selectedSchedule?.proposed_date).getTime() + (23 * 60 + 59) * 60 * 1000)"
                            :fluid="true" :autofocus="true" id="scheduled_at" :invalid="form.errors.scheduled_at !== undefined"
                            v-model="form.scheduled_at" show-time hour-format="24" :disabled="form.assessment_scheduled_id === null" />
                        <label for="scheduled_at" class="text-sm">{{ $t('field.scheduled_at') }}</label>
                    </FloatLabel>
                    <Message v-if="form.errors.scheduled_at" severity="error" size="small" variant="simple">
                        {{ form.errors.scheduled_at }}
                    </Message>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <Button type="button" size="small" :label="t('action.reject')" :loading="form.processing" :disabled="form.processing" variant="outlined" severity="danger" @click="reject"></Button>
                <Button type="submit" size="small" :label="t('action.approve')" :loading="form.processing" :disabled="form.processing"></Button>
            </div>
        </form>
    </Dialog>
</template>

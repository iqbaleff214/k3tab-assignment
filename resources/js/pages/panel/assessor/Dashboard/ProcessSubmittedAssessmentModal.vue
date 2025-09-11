<script setup lang="ts">
import { ref } from 'vue';
import type { Assessment, AssessmentSchedule } from '@/types';
import {
    Dialog, Button, DatePicker, FloatLabel, Message, RadioButton, Textarea
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
    feedback: string;
}>({
    scheduled_at: null,
    assessment_scheduled_id: null,
    feedback: '',
});

const open = (assessment: Assessment) => {
    visible.value = true;
    selectedAssessment.value = assessment;
};

const close = () => {
    visible.value = false;
    form.reset();
    selectedAssessment.value = undefined;
    selectedSchedule.value = undefined;
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
        .transform(() => ({ scheduled_at: null, assessment_schedule_id: null, feedback: form.feedback }))
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
        @after-hide="close" :header="selectedAssessment?.guide?.skill_number ?? t('menu.assessment')" :style="{ width: '40rem' }">
        <form @submit.prevent="submit">
            <div class="flex flex-col gap-6 pt-2 pb-8">
                <!-- Assessment Info -->
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ t('menu.assessment') }} {{ t('field.information') }}</h4>
                    <div class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                        <p><span class="font-medium">{{ t('field.assessee') }}:</span> {{ selectedAssessment?.assessee_name }}</p>
                        <p><span class="font-medium">{{ t('field.skill_number') }}:</span> {{ selectedAssessment?.guide?.skill_number }}</p>
                        <p><span class="font-medium">{{ t('field.assessee_school') }}:</span> {{ selectedAssessment?.assessee_school }}</p>
                    </div>
                </div>

                <!-- Date Selection -->
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-3">{{ t('field.proposed_dates') }}</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <div class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                             v-for="schedule in selectedAssessment?.schedules" :key="schedule.id"
                             :class="{ 'border-blue-500 bg-blue-50 dark:bg-blue-900/20': form.assessment_scheduled_id === schedule.id }">
                            <RadioButton
                                v-model="form.assessment_scheduled_id"
                                :input-id="`radio_${schedule.id}`" name="assessment_scheduled_id"
                                :value="schedule.id" @value-change="() => setSchedule(schedule)" />
                            <label :for="`radio_${schedule.id}`" class="cursor-pointer text-sm">
                                {{ new Date(schedule.proposed_date).toLocaleDateString("en-GB", {
                                    weekday: 'short',
                                    day: "2-digit",
                                    month: "short",
                                    year: 'numeric'
                                }) }}
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Time Selection -->
                <div>
                    <FloatLabel variant="on">
                        <DatePicker
                            :min-date="selectedSchedule?.proposed_date ? new Date(selectedSchedule.proposed_date) : undefined"
                            :max-date="selectedSchedule?.proposed_date ? new Date(new Date(selectedSchedule.proposed_date).getTime() + (23 * 60 + 59) * 60 * 1000) : undefined"
                            :fluid="true" id="scheduled_at" :invalid="form.errors.scheduled_at !== undefined"
                            v-model="form.scheduled_at" show-time hour-format="24" :disabled="form.assessment_scheduled_id === null" />
                        <label for="scheduled_at" class="text-sm">{{ t('field.scheduled_at') }}</label>
                    </FloatLabel>
                    <Message v-if="form.errors.scheduled_at" severity="error" size="small" variant="simple">
                        {{ form.errors.scheduled_at }}
                    </Message>
                </div>

                <!-- Feedback Section -->
                <div>
                    <FloatLabel variant="on">
                        <Textarea
                            id="feedback"
                            v-model="form.feedback"
                            :invalid="form.errors.feedback !== undefined"
                            :fluid="true"
                            rows="4"
                            class="resize-none" />
                        <label for="feedback" class="text-sm">{{ t('field.feedback') }} ({{ t('field.optional') }})</label>
                    </FloatLabel>
                    <Message v-if="form.errors.feedback" severity="error" size="small" variant="simple">
                        {{ form.errors.feedback }}
                    </Message>
                    <small class="text-gray-500 dark:text-gray-400 mt-1 block">
                        {{ t('field.feedback_description') }}
                    </small>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ t('field.select_date_and_time') }}
                </div>
                <div class="flex gap-3">
                    <Button
                        type="button"
                        :label="t('action.reject')"
                        :loading="form.processing"
                        :disabled="form.processing"
                        severity="danger"
                        variant="outlined"
                        size="small"
                        @click="reject">
                        <template #icon>
                            <i class="pi pi-times"></i>
                        </template>
                    </Button>
                    <Button
                        type="submit"
                        :label="t('action.approve')"
                        :loading="form.processing"
                        :disabled="form.processing || !form.assessment_scheduled_id || !form.scheduled_at"
                        size="small">
                        <template #icon>
                            <i class="pi pi-check"></i>
                        </template>
                    </Button>
                </div>
            </div>
        </form>
    </Dialog>
</template>

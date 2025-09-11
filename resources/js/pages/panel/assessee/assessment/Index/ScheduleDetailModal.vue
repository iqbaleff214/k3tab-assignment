<script setup lang="ts">
import { ref } from 'vue';
import type { Assessment } from '@/types';
import {
    Dialog, Button, InputText, FloatLabel, Message
} from 'primevue';
import { useI18n } from 'vue-i18n';
import { dateHumanFormatWithTime } from '@/lib/utils';

const visible = ref<boolean>(false);
const selectedAssessment = ref<Assessment>();
const { t, locale } = useI18n();

const open = (assessment: Assessment) => {
    visible.value = true;
    selectedAssessment.value = assessment;
};

const close = () => {
    visible.value = false;
    selectedAssessment.value = undefined;
};

const getStatusConfig = (status: string) => {
    switch (status) {
        case 'pending':
            return {
                bgClass: 'bg-yellow-50 dark:bg-yellow-900/20',
                borderClass: 'border-yellow-200 dark:border-yellow-800',
                textClass: 'text-yellow-800 dark:text-yellow-200',
                iconClass: 'text-yellow-600 dark:text-yellow-400',
                icon: 'pi-clock'
            };
        case 'scheduled':
            return {
                bgClass: 'bg-blue-50 dark:bg-blue-900/20',
                borderClass: 'border-blue-200 dark:border-blue-800',
                textClass: 'text-blue-800 dark:text-blue-200',
                iconClass: 'text-blue-600 dark:text-blue-400',
                icon: 'pi-calendar-plus'
            };
        case 'cancelled':
            return {
                bgClass: 'bg-red-50 dark:bg-red-900/20',
                borderClass: 'border-red-200 dark:border-red-800',
                textClass: 'text-red-800 dark:text-red-200',
                iconClass: 'text-red-600 dark:text-red-400',
                icon: 'pi-times-circle'
            };
        default:
            return {
                bgClass: 'bg-gray-50 dark:bg-gray-900/20',
                borderClass: 'border-gray-200 dark:border-gray-800',
                textClass: 'text-gray-800 dark:text-gray-200',
                iconClass: 'text-gray-600 dark:text-gray-400',
                icon: 'pi-info-circle'
            };
    }
};

defineExpose({
    open,
});
</script>

<template>
    <Dialog
        v-model:visible="visible" modal
        @after-hide="close"
        :header="t('label.assessment_schedule_details')"
        :style="{ width: '50rem' }">

        <div class="flex flex-col gap-6 pt-2 pb-8" v-if="selectedAssessment">
            <!-- Assessment Header Info -->
            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ selectedAssessment.guide?.skill_number }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                            {{ selectedAssessment.guide?.title }}
                        </p>
                    </div>
                    <div :class="getStatusConfig(selectedAssessment.status).bgClass + ' ' + getStatusConfig(selectedAssessment.status).borderClass"
                         class="px-3 py-2 rounded-lg border">
                        <div class="flex items-center gap-2">
                            <i :class="'pi ' + getStatusConfig(selectedAssessment.status).icon + ' ' + getStatusConfig(selectedAssessment.status).iconClass"></i>
                            <span :class="getStatusConfig(selectedAssessment.status).textClass" class="text-sm font-medium">
                                {{ t(`label.${selectedAssessment.status}`) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment Basic Info -->
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <FloatLabel variant="on">
                        <InputText
                            :fluid="true" id="assessor_name" disabled
                            :model-value="selectedAssessment.assessor?.name" />
                        <label for="assessor_name" class="text-sm">{{ t('field.assessor') }}</label>
                    </FloatLabel>
                </div>
                <div>
                    <FloatLabel variant="on">
                        <InputText
                            :fluid="true" id="assessor_email" disabled
                            :model-value="selectedAssessment.assessor?.email" />
                        <label for="assessor_email" class="text-sm">{{ t('field.email') }}</label>
                    </FloatLabel>
                </div>
                <div>
                    <FloatLabel variant="on">
                        <InputText
                            :fluid="true" id="assessee_school" disabled
                            :model-value="selectedAssessment.assessee_school || 'N/A'" />
                        <label for="assessee_school" class="text-sm">{{ t('field.assessee_school') }}</label>
                    </FloatLabel>
                </div>
                <div>
                    <FloatLabel variant="on">
                        <InputText
                            :fluid="true" id="created_date" disabled
                            :model-value="dateHumanFormatWithTime(selectedAssessment.created_at, 0, locale)" />
                        <label for="created_date" class="text-sm">{{ t('field.created_at') }}</label>
                    </FloatLabel>
                </div>
            </div>

            <!-- Schedule Information -->
            <div class="space-y-4">
                <h4 class="font-semibold text-gray-900 dark:text-white">
                    {{ t('label.schedule_information') }}
                </h4>

                <!-- Proposed Dates (for pending/cancelled) -->
                <div v-if="selectedAssessment.schedules && selectedAssessment.schedules.length > 0">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 block">
                        {{ t('field.proposed_dates') }}:
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <div v-for="schedule in selectedAssessment.schedules" :key="schedule.id"
                             class="border rounded-lg p-3 text-center"
                             :class="{
                                'border-blue-500 bg-blue-50 dark:bg-blue-900/20': schedule.current_status === 'accepted',
                                'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800': schedule.current_status === 'proposed',
                                'border-red-300 dark:border-red-600 bg-red-50 dark:bg-red-900/20': schedule.current_status === 'rejected'
                             }">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ new Date(schedule.proposed_date).toLocaleDateString("en-GB", {
                                    weekday: 'short',
                                    day: "2-digit",
                                    month: "short",
                                    year: 'numeric'
                                }) }}
                            </div>
                            <div class="text-xs mt-1"
                                 :class="{
                                    'text-blue-600 dark:text-blue-400': schedule.current_status === 'accepted',
                                    'text-gray-500 dark:text-gray-400': schedule.current_status === 'proposed',
                                    'text-red-600 dark:text-red-400': schedule.current_status === 'rejected'
                                 }">
                                {{ t(`label.${schedule.current_status}`) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scheduled Time (for scheduled assessments) -->
                <div v-if="selectedAssessment.status === 'scheduled' && selectedAssessment.scheduled_at">
                    <FloatLabel variant="on">
                        <InputText
                            :fluid="true" id="scheduled_time" disabled
                            :model-value="dateHumanFormatWithTime(selectedAssessment.scheduled_at, 0, locale)" />
                        <label for="scheduled_time" class="text-sm">{{ t('field.scheduled_at') }}</label>
                    </FloatLabel>
                </div>
            </div>

            <!-- Assessor Feedback (for cancelled assessments) -->
            <div v-if="selectedAssessment.status === 'cancelled' && selectedAssessment.feedback">
                <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <i class="pi pi-info-circle text-orange-600 dark:text-orange-400 text-lg mt-0.5 flex-shrink-0"></i>
                        <div class="flex-1">
                            <h5 class="font-medium text-orange-800 dark:text-orange-200 mb-2">
                                {{ t('field.assessor_feedback') }}:
                            </h5>
                            <p class="text-sm text-orange-700 dark:text-orange-300">
                                {{ selectedAssessment.feedback }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else-if="selectedAssessment.feedback">
                <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <i class="pi pi-info-circle text-orange-600 dark:text-orange-400 text-lg mt-0.5 flex-shrink-0"></i>
                        <div class="flex-1">
                            <h5 class="font-medium text-orange-800 dark:text-orange-200 mb-2">
                                {{ t('field.assessor_feedback') }}:
                            </h5>
                            <p class="text-sm text-orange-700 dark:text-orange-300">
                                {{ selectedAssessment.feedback }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status-specific Messages -->
            <div class="mt-4">
                <Message v-if="selectedAssessment.status === 'pending'" severity="info" :closable="false">
                    <span class="text-sm">
                        {{ t('message.pending_assessment_info') }}
                    </span>
                </Message>

                <Message v-else-if="selectedAssessment.status === 'scheduled'" severity="success" :closable="false">
                    <span class="text-sm">
                        {{ t('message.scheduled_assessment_info') }}
                    </span>
                </Message>

                <Message v-else-if="selectedAssessment.status === 'cancelled'" severity="warn" :closable="false">
                    <span class="text-sm">
                        {{ t('message.cancelled_assessment_info') }}
                    </span>
                </Message>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2">
                <Button
                    :label="t('action.close')"
                    severity="secondary"
                    variant="outlined"
                    @click="close" />
            </div>
        </template>
    </Dialog>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Assessment, type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Calendar from '@/pages/panel/assessor/Dashboard/Calendar.vue';
import { Button } from 'primevue';
import ProcessSubmittedAssessmentModal from '@/pages/panel/assessor/Dashboard/ProcessSubmittedAssessmentModal.vue';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.dashboard',
        href: route('assessor.dashboard'),
    },
];

const props = defineProps<{
    assessments: Assessment[];
}>();

const { t } = useI18n();
const processSubmittedAssessmentModal = ref();
</script>

<template>
    <Head :title="t('menu.dashboard')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid gap-6 md:grid-cols-3">
                <div class="md:col-span-2">
                    <Calendar />
                </div>

                <div>
                    <h3 class="font-medium text-amber-600 mb-2">
                        {{ t('label.proposed_assessment') }}
                    </h3>
                    <div class="flex flex-col gap-2">
                        <template v-if="assessments.length > 0">
                            <div v-for="assessment in assessments" :key="assessment.id"
                                 class="flex flex-col gap-1.5 bg-slate-50 dark:bg-slate-900 rounded-xl py-4 px-4">
                                <div class="flex justify-between">
                                    <div class="flex flex-col">
                                        <h3 class="font-semibold">{{ assessment.assessee_name }}</h3>
                                        <span class="text-sm text-gray-500">{{ assessment.guide?.skill_number }}</span>
                                    </div>
                                    <div>
                                        <Button
                                            icon="pi pi-angle-double-right"
                                            v-tooltip.bottom="t('action.process')"
                                            size="small" variant="outlined" rounded
                                            @click="() => processSubmittedAssessmentModal?.open(assessment)" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-4 md:grid-cols-6 gap-2">
                                    <div v-for="schedule in assessment.schedules"
                                         class="text-center rounded-lg dark:bg-amber-900 dark:text-amber-50 bg-amber-100 text-amber-800 py-2 text-sm"
                                         :key="schedule.id">
                                        {{ new Date(schedule.proposed_date).toLocaleDateString("en-GB", { day: "2-digit", month: "2-digit", timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone }) }}
                                    </div>
                                </div>
                            </div>
                        </template>
                        <p class="text-sm text-gray-500" v-else>
                            {{ t('label.no_assessment_proposed_to_me') }}
                        </p>
                    </div>
                </div>
            </div>

            <ProcessSubmittedAssessmentModal
                ref="processSubmittedAssessmentModal" />
        </div>
    </AppLayout>
</template>

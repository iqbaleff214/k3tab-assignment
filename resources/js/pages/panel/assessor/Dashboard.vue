<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Assessment, type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Calendar from '@/pages/panel/assessor/Dashboard/Calendar.vue';
import { Button, Card, Chip, Tag, Badge } from 'primevue';
import ProcessSubmittedAssessmentModal from '@/pages/panel/assessor/Dashboard/ProcessSubmittedAssessmentModal.vue';
import { ref, computed, onMounted, nextTick } from 'vue';
import {
    Chart as ChartJS,
    ArcElement,
    Tooltip,
    Legend,
    DoughnutController,
    PieController,
    type ChartConfiguration
} from 'chart.js';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.dashboard',
        href: route('assessor.dashboard'),
    },
];

interface DashboardMetrics {
    counts: {
        total: number;
        pending: number;
        scheduled: number;
        completed: number;
        cancelled: number;
    };
    competency: {
        total_evaluated: number;
        competent: number;
        not_competent: number;
    };
}

const props = defineProps<{
    assessments: Assessment[];
    metrics: DashboardMetrics;
    recentCompleted: Assessment[];
    upcomingScheduled: Assessment[];
}>();

const { t } = useI18n();
const processSubmittedAssessmentModal = ref();

// Register Chart.js components
ChartJS.register(ArcElement, Tooltip, Legend, DoughnutController, PieController);

// Chart refs
const statusChartRef = ref<HTMLCanvasElement>();
const competencyChartRef = ref<HTMLCanvasElement>();

// Chart data for assessment status distribution
const statusChartData = computed(() => ({
    labels: ['Pending', 'Scheduled', 'Completed', 'Cancelled'],
    datasets: [{
        data: [
            props.metrics.counts.pending,
            props.metrics.counts.scheduled,
            props.metrics.counts.completed,
            props.metrics.counts.cancelled
        ],
        backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
        hoverBackgroundColor: ['#d97706', '#2563eb', '#059669', '#dc2626']
    }]
}));

// Chart data for competency results
const competencyChartData = computed(() => ({
    labels: ['Competent', 'Not Competent'],
    datasets: [{
        data: [props.metrics.competency.competent, props.metrics.competency.not_competent],
        backgroundColor: ['#10b981', '#ef4444'],
        hoverBackgroundColor: ['#059669', '#dc2626']
    }]
}));

const chartOptions: ChartConfiguration['options'] = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom' as const,
        }
    }
};

// Initialize charts
const initializeCharts = async () => {
    await nextTick();

    try {
        if (statusChartRef.value && props.metrics.counts.total > 0) {
            const ctx = statusChartRef.value.getContext('2d');
            if (ctx) {
                console.log('Creating status chart with data:', statusChartData.value);
                new ChartJS(ctx, {
                    type: 'doughnut',
                    data: statusChartData.value,
                    options: chartOptions
                });
            }
        }

        if (competencyChartRef.value && props.metrics.competency.total_evaluated > 0) {
            const ctx = competencyChartRef.value.getContext('2d');
            if (ctx) {
                console.log('Creating competency chart with data:', competencyChartData.value);
                new ChartJS(ctx, {
                    type: 'pie',
                    data: competencyChartData.value,
                    options: chartOptions
                });
            }
        }
    } catch (error) {
        console.error('Error initializing charts:', error);
    }
};

onMounted(initializeCharts);

// Compute competency rate
const competencyRate = computed(() => {
    if (props.metrics.competency.total_evaluated === 0) return 0;
    return Math.round((props.metrics.competency.competent / props.metrics.competency.total_evaluated) * 100);
});
</script>

<template>
    <Head :title="t('menu.dashboard')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Metrics Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Assessments Card -->
                <Card class="shadow-sm border border-gray-200 dark:border-gray-700">
                    <template #content>
                        <div class="flex items-center justify-between p">
                            <div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ metrics.counts.total }}</div>
                                <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Assessments</div>
                            </div>
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-3">
                                <i class="pi pi-chart-line text-2xl text-gray-600 dark:text-gray-400"></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Pending Assessments Card -->
                <Card class="shadow-sm border border-gray-200 dark:border-gray-700">
                    <template #content>
                        <div class="flex items-center justify-between p">
                            <div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ metrics.counts.pending }}</div>
                                <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">Pending Reviews</div>
                            </div>
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-3">
                                <i class="pi pi-clock text-2xl text-gray-600 dark:text-gray-400"></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Completed Assessments Card -->
                <Card class="shadow-sm border border-gray-200 dark:border-gray-700">
                    <template #content>
                        <div class="flex items-center justify-between p">
                            <div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ metrics.counts.completed }}</div>
                                <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">Completed</div>
                            </div>
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-3">
                                <i class="pi pi-check-circle text-2xl text-gray-600 dark:text-gray-400"></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Competency Rate Card -->
                <Card class="shadow-sm border border-gray-200 dark:border-gray-700">
                    <template #content>
                        <div class="flex items-center justify-between p">
                            <div class="flex-1">
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ competencyRate }}%</div>
                                <div class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-3">Competency Rate</div>
                                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                    <div
                                        class="bg-gray-600 dark:bg-gray-400 h-full rounded-full transition-all duration-300 ease-out"
                                        :style="{ width: `${competencyRate}%` }"
                                    ></div>
                                </div>
                            </div>
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-3 ml-4">
                                <i class="pi pi-star text-2xl text-gray-600 dark:text-gray-400"></i>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Main Dashboard Content -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Calendar and Quick Actions (Left Side) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Calendar -->
                    <Card class="shadow-sm border border-gray-200 dark:border-gray-700">
                        <template #title>
                            <div class="flex items-center gap-2">
                                <i class="pi pi-calendar text-gray-600 dark:text-gray-400"></i>
                                <span class="text-gray-900 dark:text-gray-100">{{ t('label.assessment_calendar') }}</span>
                            </div>
                        </template>
                        <template #content>
                            <Calendar />
                        </template>
                    </Card>

                    <!-- Analytics Charts -->
                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- Assessment Status Distribution -->
                        <Card class="shadow-sm border border-gray-200 dark:border-gray-700">
                            <template #title>
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-chart-pie text-gray-600 dark:text-gray-400"></i>
                                    <span class="text-gray-900 dark:text-gray-100">Assessment Status Distribution</span>
                                </div>
                            </template>
                            <template #content>
                                <div v-if="metrics.counts.total > 0" class="h-64 flex items-center justify-center">
                                    <canvas ref="statusChartRef" class="max-w-full max-h-full"></canvas>
                                </div>
                                <div v-else class="h-64 flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                    <i class="pi pi-chart-pie text-4xl mb-3 text-gray-300 dark:text-gray-600"></i>
                                    <p class="text-center font-medium">No assessment data available</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500">Charts will appear once you have assessments</p>
                                </div>
                            </template>
                        </Card>

                        <!-- Competency Results -->
                        <Card class="shadow-sm border border-gray-200 dark:border-gray-700">
                            <template #title>
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-chart-bar text-gray-600 dark:text-gray-400"></i>
                                    <span class="text-gray-900 dark:text-gray-100">Competency Results</span>
                                </div>
                            </template>
                            <template #content>
                                <div v-if="metrics.competency.total_evaluated > 0" class="h-64 flex items-center justify-center">
                                    <canvas ref="competencyChartRef" class="max-w-full max-h-full"></canvas>
                                </div>
                                <div v-else class="h-64 flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                    <i class="pi pi-chart-bar text-4xl mb-3 text-gray-300 dark:text-gray-600"></i>
                                    <p class="text-center font-medium">No competency data available</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500">Results will appear after assessments are completed</p>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="space-y-6">
                    <!-- Enhanced Proposed Assessments -->
                    <Card class="shadow-sm border border-gray-200 dark:border-gray-700">
                        <template #title>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-bell text-gray-600 dark:text-gray-400"></i>
                                    <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ t('label.proposed_assessment') }}</span>
                                </div>
                                <Badge :value="assessments.length" v-if="assessments.length > 0" class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300" />
                            </div>
                        </template>
                        <template #content>
                            <div class="space-y-4">
                                <template v-if="assessments.length > 0">
                                    <div v-for="assessment in assessments" :key="assessment.id"
                                         class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 hover:shadow-sm transition-all">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-900 dark:text-gray-100 text-base">
                                                    {{ assessment.assessee_name }}
                                                </h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-2">
                                                    {{ assessment.guide?.skill_number }}
                                                </p>
                                                <Tag icon="pi pi-user" :value="assessment.assessee?.name || 'N/A'"
                                                     class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300" />
                                            </div>
                                            <Button
                                                icon="pi pi-arrow-right"
                                                label="Process"
                                                size="small"
                                                class="bg-gray-700 hover:bg-gray-800 text-white border-gray-700"
                                                @click="() => processSubmittedAssessmentModal?.open(assessment)" />
                                        </div>

                                        <div class="space-y-3">
                                            <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">Proposed Dates:</p>
                                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                                <Chip v-for="schedule in assessment.schedules"
                                                      :key="schedule.id"
                                                      class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium">
                                                    {{ new Date(schedule.proposed_date).toLocaleDateString("en-GB", {
                                                        day: "2-digit",
                                                        month: "2-digit",
                                                        year: "2-digit",
                                                        timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone
                                                    }) }}
                                                </Chip>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div v-else class="text-center py-12 text-gray-500 dark:text-gray-400">
                                    <i class="pi pi-info-circle text-4xl mb-4 text-gray-300 dark:text-gray-600"></i>
                                    <p class="font-medium mb-2">{{ t('label.no_assessment_proposed_to_me') }}</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500">New assessment proposals will appear here</p>
                                </div>
                            </div>
                        </template>
                    </Card>

                    <!-- Upcoming Scheduled Assessments -->
                    <Card v-if="upcomingScheduled.length > 0" class="shadow-sm border border-gray-200 dark:border-gray-700">
                        <template #title>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-calendar text-gray-600 dark:text-gray-400"></i>
                                    <span class="text-gray-900 dark:text-gray-100 font-semibold">Upcoming Scheduled</span>
                                </div>
                                <Badge :value="upcomingScheduled.length" class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300" />
                            </div>
                        </template>
                        <template #content>
                            <div class="space-y-3">
                                <div v-for="assessment in upcomingScheduled" :key="assessment.id"
                                     class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 hover:shadow-sm transition-all">
                                    <div class="flex justify-between items-center">
                                        <div class="flex-1">
                                            <h5 class="font-bold text-gray-900 dark:text-gray-100 text-base">
                                                {{ assessment.assessee_name }}
                                            </h5>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                                {{ assessment.guide?.skill_number }}
                                            </p>
                                        </div>
                                        <Tag :value="new Date(assessment.scheduled_at || '').toLocaleDateString()"
                                             class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium" />
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>

                    <!-- Recent Completed Assessments -->
                    <Card v-if="recentCompleted.length > 0" class="shadow-sm border border-gray-200 dark:border-gray-700">
                        <template #title>
                            <div class="flex items-center gap-2">
                                <i class="pi pi-check-circle text-gray-600 dark:text-gray-400"></i>
                                <span class="text-gray-900 dark:text-gray-100 font-semibold">Recently Completed</span>
                            </div>
                        </template>
                        <template #content>
                            <div class="space-y-3">
                                <div v-for="assessment in recentCompleted" :key="assessment.id"
                                     class="flex justify-between items-center py-3 px-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 hover:shadow-sm transition-all">
                                    <div class="flex-1">
                                        <h6 class="font-bold text-gray-900 dark:text-gray-100">{{ assessment.assessee_name }}</h6>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ assessment.guide?.skill_number }}</p>
                                    </div>
                                    <Tag :value="assessment.result"
                                         :class="assessment.result === 'competent' ?
                                           'bg-gray-700 text-white' :
                                           'bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200'"
                                         class="font-medium" />
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>

            <ProcessSubmittedAssessmentModal
                ref="processSubmittedAssessmentModal" />
        </div>
    </AppLayout>
</template>

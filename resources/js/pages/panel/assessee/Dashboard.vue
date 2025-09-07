<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type {
    Assessment,
    BreadcrumbItem, CalendarEvent,
    FilterColumn,
    Paginate,
} from '@/types';
import { Head, router, useForm, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, computed, onMounted, nextTick } from 'vue';
import { Button, ConfirmPopup, useConfirm } from 'primevue';
import EventModal from '@/pages/panel/assessee/assessment/Index/EventModal.vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';
import FullCalendar from '@fullcalendar/vue3';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    LineController,
    Title,
    Tooltip,
    Legend,
    Filler,
    type ChartConfiguration
} from 'chart.js';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.dashboard',
        href: route('assessee.dashboard'),
    },
];

const statuses = ['pending', 'scheduled', 'completed', 'cancelled'];

interface DashboardMetrics {
    assessments: {
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
    modules_enrolled: number;
    modules_with_post_test: number;
}

interface ModuleProgress {
    id: number;
    title: string;
    code: string;
    total_assessments: number;
    completed_assessments: number;
    completion_rate: number;
    post_test_completed: boolean;
    post_test_score: number;
    post_test_passed: boolean;
}

interface RecentAssessment {
    id: string;
    assessor_name: string;
    skill_number: string;
    status: string;
    result: string | null;
    scheduled_at: string | null;
    finished_at: string | null;
    created_at: string;
}

interface PerformanceTimelineItem {
    date: string;
    assessment_count: number;
    completed_count: number;
}

const props = defineProps<{
    items: Assessment[];
    events: CalendarEvent[];
    metrics: DashboardMetrics;
    module_progress: ModuleProgress[];
    recent_assessments: RecentAssessment[];
    performance_timeline: PerformanceTimelineItem[];
}>();

// Register Chart.js components
ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, LineController, Title, Tooltip, Legend, Filler);

// Chart refs
const performanceChartRef = ref<HTMLCanvasElement>();

const { t } = useI18n();

const eventModal = ref();
const confirm = useConfirm();

// Chart data for performance timeline
const performanceChartData = computed(() => ({
    labels: props.performance_timeline.map(item => item.date),
    datasets: [
        {
            label: 'Total Assessments',
            data: props.performance_timeline.map(item => item.assessment_count),
            borderColor: 'rgb(99, 102, 241)',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            fill: true,
            tension: 0.4,
        },
        {
            label: 'Completed',
            data: props.performance_timeline.map(item => item.completed_count),
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            fill: true,
            tension: 0.4,
        }
    ]
}));

const chartOptions: ChartConfiguration<'line'>['options'] = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top' as const,
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1,
            }
        }
    }
};

// Initialize performance chart
const initializeChart = async () => {
    await nextTick();
    
    try {
        if (performanceChartRef.value && props.performance_timeline.length > 0) {
            const ctx = performanceChartRef.value.getContext('2d');
            if (ctx) {
                new ChartJS(ctx, {
                    type: 'line',
                    data: performanceChartData.value,
                    options: chartOptions
                });
            }
        }
    } catch (error) {
        console.error('Error initializing performance chart:', error);
    }
};

onMounted(initializeChart);

// Computed values
const competencyRate = computed(() => {
    if (props.metrics.competency.total_evaluated === 0) return 0;
    return Math.round((props.metrics.competency.competent / props.metrics.competency.total_evaluated) * 100);
});

const overallProgress = computed(() => {
    if (props.metrics.assessments.total === 0) return 0;
    return Math.round((props.metrics.assessments.completed / props.metrics.assessments.total) * 100);
});


const filterForm = useForm<{ [key: string]: any; filters: Record<string, FilterColumn> }>({
    filters: {
        'guide.skill_number': {
            value: null,
            matchMode: 'equals',
            label: 'field.skill_number',
            canChange: true,
        },
        status: {
            value: null,
            matchMode: 'equals',
            label: 'field.status',
            canChange: true,
            options: statuses.map(s => ({ code: s, label: t(`label.${s}`) }))
        },
        scheduled_at: {
            value: null,
            matchMode: 'dateBetween',
            label: 'field.scheduled_at',
            canChange: true,
        },
        created_at: {
            value: null,
            matchMode: 'dateBetween',
            label: 'field.created_at',
            canChange: true,
        },
    }
});

const destroy = (event: MouseEvent, item: Assessment) => {
    confirm.require({
        target: event.currentTarget as HTMLElement,
        message: t('label.are_you_sure_delete'),
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: t('action.cancel'),
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: t('action.delete'),
            severity: 'danger',
        },
        accept: () => {
            router.delete(route('assessee.assessment.destroy', item.id), {
                preserveScroll: true,
                preserveState: true,
            });
        },
    });
};


const calendarOptions = ref({
    plugins: [ dayGridPlugin, interactionPlugin, timeGridPlugin ],
    initialView: 'dayGridMonth',
    eventClick: function(arg: any) {
        eventModal.value?.open(arg.event);
    },
    events: props.events,
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
});

</script>

<template>
    <Head :title="t('menu.dashboard')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            
            <!-- Metrics Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Assessments Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ metrics.assessments.total }}</div>
                            <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Assessments</div>
                            <div class="text-xs text-gray-500 mt-1">{{ metrics.assessments.completed }} completed, {{ metrics.assessments.pending }} pending</div>
                        </div>
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-3">
                            <i class="pi pi-clipboard text-2xl text-gray-600 dark:text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Modules Enrolled Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ metrics.modules_enrolled }}</div>
                            <div class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-3">Modules Enrolled</div>
                            <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div
                                    class="bg-gray-600 dark:bg-gray-400 h-full rounded-full transition-all duration-300 ease-out"
                                    :style="{ width: `${overallProgress}%` }"
                                ></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ overallProgress }}% Overall Progress</div>
                        </div>
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-3 ml-4">
                            <i class="pi pi-book text-2xl text-gray-600 dark:text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Competency Rate Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ competencyRate }}%</div>
                            <div class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-3">Competency Rate</div>
                            <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div
                                    class="bg-gray-600 dark:bg-gray-400 h-full rounded-full transition-all duration-300 ease-out"
                                    :style="{ width: `${competencyRate}%` }"
                                ></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ metrics.competency.competent }}/{{ metrics.competency.total_evaluated }} Competent</div>
                        </div>
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-3 ml-4">
                            <i class="pi pi-star text-2xl text-gray-600 dark:text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Post Tests Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ metrics.modules_with_post_test }}</div>
                            <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">Post Tests Completed</div>
                        </div>
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-3">
                            <i class="pi pi-check-circle text-2xl text-gray-600 dark:text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Content -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Calendar and Performance Chart Section (Left Side) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Calendar -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-calendar text-gray-600 dark:text-gray-400"></i>
                                <span class="text-gray-900 dark:text-gray-100 font-semibold">Assessment Schedule</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <FullCalendar :options="calendarOptions" />
                        </div>
                    </div>

                    <!-- Performance Timeline Chart -->
                    <div v-if="performance_timeline.length > 0" class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-chart-line text-gray-600 dark:text-gray-400"></i>
                                <span class="text-gray-900 dark:text-gray-100 font-semibold">Performance Timeline</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Assessment activity over the last 6 months</p>
                        </div>
                        <div class="p-6">
                            <div class="h-64">
                                <canvas ref="performanceChartRef"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Module Progress Section -->
                    <div v-if="module_progress.length > 0" class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-book text-gray-600 dark:text-gray-400"></i>
                                <span class="text-gray-900 dark:text-gray-100 font-semibold">Module Progress</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div v-for="module in module_progress" :key="module.id" 
                                     class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800/50">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-gray-100">{{ module.title }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ module.code }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ module.completion_rate }}%</p>
                                            <p class="text-xs text-gray-500">{{ module.completed_assessments }}/{{ module.total_assessments }} completed</p>
                                        </div>
                                    </div>
                                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden mb-3">
                                        <div
                                            class="bg-gray-600 dark:bg-gray-400 h-full rounded-full transition-all duration-300 ease-out"
                                            :style="{ width: `${module.completion_rate}%` }"
                                        ></div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-300 font-medium">Post Test:</span>
                                        <div class="flex items-center gap-2">
                                            <span v-if="module.post_test_completed" 
                                                  :class="module.post_test_passed ? 
                                                    'bg-gray-700 text-white' : 
                                                    'bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200'"
                                                  class="px-2 py-1 rounded text-xs font-medium">
                                                {{ module.post_test_passed ? 'Passed' : 'Failed' }}
                                            </span>
                                            <span v-else class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded text-xs font-medium">
                                                Not Completed
                                            </span>
                                            <span v-if="module.post_test_completed" class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                                {{ module.post_test_score }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="space-y-6">
                    
                    <!-- Enhanced Proposed Assessments -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-bell text-gray-600 dark:text-gray-400"></i>
                                    <span class="text-gray-900 dark:text-gray-100 font-semibold">Proposed Assessments</span>
                                </div>
                                <span v-if="items.length > 0" class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-2 py-1 rounded text-xs font-medium">
                                    {{ items.length }}
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <template v-if="items.length > 0">
                                    <div v-for="item in items" :key="item.id"
                                         class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 hover:shadow-sm transition-all">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-900 dark:text-gray-100 text-base">
                                                    {{ item.guide?.skill_number }}
                                                </h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                                    {{ item.assessor?.name }}
                                                </p>
                                            </div>
                                            <button
                                                v-if="item.status === 'pending'"
                                                @click="destroy($event, item)"
                                                class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors p-1 rounded"
                                                :title="t('action.delete')">
                                                <i class="pi pi-trash text-sm"></i>
                                            </button>
                                        </div>
                                        
                                        <div v-if="item.schedules && item.schedules.length > 0" class="mb-3">
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Proposed Dates:</p>
                                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                                <span v-for="schedule in item.schedules" :key="schedule.id"
                                                      class="text-center rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-2 text-xs font-medium">
                                                    {{ new Date(schedule.proposed_date).toLocaleDateString("en-GB", { 
                                                        day: "2-digit", 
                                                        month: "2-digit", 
                                                        year: "2-digit",
                                                        timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone 
                                                    }) }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex justify-between items-center">
                                            <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded text-xs font-medium">
                                                {{ item.status }}
                                            </span>
                                            <span class="text-xs text-gray-500">{{ new Date(item.created_at).toLocaleDateString() }}</span>
                                        </div>
                                    </div>
                                </template>
                                <div v-else class="text-center py-12 text-gray-500 dark:text-gray-400">
                                    <i class="pi pi-info-circle text-4xl mb-4 text-gray-300 dark:text-gray-600"></i>
                                    <p class="font-medium mb-2">{{ t('label.no_data_available') }}</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500">New assessment proposals will appear here</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Assessments -->
                    <div v-if="recent_assessments.length > 0" class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-check-circle text-gray-600 dark:text-gray-400"></i>
                                <span class="text-gray-900 dark:text-gray-100 font-semibold">Recent Assessments</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div v-for="assessment in recent_assessments" :key="assessment.id"
                                     class="flex justify-between items-center py-3 px-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 hover:shadow-sm transition-all">
                                    <div class="flex-1">
                                        <h6 class="font-bold text-gray-900 dark:text-gray-100">{{ assessment.skill_number }}</h6>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ assessment.assessor_name }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ assessment.created_at }}</p>
                                    </div>
                                    <span v-if="assessment.result"
                                          :class="assessment.result === 'competent' ?
                                            'bg-gray-700 text-white' :
                                            'bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200'"
                                          class="px-2 py-1 rounded text-xs font-medium">
                                        {{ assessment.result === 'competent' ? 'Competent' : 'Not Competent' }}
                                    </span>
                                    <span v-else class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded text-xs font-medium">
                                        {{ assessment.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-bolt text-gray-600 dark:text-gray-400"></i>
                                <span class="text-gray-900 dark:text-gray-100 font-semibold">Quick Actions</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <Link :href="route('assessee.assessment.index')" 
                                      class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 hover:shadow-sm transition-all">
                                    <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-2">
                                        <i class="pi pi-list text-gray-600 dark:text-gray-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-gray-100">View All Assessments</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Manage your assessment history</p>
                                    </div>
                                </Link>
                                
                                <Link :href="route('assessee.module.index')" 
                                      class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 hover:shadow-sm transition-all">
                                    <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-2">
                                        <i class="pi pi-book text-gray-600 dark:text-gray-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-gray-100">Browse Modules</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Explore available learning modules</p>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <ConfirmPopup />
            <EventModal ref="eventModal" />
        </div>
    </AppLayout>
</template>

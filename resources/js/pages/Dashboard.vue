<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted, nextTick, ref } from 'vue';
import {
    Users,
    BookOpen,
    ClipboardCheck,
    TrendingUp,
    Clock,
    CheckCircle,
    AlertCircle,
    BarChart3,
    User,
    Calendar,
    Target
} from 'lucide-vue-next';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    BarController,
    Title,
    Tooltip,
    Legend,
    type ChartConfiguration
} from 'chart.js';

interface Props {
    stats: {
        users: {
            total: number;
            assessors: number;
            assessees: number;
        };
        modules: {
            total: number;
            with_tests: number;
        };
        assessments: {
            total: number;
            pending: number;
            in_progress: number;
            completed: number;
        };
        post_tests: {
            total: number;
            completed: number;
        };
    };
    charts: {
        assessment_status: Record<string, number>;
        monthly_trend: Array<{ month: string; count: number }>;
        user_registration: Array<{ date: string; count: number }>;
        top_guides: Array<{ name: string; usage_count: number }>;
    };
    recent_assessments: Array<{
        id: string;
        assessee_name: string;
        assessor_name: string;
        guide_name: string;
        status: string;
        scheduled_at: string | null;
        created_at: string;
    }>;
    module_stats: Array<{
        title: string;
        enrolled: number;
        completed: number;
        completion_rate: number;
    }>;
}

const props = defineProps<Props>();

// Register Chart.js components
ChartJS.register(CategoryScale, LinearScale, BarElement, BarController, Title, Tooltip, Legend);

// Chart refs
const trendChartRef = ref<HTMLCanvasElement>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.dashboard',
        href: route('dashboard'),
    },
];

// Chart data for assessment trend
const trendChartData = computed(() => ({
    labels: props.charts.monthly_trend.map(item => item.month),
    datasets: [
        {
            label: 'Assessment Count',
            data: props.charts.monthly_trend.map(item => item.count),
            backgroundColor: 'rgba(99, 102, 241, 0.6)',
            borderColor: 'rgb(99, 102, 241)',
            borderWidth: 1,
            borderRadius: 4,
            borderSkipped: false,
        }
    ]
}));

const trendChartOptions: ChartConfiguration<'bar'>['options'] = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        title: {
            display: false,
        }
    },
    scales: {
        x: {
            display: true,
            grid: {
                display: false,
            }
        },
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1,
            }
        }
    }
};

// Initialize chart
const initializeTrendChart = async () => {
    await nextTick();
    
    try {
        if (trendChartRef.value && props.charts.monthly_trend.length > 0) {
            const ctx = trendChartRef.value.getContext('2d');
            if (ctx) {
                new ChartJS(ctx, {
                    type: 'bar',
                    data: trendChartData.value,
                    options: trendChartOptions
                });
            }
        }
    } catch (error) {
        console.error('Error initializing trend chart:', error);
    }
};

onMounted(initializeTrendChart);

// Computed values for better organization
const assessmentCompletionRate = computed(() => {
    if (props.stats.assessments.total === 0) return 0;
    return Math.round((props.stats.assessments.completed / props.stats.assessments.total) * 100);
});

const postTestCompletionRate = computed(() => {
    if (props.stats.post_tests.total === 0) return 0;
    return Math.round((props.stats.post_tests.completed / props.stats.post_tests.total) * 100);
});

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300';
        case 'scheduled': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300';
        case 'completed': return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300';
        case 'cancelled': return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300';
        default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300';
    }
};

const getStatusText = (status: string) => {
    switch (status) {
        case 'pending': return 'Pending';
        case 'scheduled': return 'Scheduled';
        case 'completed': return 'Completed';
        case 'cancelled': return 'Cancelled';
        default: return 'Unknown';
    }
};
</script>

<template>
    <Head :title="$t('menu.dashboard')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Welcome Header -->
            <div class="flex flex-col gap-2">
                <h1 class="text-3xl font-bold tracking-tight">Admin Dashboard</h1>
                <p class="text-muted-foreground">
                    Welcome to CORSA
                </p>
            </div>

            <!-- Stats Cards Grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Users Card -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Users</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.users.total.toLocaleString() }}</div>
                        <div class="flex items-center gap-2 text-xs text-muted-foreground mt-1">
                            <span>{{ stats.users.assessors }} Assessors</span>
                            <span>•</span>
                            <span>{{ stats.users.assessees }} Assessees</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Total Modules Card -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Learning Modules</CardTitle>
                        <BookOpen class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.modules.total.toLocaleString() }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ stats.modules.with_tests }} modules with post-tests
                        </p>
                    </CardContent>
                </Card>

                <!-- Total Assessments Card -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Assessments</CardTitle>
                        <ClipboardCheck class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.assessments.total.toLocaleString() }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ assessmentCompletionRate }}% completion rate
                        </p>
                    </CardContent>
                </Card>

                <!-- Post-Tests Card -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Post-Tests</CardTitle>
                        <Target class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.post_tests.total.toLocaleString() }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ postTestCompletionRate }}% completed
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Assessment Status Overview -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending</CardTitle>
                        <Clock class="h-4 w-4 text-yellow-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-yellow-600">{{ stats.assessments.pending }}</div>
                        <p class="text-xs text-muted-foreground">
                            Awaiting scheduling
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Scheduled</CardTitle>
                        <Calendar class="h-4 w-4 text-blue-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.assessments.in_progress }}</div>
                        <p class="text-xs text-muted-foreground">
                            Scheduled assessments
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Completed</CardTitle>
                        <CheckCircle class="h-4 w-4 text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ stats.assessments.completed }}</div>
                        <p class="text-xs text-muted-foreground">
                            Successfully finished
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Success Rate</CardTitle>
                        <BarChart3 class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ assessmentCompletionRate }}%</div>
                        <p class="text-xs text-muted-foreground">
                            Overall completion
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Charts and Recent Activity -->
            <div class="grid gap-4 md:grid-cols-7">
                <!-- Monthly Assessment Trend Chart -->
                <Card class="col-span-4">
                    <CardHeader>
                        <CardTitle>Assessment Trend</CardTitle>
                        <CardDescription>
                            Assessment volume over the last 6 months
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="pl-2">
                        <div v-if="charts.monthly_trend.length > 0" class="h-[300px] flex items-center justify-center">
                            <canvas ref="trendChartRef" class="max-w-full max-h-full"></canvas>
                        </div>
                        <div v-else class="h-[300px] flex flex-col items-center justify-center text-muted-foreground">
                            <TrendingUp class="h-12 w-12 mb-4 text-muted-foreground/30" />
                            <p class="text-center font-medium">No assessment trend data available</p>
                            <p class="text-sm text-center">Charts will appear once assessments are created</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Assessments -->
                <Card class="col-span-3">
                    <CardHeader>
                        <CardTitle>Recent Assessments</CardTitle>
                        <CardDescription>
                            Latest assessment activities
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                v-for="assessment in recent_assessments"
                                :key="assessment.id"
                                class="flex items-center gap-4"
                            >
                                <Avatar class="h-9 w-9">
                                    <AvatarFallback>
                                        {{ assessment.assessee_name.charAt(0).toUpperCase() }}
                                    </AvatarFallback>
                                </Avatar>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">
                                        {{ assessment.assessee_name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground truncate">
                                        {{ assessment.guide_name }}
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <Badge
                                        variant="secondary"
                                        :class="getStatusColor(assessment.status)"
                                        class="text-xs"
                                    >
                                        {{ getStatusText(assessment.status) }}
                                    </Badge>
                                    <span class="text-xs text-muted-foreground">
                                        {{ assessment.created_at }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Top Performance Guides and Module Stats -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Top Performance Guides -->
                <Card>
                    <CardHeader>
                        <CardTitle>Top Performance Guides</CardTitle>
                        <CardDescription>
                            Most frequently used assessment guides
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                v-for="guide in charts.top_guides"
                                :key="guide.name"
                                class="flex items-center gap-4"
                            >
                                <div class="flex-1">
                                    <p class="text-sm font-medium">{{ guide.name }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="text-sm font-mono">{{ guide.usage_count }}</div>
                                    <div class="w-20 bg-muted rounded-full h-2">
                                        <div
                                            class="bg-primary h-2 rounded-full"
                                            :style="{
                                                width: `${(guide.usage_count / Math.max(...charts.top_guides.map(g => g.usage_count))) * 100}%`
                                            }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Module Completion Stats -->
                <Card>
                    <CardHeader>
                        <CardTitle>Module Performance</CardTitle>
                        <CardDescription>
                            Top performing learning modules by completion rate
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                v-for="module in module_stats"
                                :key="module.title"
                                class="flex items-center gap-4"
                            >
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ module.title }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ module.completed }}/{{ module.enrolled }} completed
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="text-sm font-mono">{{ module.completion_rate }}%</div>
                                    <div class="w-20 bg-muted rounded-full h-2">
                                        <div
                                            class="bg-green-500 h-2 rounded-full"
                                            :style="{ width: `${module.completion_rate}%` }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Actions -->
            <Card>
                <CardHeader>
                    <CardTitle>Quick Actions</CardTitle>
                    <CardDescription>
                        Common administrative tasks
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-4 flex-wrap">
                        <Button asChild>
                            <Link :href="route('admin.module.index')">
                                <BookOpen class="mr-2 h-4 w-4" />
                                Manage Modules
                            </Link>
                        </Button>
                        <Button asChild variant="outline">
                            <Link :href="route('assessor.index')">
                                <User class="mr-2 h-4 w-4" />
                                Manage Assessors
                            </Link>
                        </Button>
                        <Button asChild variant="outline">
                            <Link :href="route('assessee.index')">
                                <Users class="mr-2 h-4 w-4" />
                                Manage Assessees
                            </Link>
                        </Button>
                        <Button asChild variant="outline">
                            <Link :href="route('admin.performance-guide.index')">
                                <Target class="mr-2 h-4 w-4" />
                                Performance Guides
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import FormModal from '@/pages/assessee/Index/FormModal.vue';
import { type BreadcrumbItem, SharedData, type User } from '@/types';
import { Head, usePage, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useConfirm, ConfirmPopup, Button as PrimeButton } from 'primevue';
import { ref, computed } from 'vue';
import PhoneNumber from '@/components/PhoneNumber.vue';
import {
    BookOpen,
    ClipboardCheck,
    TrendingUp,
    Award,
    Activity,
    GraduationCap
} from 'lucide-vue-next';

interface Props {
    item: User;
    next: number | null;
    prev: number | null;
    total: number;
    index: number;
    stats: {
        assessments: {
            total: number;
            completed: number;
            scheduled: number;
            pending: number;
            completion_rate: number;
        };
        modules: {
            total: number;
            post_tests_completed: number;
            post_tests_passed: number;
            post_test_completion_rate: number;
            post_test_success_rate: number;
        };
    };
    charts: {
        performance_timeline: Array<{
            date: string;
            assessment_count: number;
            completed_count: number;
        }>;
        skills_statistics: Array<{
            skill_name: string;
            total_assessments: number;
            passed_assessments: number;
            success_rate: number;
            last_assessment: string;
        }>;
    };
    assessment_history: Array<{
        id: string;
        assessor_name: string;
        guide_name: string;
        status: string;
        result: string | null;
        comment: string | null;
        scheduled_at: string | null;
        finished_at: string | null;
        created_at: string;
        created_at_formatted: string;
    }>;
    module_progress: Array<{
        id: string;
        title: string;
        code: string;
        enrolled_at: string;
        post_test_completed: boolean;
        post_test_passed: boolean;
        post_test_score: number;
        minimum_score: number;
        questions_available: number;
    }>;
    recent_activity: Array<{
        type: string;
        title: string;
        description: string;
        date: string;
        icon: string;
    }>;
}

const props = defineProps<Props>();
const page = usePage<SharedData>();
const confirm = useConfirm();
const modal = ref();
const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.assessee',
        href: route('assessee.index'),
    },
    {
        title: props.item.name,
        href: route('assessee.show', props.item.id),
    },
];

// Computed values
const overallPerformance = computed(() => {
    const assessmentRate = props.stats.assessments.completion_rate;
    const moduleRate = props.stats.modules.post_test_completion_rate;
    return Math.round((assessmentRate + moduleRate) / 2);
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

const getResultColor = (result: string | null) => {
    switch (result) {
        case 'competent': return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300';
        case 'not_competent': return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300';
        default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300';
    }
};

const getResultText = (result: string | null) => {
    switch (result) {
        case 'competent': return 'Competent';
        case 'not_competent': return 'Not Competent';
        default: return 'Pending';
    }
};

const destroy = (event: MouseEvent, item: User) => {
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
            router.delete(route('assessee.destroy', {
                assessee: item.id, next: props.next, prev: props.prev,
            }), {
                preserveScroll: true,
                preserveState: true,
            });
        },
    });
};
</script>

<template>
    <Head :title="item.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex justify-between">
                <div class="flex gap-x-1 items-center">
                    <Link :href="route('assessee.index')">
                        <PrimeButton
                            v-tooltip.bottom="t('action.back')"
                            icon="pi pi-arrow-left" size="small" variant="text" severity="secondary"
                            rounded />
                    </Link>
                    <PrimeButton
                        v-tooltip.bottom="t('action.delete')"
                        icon="pi pi-trash" size="small" variant="text" severity="danger"
                        @click="destroy($event, item)" rounded />
                    <span class="text-gray-300">|</span>
                </div>

                <div class="flex gap-x-1 items-center">
                    <div class="text-sm text-gray-500">{{ t('label.show_of', { index, total }) }}</div>
                    <Link :href="route('assessee.show', prev ?? '')" :disabled="prev === null">
                        <PrimeButton
                            v-tooltip.bottom="t('label.older')"
                            :disabled="prev === null"
                            icon="pi pi-chevron-left" size="small" variant="text" severity="secondary"
                            rounded />
                    </Link>
                    <Link :href="route('assessee.show', next ?? '')" :disabled="next === null">
                        <PrimeButton
                            v-tooltip.bottom="t('label.newer')"
                            :disabled="next === null"
                            icon="pi pi-chevron-right" size="small" variant="text" severity="secondary"
                            rounded />
                    </Link>
                    <PrimeButton
                        v-tooltip.bottom="t('action.edit')"
                        icon="pi pi-pencil" size="small" variant="text" severity="secondary"
                        @click="() => modal?.open(item)" rounded />
                </div>
            </div>

            <!-- Header Section -->
            <div class="flex justify-between items-start">
                <div class="flex items-center gap-4">
                    <Avatar class="h-16 w-16">
                        <AvatarFallback class="text-lg">
                            {{ item.name.charAt(0).toUpperCase() }}
                        </AvatarFallback>
                    </Avatar>
                    <div>
                        <h1 class="text-3xl font-bold">{{ item.name }}</h1>
                        <div class="flex items-center gap-3 mt-2 text-sm text-muted-foreground">
                            <a :href="`mailto:${item.email}`" class="hover:underline text-blue-600">
                                {{ item.email }}
                            </a>
                            <span v-if="item.phone">•</span>
                            <PhoneNumber
                                v-if="item.phone"
                                :phone="item.phone"
                                :international_phone="item.international_phone"
                                :has_whatsapp="item.has_whatsapp"
                            />
                            <span v-if="item.nim">• NIM: {{ item.nim }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <Badge variant="secondary">Student</Badge>
                            <Badge :class="overallPerformance >= 75 ? 'bg-green-100 text-green-800' : overallPerformance >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'">
                                {{ overallPerformance }}% Performance
                            </Badge>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                </div>
            </div>

            <!-- Overview Stats -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Assessments</CardTitle>
                        <ClipboardCheck class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.assessments.total }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ stats.assessments.completion_rate }}% completion rate
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Modules Enrolled</CardTitle>
                        <BookOpen class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.modules.total }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ stats.modules.post_test_completion_rate }}% post-tests completed
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Tests Passed</CardTitle>
                        <Award class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.modules.post_tests_passed }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ stats.modules.post_test_success_rate }}% success rate
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Overall Performance</CardTitle>
                        <TrendingUp class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ overallPerformance }}%</div>
                        <p class="text-xs text-muted-foreground">
                            {{ overallPerformance >= 75 ? 'Excellent' : overallPerformance >= 50 ? 'Good' : 'Needs Improvement' }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Charts and Progress -->
            <div class="grid gap-6 md:grid-cols-7">
                <!-- Skills Performance Chart -->
                <Card class="col-span-4">
                    <CardHeader>
                        <CardTitle>Skills Assessment Performance</CardTitle>
                        <CardDescription>Performance across different skill areas</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                v-for="skill in charts.skills_statistics"
                                :key="skill.skill_name"
                                class="flex items-center gap-4"
                            >
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ skill.skill_name }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ skill.passed_assessments }}/{{ skill.total_assessments }} passed • Last: {{ skill.last_assessment }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="text-sm font-mono">{{ skill.success_rate }}%</div>
                                    <div class="w-24 bg-muted rounded-full h-2">
                                        <div
                                            class="h-2 rounded-full"
                                            :class="skill.success_rate >= 75 ? 'bg-green-500' : skill.success_rate >= 50 ? 'bg-yellow-500' : 'bg-red-500'"
                                            :style="{ width: `${skill.success_rate}%` }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="charts.skills_statistics.length === 0" class="text-center text-muted-foreground py-8">
                                No completed assessments yet
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Activity -->
                <Card class="col-span-3">
                    <CardHeader>
                        <CardTitle>Recent Activity</CardTitle>
                        <CardDescription>Latest learning activities</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                v-for="activity in recent_activity"
                                :key="`${activity.type}-${activity.date}`"
                                class="flex items-start gap-4"
                            >
                                <div class="mt-1">
                                    <ClipboardCheck v-if="activity.icon === 'clipboard-check'" class="h-4 w-4 text-blue-500" />
                                    <GraduationCap v-else-if="activity.icon === 'academic-cap'" class="h-4 w-4 text-green-500" />
                                    <Activity v-else class="h-4 w-4 text-gray-500" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium">{{ activity.title }}</p>
                                    <p class="text-xs text-muted-foreground">{{ activity.description }}</p>
                                    <p class="text-xs text-muted-foreground mt-1">{{ activity.date }}</p>
                                </div>
                            </div>
                            <div v-if="recent_activity.length === 0" class="text-center text-muted-foreground py-8">
                                No recent activity
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Assessment History and Module Progress -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Assessment History -->
                <Card>
                    <CardHeader>
                        <CardTitle>Assessment History</CardTitle>
                        <CardDescription>Recent assessment attempts and results</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                v-for="assessment in assessment_history"
                                :key="assessment.id"
                                class="flex items-center justify-between p-3 border rounded-lg"
                            >
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium">{{ assessment.guide_name }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        Assessor: {{ assessment.assessor_name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ assessment.created_at_formatted }}
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <Badge variant="secondary" :class="getStatusColor(assessment.status)">
                                        {{ getStatusText(assessment.status) }}
                                    </Badge>
                                    <Badge
                                        v-if="assessment.result"
                                        variant="secondary"
                                        :class="getResultColor(assessment.result)"
                                    >
                                        {{ getResultText(assessment.result) }}
                                    </Badge>
                                </div>
                            </div>
                            <div v-if="assessment_history.length === 0" class="text-center text-muted-foreground py-8">
                                No assessment history
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Module Progress -->
                <Card>
                    <CardHeader>
                        <CardTitle>Module Progress</CardTitle>
                        <CardDescription>Learning module completion status</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                v-for="module in module_progress"
                                :key="module.id"
                                class="flex items-center justify-between p-3 border rounded-lg"
                            >
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium">{{ module.title }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        Code: {{ module.code }} • Enrolled: {{ module.enrolled_at }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs" :class="module.post_test_completed ? 'text-green-600' : 'text-gray-500'">
                                            {{ module.post_test_completed ? '✓' : '○' }} Post-test
                                        </span>
                                        <span v-if="module.post_test_completed" class="text-xs text-muted-foreground">
                                            Score: {{ module.post_test_score }}/{{ module.minimum_score }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <Badge
                                        v-if="module.post_test_completed"
                                        :variant="module.post_test_passed ? 'default' : 'secondary'"
                                        :class="module.post_test_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                    >
                                        {{ module.post_test_passed ? 'Passed' : 'Failed' }}
                                    </Badge>
                                    <Badge v-else variant="outline">
                                        Pending
                                    </Badge>
                                </div>
                            </div>
                            <div v-if="module_progress.length === 0" class="text-center text-muted-foreground py-8">
                                No enrolled modules
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Performance Timeline Chart -->
            <Card v-if="charts.performance_timeline.length > 0">
                <CardHeader>
                    <CardTitle>Performance Timeline</CardTitle>
                    <CardDescription>Assessment activity over the last 6 months</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="h-[200px] flex items-end justify-between gap-2">
                        <div
                            v-for="item in charts.performance_timeline"
                            :key="item.date"
                            class="flex flex-col items-center gap-2 flex-1"
                        >
                            <div class="text-xs text-muted-foreground">{{ item.completed_count }}</div>
                            <div
                                class="bg-primary w-full min-h-[4px] rounded-t"
                                :style="{
                                    height: `${Math.max(4, (item.completed_count / Math.max(...charts.performance_timeline.map(i => i.completed_count))) * 160)}px`
                                }"
                            ></div>
                            <div class="text-xs text-muted-foreground text-center">
                                {{ item.date }}
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <ConfirmPopup />
        <FormModal ref="modal" />
    </AppLayout>
</template>

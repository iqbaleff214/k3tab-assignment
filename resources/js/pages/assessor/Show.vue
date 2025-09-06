<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import FormModal from '@/pages/assessor/Index/FormModal.vue';
import { type BreadcrumbItem, type Role, SharedData, type User as UserBase } from '@/types';
import { Head, usePage, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useConfirm, ConfirmPopup, Button as PrimeButton } from 'primevue';
import { ref, computed } from 'vue';
import PhoneNumber from '@/components/PhoneNumber.vue';
import {
    ClipboardCheck,
    Award,
    Timer,
    Eye
} from 'lucide-vue-next';

interface User extends UserBase {
    roles: Role[];
}

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
        results: {
            passed: number;
            failed: number;
            success_rate: number;
        };
        workload: {
            assessees_supervised: number;
            avg_assessments_per_month: number;
            avg_time_to_complete: number;
        };
    };
    charts: {
        assessment_trend: Array<{
            month: string;
            total_assessments: number;
            completed_count: number;
            passed_count: number;
            success_rate: number;
        }>;
        skills_expertise: Array<{
            skill_name: string;
            total_assessments: number;
            passed_assessments: number;
            success_rate: number;
            last_assessment: string;
        }>;
        weekly_distribution: Array<{
            day: string;
            count: number;
        }>;
    };
    recent_assessments: Array<{
        id: string;
        assessee_name: string;
        guide_name: string;
        status: string;
        result: string | null;
        scheduled_at: string | null;
        finished_at: string | null;
        created_at: string;
        created_at_formatted: string;
    }>;
    recent_activity: Array<{
        type: string;
        title: string;
        description: string;
        date: string;
        icon: string;
        status: string;
    }>;
}

const props = defineProps<Props>();
const page = usePage<SharedData>();
const confirm = useConfirm();
const modal = ref();
const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.assessor',
        href: route('assessor.index'),
    },
    {
        title: props.item.name,
        href: route('assessor.show', props.item.id),
    },
];

// Computed values
const overallEfficiency = computed(() => {
    const completionRate = props.stats.assessments.completion_rate;
    const successRate = props.stats.results.success_rate;
    return Math.round((completionRate + successRate) / 2);
});

const workloadLevel = computed(() => {
    const monthlyAssessments = props.stats.workload.avg_assessments_per_month;
    if (monthlyAssessments >= 20) return { text: 'High', color: 'bg-red-100 text-red-800' };
    if (monthlyAssessments >= 10) return { text: 'Medium', color: 'bg-yellow-100 text-yellow-800' };
    return { text: 'Light', color: 'bg-green-100 text-green-800' };
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
            router.delete(route('assessor.destroy', {
                assessor: item.id, next: props.next, prev: props.prev,
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
                    <Link :href="route('assessor.index')">
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
                    <Link :href="route('assessor.show', prev ?? '')" :disabled="prev === null">
                        <PrimeButton
                            v-tooltip.bottom="t('label.older')"
                            :disabled="prev === null"
                            icon="pi pi-chevron-left" size="small" variant="text" severity="secondary"
                            rounded />
                    </Link>
                    <Link :href="route('assessor.show', next ?? '')" :disabled="next === null">
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
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <Badge variant="secondary">Assessor</Badge>
                            <Badge :class="overallEfficiency >= 75 ? 'bg-green-100 text-green-800' : overallEfficiency >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'">
                                {{ overallEfficiency }}% Efficiency
                            </Badge>
                            <Badge :class="workloadLevel.color">
                                {{ workloadLevel.text }} Workload
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
                        <CardTitle class="text-sm font-medium">Students Supervised</CardTitle>
                        <Eye class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.workload.assessees_supervised }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ stats.workload.avg_assessments_per_month }} assessments/month
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Success Rate</CardTitle>
                        <Award class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.results.success_rate }}%</div>
                        <p class="text-xs text-muted-foreground">
                            {{ stats.results.passed }}/{{ stats.results.passed + stats.results.failed }} passed
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Avg. Completion Time</CardTitle>
                        <Timer class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.workload.avg_time_to_complete }}h</div>
                        <p class="text-xs text-muted-foreground">
                            Time from schedule to finish
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Charts and Analytics -->
            <div class="grid gap-6 md:grid-cols-7">
                <!-- Assessment Trend Chart -->
                <Card class="col-span-4">
                    <CardHeader>
                        <CardTitle>Assessment Performance Trend</CardTitle>
                        <CardDescription>
                            Assessment volume and success rate over the last 6 months
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="pl-2">
                        <div class="h-[200px] flex items-end justify-between gap-2">
                            <div
                                v-for="item in charts.assessment_trend"
                                :key="item.month"
                                class="flex flex-col items-center gap-2 flex-1"
                            >
                                <div class="text-xs text-muted-foreground">{{ item.completed_count }}</div>
                                <div
                                    class="bg-primary w-full min-h-[4px] rounded-t"
                                    :style="{
                                        height: `${Math.max(4, (item.completed_count / Math.max(...charts.assessment_trend.map(i => i.completed_count))) * 160)}px`
                                    }"
                                ></div>
                                <div class="text-xs text-muted-foreground text-center">
                                    {{ item.month }}
                                </div>
                                <div class="text-xs font-medium text-center"
                                     :class="item.success_rate >= 75 ? 'text-green-600' : item.success_rate >= 50 ? 'text-yellow-600' : 'text-red-600'">
                                    {{ item.success_rate }}%
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Weekly Distribution -->
                <Card class="col-span-3">
                    <CardHeader>
                        <CardTitle>Weekly Activity</CardTitle>
                        <CardDescription>
                            Assessment distribution by day of week
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div
                                v-for="day in charts.weekly_distribution"
                                :key="day.day"
                                class="flex items-center gap-4"
                            >
                                <div class="w-8 text-sm font-medium">{{ day.day }}</div>
                                <div class="flex-1">
                                    <div class="w-full bg-muted rounded-full h-2">
                                        <div
                                            class="bg-primary h-2 rounded-full"
                                            :style="{
                                                width: `${Math.max(4, (day.count / Math.max(...charts.weekly_distribution.map(d => d.count))) * 100)}%`
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div class="text-sm font-mono">{{ day.count }}</div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Skills Expertise and Recent Activity -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Skills Expertise -->
                <Card>
                    <CardHeader>
                        <CardTitle>Skills Expertise</CardTitle>
                        <CardDescription>Performance across different skill areas</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                v-for="skill in charts.skills_expertise"
                                :key="skill.skill_name"
                                class="flex items-center gap-4"
                            >
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ skill.skill_name }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ skill.total_assessments }} assessments • Last: {{ skill.last_assessment }}
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
                            <div v-if="charts.skills_expertise.length === 0" class="text-center text-muted-foreground py-8">
                                No assessments completed yet
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Activity -->
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Activity</CardTitle>
                        <CardDescription>Latest assessment activities</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                v-for="activity in recent_activity"
                                :key="`${activity.title}-${activity.date}`"
                                class="flex items-start gap-4"
                            >
                                <div class="mt-1">
                                    <ClipboardCheck class="h-4 w-4 text-blue-500" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium">{{ activity.title }}</p>
                                    <p class="text-xs text-muted-foreground">{{ activity.description }}</p>
                                    <p class="text-xs text-muted-foreground mt-1">{{ activity.date }}</p>
                                </div>
                                <Badge
                                    variant="secondary"
                                    :class="getStatusColor(activity.status)"
                                    class="text-xs"
                                >
                                    {{ getStatusText(activity.status) }}
                                </Badge>
                            </div>
                            <div v-if="recent_activity.length === 0" class="text-center text-muted-foreground py-8">
                                No recent activity
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Assessments -->
            <Card>
                <CardHeader>
                    <CardTitle>Recent Assessments</CardTitle>
                    <CardDescription>Latest assessment supervision and results</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div
                            v-for="assessment in recent_assessments"
                            :key="assessment.id"
                            class="flex items-center justify-between p-4 border rounded-lg hover:bg-muted/50 transition-colors"
                        >
                            <div class="flex items-center gap-4">
                                <Avatar class="h-10 w-10">
                                    <AvatarFallback class="text-sm">
                                        {{ assessment.assessee_name.charAt(0).toUpperCase() }}
                                    </AvatarFallback>
                                </Avatar>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium">{{ assessment.assessee_name }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        Skill: {{ assessment.guide_name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ assessment.created_at_formatted }}
                                        <span v-if="assessment.scheduled_at"> • Scheduled: {{ assessment.scheduled_at }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <Badge
                                    variant="secondary"
                                    :class="getStatusColor(assessment.status)"
                                    class="text-xs"
                                >
                                    {{ getStatusText(assessment.status) }}
                                </Badge>
                                <Badge
                                    v-if="assessment.result"
                                    variant="secondary"
                                    :class="getResultColor(assessment.result)"
                                    class="text-xs"
                                >
                                    {{ getResultText(assessment.result) }}
                                </Badge>
                            </div>
                        </div>
                        <div v-if="recent_assessments.length === 0" class="text-center text-muted-foreground py-8">
                            No assessments found
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <ConfirmPopup />
        <FormModal ref="modal" />
    </AppLayout>
</template>

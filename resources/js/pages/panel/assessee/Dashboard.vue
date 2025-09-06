<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type {
    Assessment,
    BreadcrumbItem, CalendarEvent,
    FilterColumn,
    Paginate,
} from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';
import { Button, ConfirmPopup, useConfirm } from 'primevue';
import Pagination from '@/components/Pagination.vue';
import EventModal from '@/pages/panel/assessee/assessment/Index/EventModal.vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';
import FullCalendar from '@fullcalendar/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.dashboard',
        href: route('assessee.dashboard'),
    },
];

const statuses = ['pending', 'scheduled', 'completed', 'cancelled'];

const props = defineProps<{
    items: Assessment[];
    events: CalendarEvent[];
}>();

const { t, locale } = useI18n();

const modal = ref();
const eventModal = ref();
const confirm = useConfirm();

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

const searchByCode = (value: string | undefined): void => {
    const url = new URL(window.location.href);
    url.search = '';
    filterForm.get(url.toString(), {
        preserveScroll: true,
        preserveState: true,
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
    <Head :title="t('menu.assessment')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">


            <div class="grid gap-6 md:grid-cols-3">
                <div class="md:col-span-2">
                    <FullCalendar :options="calendarOptions" />
                </div>
                <div>
                    <h3 class="font-medium text-amber-600 mb-2">
                        {{ t('label.pending') }}
                    </h3>
                    <div class="flex flex-col gap-2">
                        <template v-if="items.length > 0">
                            <div v-for="item in items" :key="item.id"
                                 class="flex flex-col gap-3 bg-slate-50 dark:bg-slate-900 rounded-xl py-4 px-4">
                                <div class="flex justify-between">
                                    <div class="flex flex-col">
                                        <h3 class="font-semibold text-amber-500">
                                            <span class="text-lg inline-block">{{ item.guide?.skill_number }}</span>
                                        </h3>
                                        <span class="text-sm text-gray-500">{{ item.assessor?.name }}</span>
                                    </div>
                                    <div>
                                        <Button
                                            v-tooltip.bottom="t('action.delete')" v-if="item.status === 'pending'"
                                            icon="pi pi-trash" size="small" variant="text" severity="danger"
                                            @click="destroy($event, item)" rounded></Button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-4 md:grid-cols-6 gap-2">
                                    <div v-for="schedule in item.schedules"
                                         class="text-center rounded-lg dark:bg-amber-900 dark:text-amber-50 bg-amber-100 text-amber-800 py-2 text-sm"
                                         :key="schedule.id">
                                        {{ new Date(schedule.proposed_date).toLocaleDateString("en-GB", { day: "2-digit", month: "2-digit", timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone }) }}
                                    </div>
                                </div>
                            </div>
                        </template>
                        <p class="text-sm text-gray-500" v-else>
                            {{ t('label.no_data_available') }}
                        </p>
                    </div>
                </div>
            </div>

            <ConfirmPopup />

            <EventModal ref="eventModal" />
        </div>
    </AppLayout>
</template>

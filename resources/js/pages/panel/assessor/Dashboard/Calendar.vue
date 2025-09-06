<script setup lang="ts">
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid'
import { onMounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import type { APIResponse, SharedData } from '@/types';
import EventModal from '@/pages/panel/assessor/Dashboard/EventModal.vue';

const page = usePage<SharedData>();
const modal = ref();

const options = ref({
    plugins: [ dayGridPlugin, interactionPlugin, timeGridPlugin ],
    initialView: 'dayGridMonth',
    eventClick: function(arg: any) {
        modal.value?.open(arg.event);
    },
    events: [],
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
});

const loadEvent = () => {
    fetch(route('json.assessor.assessment.event.index', {
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        assessor_id: page.props.auth.user.id,
    })).then(res => res.json()).then((res: APIResponse<Record<string, any>[]>) => {
       if (res.error || !res.data)
           return;

       options.value.events = res.data;
    });
};

onMounted(loadEvent);
</script>

<template>
    <div>
        <FullCalendar :options />
    </div>

    <EventModal
        @submit="loadEvent"
        ref="modal" />
</template>

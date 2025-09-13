<script setup lang="ts">
import { ref } from 'vue';
import type { CalendarEvent, SharedData, TaskGroup } from '@/types';
import {
    Dialog, Button, Textarea, FloatLabel, Message, InputText, RadioButton, Image
} from 'primevue';
import { useI18n } from 'vue-i18n';
import { dateHumanFormat, isHttpUrl } from '@/lib/utils';
import { useForm, usePage } from '@inertiajs/vue3';
import SignaturePad from '@/components/SignaturePad.vue';

const visible = ref<boolean>(false);
const selectedEvent = ref<CalendarEvent>();
const { t, locale } = useI18n();

const emit = defineEmits<{
    (e: "submit"): void;
}>();

const page = usePage<SharedData>();

const form = useForm<{
    [key: string]: any;
    tasks: TaskGroup[];
    result: boolean;
    comment: string;
    started_at: Date;
    assessee_name: string;
    assessor_name: string;
    supervisor_name: string;
    data_recorder_name: string;
    assessee_signature: string;
    assessor_signature: string;
    supervisor_signature: string;
    data_recorder_signature: string;
}>({
    comment: '', result: false,
    started_at: new Date(), tasks: [],
    assessee_name: '',
    assessor_name: '',
    supervisor_name: '',
    data_recorder_name: '',
    assessee_signature: '',
    assessor_signature: '',
    supervisor_signature: '',
    data_recorder_signature: '',
});

const open = (event: CalendarEvent) => {
    visible.value = true;
    selectedEvent.value = event;
    form.started_at = new Date();
    form.tasks = event.extendedProps?.detail?.tasks ?? [];
    form.comment = event?.extendedProps?.detail?.comment ?? '';
    form.assessor_name = event.extendedProps?.detail?.assessor_name ?? page.props.auth.user.name;
    form.assessee_name = event.extendedProps?.detail?.assessee_name ?? '';
    form.supervisor_name = event.extendedProps?.detail?.supervisor_name ?? '';
    form.data_recorder_name = event.extendedProps?.detail?.data_recorder_name ?? '';
    form.assessor_signature = event.extendedProps?.detail?.assessor_signature ?? '';
    form.assessee_signature = event.extendedProps?.detail?.assessee_signature ?? '';
    form.supervisor_signature = event.extendedProps?.detail?.supervisor_signature ?? '';
    form.data_recorder_signature = event.extendedProps?.detail?.data_recorder_signature ?? '';
};

const close = () => {
    visible.value = false;
    form.reset();
};

const submit = () => {
    form.post(route('assessor.assessment.store', selectedEvent.value?.extendedProps?.detail?.id), {
        preserveUrl: true,
        preserveScroll: true,
        onSuccess: () => {
            emit('submit');
            close();
        },
    });
};

defineExpose({
    open,
});
</script>

<template>
    <Dialog
        v-model:visible="visible" modal maximizable
        @after-hide="close" :header="selectedEvent?.extendedProps?.detail?.guide?.skill_number ?? t('menu.assessment')" :style="{ width: '65rem' }">
        <form @submit.prevent="submit">
            <div class="flex flex-col gap-6 pt-2 pb-8">
                <div class="flex justify-between">
                    <h1 class="text-xl font-semibold underline flex-1">{{ selectedEvent?.extendedProps?.detail?.guide?.title }}</h1>

                    <a target="_blank" :href="route('assessor.assessment.print', selectedEvent?.extendedProps?.detail?.id)">
                        <Button
                            v-tooltip.bottom="t('action.print')" type="button"
                            icon="pi pi-print" size="small" variant="text" severity="secondary"
                            rounded></Button>
                    </a>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <FloatLabel variant="on">
                        <InputText
                            :fluid="true" id="assessee_name" disabled
                            :model-value="selectedEvent?.extendedProps?.detail?.assessee_name" />
                            <label for="assessee_name" class="text-sm">{{ t('field.name') }}</label>
                        </FloatLabel>
                    </div>
                    <div class="grid">
                        <FloatLabel variant="on">
                            <InputText
                                :fluid="true" id="assessee_no_id" disabled
                                :model-value="selectedEvent?.extendedProps?.detail?.assessee_no_id" />
                            <label for="assessee_no_id" class="text-sm">{{ t('field.assessee_no_id') }}</label>
                        </FloatLabel>
                    </div>
                    <div class="grid">
                        <FloatLabel variant="on">
                            <InputText
                                :fluid="true" id="validation_date" disabled
                                :model-value="dateHumanFormat(selectedEvent?.start ?? '', 0, locale)" />
                            <label for="validation_date" class="text-sm">{{ t('field.validation_date') }}</label>
                        </FloatLabel>
                    </div>
                    <div class="grid">
                        <FloatLabel variant="on">
                            <InputText
                                :fluid="true" id="assessee_school" disabled
                                :model-value="selectedEvent?.extendedProps?.detail?.assessee_school" />
                            <label for="assessee_school" class="text-sm">{{ t('field.assessee_school') }}</label>
                        </FloatLabel>
                    </div>
                </div>

                <div class="flex flex-col">
                    <div class="font-semibold">{{ t('field.performance_task') }}</div>
                    <p class="prose break-words" v-html="selectedEvent?.extendedProps?.detail?.guide?.performance_task"></p>
                </div>

                <div class="overflow-x-auto flex flex-col gap-4">
                    <template v-for="(taskGroup, taskGroupIndex) in form.tasks" :key="taskGroupIndex">
                        <table class="min-w-full text-sm text-gray-700 whitespace-nowrap">
                            <thead>
                            <tr class="bg-amber-300 dark:bg-gray-900 text-black dark:text-gray-300">
                                <th class="text-center px-3 py-2 font-semibold border-1 border-gray-200" rowspan="2" style="width: 25rem">Tasks</th>
                                <th class="text-center px-3 py-2 font-semibold border-1 border-gray-200" colspan="3">Completed</th>
                                <th class="text-center px-3 py-2 font-semibold border-1 border-gray-200" rowspan="2">Observation/ Hints</th>
                            </tr>
                            <tr class="bg-amber-300 dark:bg-gray-900 text-black dark:text-gray-300">
                                <th class="text-center px-3 py-2 font-semibold border-1 border-gray-200" style="width: 75px">Yes</th>
                                <th class="text-center px-3 py-2 font-semibold border-1 border-gray-200" style="width: 75px">No</th>
                                <th class="text-center px-3 py-2 font-semibold border-1 border-gray-200" style="width: 75px">N/A</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="border-1 border-gray-200 font-semibold hover:bg-amber-50 text-black">
                                <td class="px-3 py-2" colspan="5">{{ taskGroup.title }}</td>
                            </tr>
                            <tr
                                v-for="(task, taskIndex) in taskGroup.child" :key="taskIndex"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-3 py-2 border border-gray-200 w-96 break-words whitespace-normal">
                                    <p class="prose m-0" v-html="task.title" style="width: 25rem"></p>
                                </td>
                                <td class="px-3 py-2 border-1 text-center border-gray-200">
                                    <RadioButton
                                        :disabled="selectedEvent?.extendedProps?.detail.status !== 'scheduled'"
                                        v-model="form.tasks[taskGroupIndex].child[taskIndex].status"
                                        :input-id="`completed_${taskGroupIndex}_${taskIndex}_status_completed`"
                                        :name="`completed_${taskGroupIndex}_${taskIndex}_status`" value="completed" />
                                </td>
                                <td class="px-3 py-2 border-1 text-center border-gray-200">
                                    <RadioButton
                                        :disabled="selectedEvent?.extendedProps?.detail.status !== 'scheduled'"
                                        v-model="form.tasks[taskGroupIndex].child[taskIndex].status"
                                        :input-id="`completed_${taskGroupIndex}_${taskIndex}_status_not_completed`"
                                        :name="`completed_${taskGroupIndex}_${taskIndex}_status`" value="not_completed" />
                                </td>
                                <td class="px-3 py-2 border-1 text-center border-gray-200">
                                    <RadioButton
                                        :disabled="selectedEvent?.extendedProps?.detail.status !== 'scheduled'"
                                        v-model="form.tasks[taskGroupIndex].child[taskIndex].status"
                                        :input-id="`completed_${taskGroupIndex}_${taskIndex}_status_not_available`"
                                        :name="`completed_${taskGroupIndex}_${taskIndex}_status`" value="not_available" />
                                </td>
                                <td class="px-3 py-2 border-1 border-gray-200">
                                    <Image
                                        :src="task.hint as string" v-if="isHttpUrl(task.hint)"
                                        class="w-16 h-16" alt="Answer" preview />
                                    <Textarea v-if="selectedEvent?.extendedProps?.detail.status === 'scheduled'"
                                        :fluid="true" id="comment" rows="1"
                                        v-model="form.tasks[taskGroupIndex].child[taskIndex].hint" />
                                    <span v-else>{{ task.hint }}</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </template>
                </div>

                <div>
                    <FloatLabel variant="on">
                        <Textarea
                            :fluid="true" id="comment" rows="5"
                            :disabled="selectedEvent?.extendedProps?.detail.status !== 'scheduled'"
                            :invalid="form.errors.comment !== undefined" v-model="form.comment" />
                        <label for="comment" class="text-sm">{{ t('field.comment') }}</label>
                    </FloatLabel>
                    <Message v-if="form.errors.comment" severity="error" size="small" variant="simple">
                        {{ form.errors.comment }}
                    </Message>
                </div>

                <div>
                    <label class="text-sm text-gray-500 font-medium mb-3 inline-block">{{ t('field.result') }}</label>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2">
                            <RadioButton
                                :disabled="selectedEvent?.extendedProps?.detail.status !== 'scheduled'"
                                v-model="form.result" input-id="result_competent" name="result" :value="true" />
                            <label for="result_competent">{{ t('label.competent') }}</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <RadioButton
                                :disabled="selectedEvent?.extendedProps?.detail.status !== 'scheduled'"
                                v-model="form.result" input-id="result_incompetent" name="result" :value="false" />
                            <label for="result_incompetent">{{ t('label.not_competent') }}</label>
                        </div>
                    </div>
                </div>

                <div class="grid lg:grid-cols-2 gap-4">
                    <SignaturePad
                        v-model:name="form.assessee_name"
                        v-model:sign="form.assessee_signature"
                        :label="t('label.assessee')"
                        :disabled="selectedEvent?.extendedProps?.detail.status !== 'scheduled'" />
                    <SignaturePad
                        v-model:name="form.assessor_name"
                        v-model:sign="form.assessor_signature"
                        :label="t('label.assessor')"
                        :disabled="selectedEvent?.extendedProps?.detail.status !== 'scheduled'" />
                    <SignaturePad
                        v-model:name="form.supervisor_name"
                        v-model:sign="form.supervisor_signature"
                        :label="t('label.supervisor')"
                        :disabled="selectedEvent?.extendedProps?.detail.status !== 'scheduled'" />
                    <SignaturePad
                        v-model:name="form.data_recorder_name"
                        v-model:sign="form.data_recorder_signature"
                        :label="t('label.data_recorder')"
                        :disabled="selectedEvent?.extendedProps?.detail.status !== 'scheduled'" />
                </div>

            </div>

            <div class="flex justify-end gap-2" v-if="selectedEvent?.extendedProps?.detail.status === 'scheduled'">
                <Button type="button" size="small" :label="t('action.cancel')" severity="secondary" @click="close" />
                <Button type="submit" size="small" :label="t('action.submit')" :loading="form.processing" :disabled="form.processing" />
            </div>
        </form>
    </Dialog>
</template>

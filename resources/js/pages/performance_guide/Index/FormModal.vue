<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Module, PerformanceGuide, TaskGroup } from '@/types';
import {
    Dialog, Button, FloatLabel, InputText, Message, Textarea, Select, FileUpload, Image
} from 'primevue';
import Editor from 'primevue/editor';
import { isHttpUrl } from '@/lib/utils';

const props = defineProps<{
    modules: Module[];
}>();

const visible = ref<boolean>(false);

const form = useForm<{
    [key: string]: any;
    _method: 'POST' | 'PUT';
    id: string;
    title: string;
    skill_number: string;
    module_id: string | null;
    performance_task: string;
    tasks: TaskGroup[];
}>({
    _method: 'POST',
    id: '',
    title: '',
    skill_number: '',
    module_id: null,
    performance_task: '',
    tasks: [
        {
            title: "Prerequisite",
            child: [
                {
                    title: "The student must complete the knowledge assessment. Minimum passing grade 80%.",
                    hint: "",
                }
            ],
        }
    ],
});


const open = (guide: PerformanceGuide | null) => {
    visible.value = true;
    if (guide === null)
        return;

    form._method = 'PUT';
    form.id = guide.id;
    form.title = guide.title;
    form.skill_number = guide.skill_number;
    form.module_id = guide.module_id;
    form.performance_task = guide.performance_task;
    form.tasks = guide.tasks;
};

const close = () => {
    visible.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    const url = form.id === '' ?
        route('admin.performance-guide.store') :
        route('admin.performance-guide.update', form.id);
    form.post(url, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: close,
    });
};

defineExpose({
    open,
});
</script>

<template>
    <Dialog
        v-model:visible="visible" modal :maximizable="true" :resizable="true"
        @after-hide="close" :header="$t('menu.performance_guide')" :style="{ width: '75rem' }">
        <form @submit.prevent="submit">
            <div class="grid gap-6 pt-2 pb-8 lg:grid-cols-2">
                <div class="flex flex-col gap-6">
                    <div class="grid lg:grid-cols-3 gap-6">
                        <div>
                            <FloatLabel variant="on">
                                <InputText
                                    :fluid="true" :autofocus="true" id="skill_number" :invalid="form.errors.skill_number !== undefined"
                                    v-model="form.skill_number" type="text" autocomplete="off" :disabled="form._method === 'PUT'" />
                                <label for="skill_number" class="text-sm">{{ $t('field.skill_number') }} <span class="text-red-500">*</span></label>
                            </FloatLabel>
                            <Message v-if="form.errors.skill_number" severity="error" size="small" variant="simple">
                                {{ form.errors.skill_number }}
                            </Message>
                        </div>
                        <div class="lg:col-span-2">
                            <FloatLabel variant="on">
                                <InputText
                                    :fluid="true" id="title" :invalid="form.errors.title !== undefined"
                                    v-model="form.title" type="text" autocomplete="off" />
                                <label for="title" class="text-sm">{{ $t('field.title') }} <span class="text-red-500">*</span></label>
                            </FloatLabel>
                            <Message v-if="form.errors.title" severity="error" size="small" variant="simple">
                                {{ form.errors.title }}
                            </Message>
                        </div>
                    </div>
                    <div>
                        <FloatLabel variant="on">
                            <Select
                                fluid id="module_id" v-model="form.module_id" :options="props.modules"
                                option-value="id" option-label="code" show-clear />
                            <label for="module_id" class="text-sm">{{ $t('menu.module_skill') }}</label>
                        </FloatLabel>
                        <Message v-if="form.errors.module_id" severity="error" size="small" variant="simple">
                            {{ form.errors.module_id }}
                        </Message>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 font-medium ms-3.5">{{ $t('field.performance_task') }}</label>
                        <Editor v-model="form.performance_task" editor-style="height: 135px">
                            <template v-slot:toolbar>
                                <span class="ql-formats">
                                  <button class="ql-bold"></button>
                                  <button class="ql-italic"></button>
                                  <button class="ql-underline"></button>
                                  <button class="ql-strike"></button>
                                </span>
                                <span class="ql-formats">
                                  <select class="ql-font"></select>
                                  <select class="ql-size"></select>
                                </span>
                                <span class="ql-formats">
                                  <select class="ql-color"></select>
                                  <select class="ql-background"></select>
                                </span>
                                <span class="ql-formats">
                                  <button class="ql-list" value="ordered"></button>
                                  <button class="ql-list" value="bullet"></button>
                                  <button class="ql-indent" value="-1"></button>
                                  <button class="ql-indent" value="+1"></button>
                                </span>
                                <span class="ql-formats">
                                  <select class="ql-align"></select>
                                </span>
                                <span class="ql-formats">
                                  <button class="ql-blockquote"></button>
                                  <button class="ql-code-block"></button>
                                </span>
                                <span class="ql-formats">
                                  <button class="ql-link"></button>
                                </span>
                                <span class="ql-formats">
                                  <button class="ql-clean"></button>
                                </span>
                            </template>
                        </Editor>
                        <Message v-if="form.errors.performance_task" severity="error" size="small" variant="simple">
                            {{ form.errors.performance_task }}
                        </Message>
                    </div>
                </div>
                <div class="overflow-x-auto flex flex-col gap-6">
                    <table class="min-w-full text-sm text-gray-700 whitespace-nowrap">
                        <thead>
                        <tr class="bg-gray-100 text-xs uppercase text-gray-500 border-b border-gray-200">
                            <th class="px-3 py-2 text-center">{{ $t('field.tasks') }}</th>
                            <th class="px-3 py-2 text-center">{{ $t('field.hint') }}</th>
                            <th class="py-2 text-center w-[50px]">
                                <Button
                                    icon="pi pi-plus" size="small" variant="text" severity="secondary"
                                    @click="(e) => form.tasks.push({ title: '', child: [ { title: '', hint: '' } ] })" rounded></Button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="(taskGroup, taskGroupIndex) in form.tasks" :key="taskGroupIndex">
                            <tr class="border-b border-gray-200 bg-gray-100">
                                <td class="px-3 py-2" colspan="2">
                                    <InputText
                                        :fluid="true" v-model="form.tasks[taskGroupIndex].title" size="small" type="text" />
                                </td>
                                <td class="py-2 text-center w-[50px]">
                                    <Button
                                        icon="pi pi-trash" size="small" variant="text" severity="danger"
                                        @click="(e) => form.tasks.splice(taskGroupIndex, 1)" rounded></Button>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-200 hover:bg-gray-50"
                                v-for="(task, taskIndex) in form.tasks[taskGroupIndex].child" :key="taskIndex">
                                <td class="px-3 py-2">
                                    <Textarea
                                        v-model="form.tasks[taskGroupIndex].child[taskIndex].title"
                                        :fluid="true" size="small" type="text" rows="3" />
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex-1 flex justify-start" v-if="form.tasks[taskGroupIndex].child[taskIndex].hint_is_image">
                                        <FileUpload
                                            mode="basic" v-if="!isHttpUrl(form.tasks[taskGroupIndex].child[taskIndex].hint)" accept="image/*" :max-file-size="20_000_000" size="small"
                                            @select="(e) => form.tasks[taskGroupIndex].child[taskIndex].hint = e.files[0]" />
                                        <Image v-else :src="form.tasks[taskGroupIndex].child[taskIndex].hint as string" class="w-16 h-16" alt="Answer" preview />
                                    </div>
                                    <Textarea v-else
                                        v-model="form.tasks[taskGroupIndex].child[taskIndex].hint"
                                        :fluid="true" size="small" type="text" rows="3" />
                                </td>
                                <td class="py-2 text-center w-[50px]">
                                    <div class="flex flex-col gap-2 justify-end items-center">
                                        <Button
                                            v-if="!isHttpUrl(form.tasks[taskGroupIndex].child[taskIndex].hint)"
                                            v-tooltip="$t(!form.tasks[taskGroupIndex].child[taskIndex].hint_is_image ? 'label.n_as_an_image' : 'label.n_as_a_text', { n: $t('field.hint') } )"
                                            :icon="!form.tasks[taskGroupIndex].child[taskIndex].hint_is_image ? 'pi pi-image' : 'pi pi-align-center'"
                                            @click="() => form.tasks[taskGroupIndex].child[taskIndex].hint_is_image = !form.tasks[taskGroupIndex].child[taskIndex].hint_is_image"
                                            size="small" variant="text" severity="secondary" rounded />
                                        <Button
                                            icon="pi pi-trash" size="small" variant="text" severity="danger"
                                            @click="(e) => form.tasks[taskGroupIndex].child.splice(taskIndex, 1)" rounded></Button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <td class="px-3 py-2 text-center" colspan="2">
                                    <div class="flex">
                                        <Button
                                            icon="pi pi-plus" size="small" variant="outlined"
                                            class="flex-1" severity="secondary"
                                            @click="(e) => form.tasks[taskGroupIndex].child.push({ title: '', hint: '' })"></Button>
                                    </div>
                                </td>
                                <td class="py-2 text-center w-[50px]">
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <Button type="button" size="small" :label="$t('action.cancel')" severity="secondary" @click="close"></Button>
                <Button type="submit" size="small" :label="$t('action.submit')" :loading="form.processing" :disabled="form.processing"></Button>
            </div>
        </form>
    </Dialog>
</template>

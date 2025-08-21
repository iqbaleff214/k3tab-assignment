<script setup lang="ts">
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Module, Question } from '@/types';
import {
    Dialog, Button, FloatLabel, InputText, Message, InputNumber,
    FileUpload, FileUploadSelectEvent, Image, Textarea,
} from 'primevue';
import Editor from 'primevue/editor';
import { formatBytes } from '@/lib/utils';

const visible = ref<boolean>(false);

const form = useForm<{
    [key: string]: any;
    _method: 'POST' | 'PUT';
    id: string;
    title: string;
    body: string;
    description: string | null;
    duration_estimation: number;
    minimum_score: number;
    questions_count: number;
    questions: Question[];
    files: File[];
    code: string;
    equipment_required: string;
    procedure: string;
    reference: string;
    performance: string;
}>({
    _method: 'POST',
    id: '',
    title: '',
    body: '',
    description: null,
    duration_estimation: 0,
    minimum_score: 80,
    questions_count: 0,
    questions: [],
    files: [],
    code: '',
    equipment_required: '',
    procedure: '',
    reference: '',
    performance: '',
});


const open = (module: Module | null) => {
    visible.value = true;
    if (module === null)
        return;

    form._method = 'PUT';
    form.id = module.id;
    form.title = module.title;
    form.body = module.body;
    form.description = module.description;
    form.duration_estimation = module.duration_estimation;
    form.minimum_score = module.minimum_score;
    form.questions_count = module.questions_count;
    form.code = module.code ?? '';
    form.equipment_required = module.equipment_required ?? '';
    form.procedure = module.procedure ?? '';
    form.reference = module.reference ?? '';
    form.performance = module.performance ?? '';
};

const close = () => {
    visible.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    const url = form.id === '' ?
        route('admin.module.store') :
        route('admin.module.update', form.id);
    form.post(url, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: close,
    });
};

const previewUrls = computed<string[]>(() => form.files.map(file => URL.createObjectURL(file)));

const upload = (e: FileUploadSelectEvent) => {
    if (!e.files)
        return;

    form.files = e.files as File[];
};

defineExpose({
    open,
});
</script>

<template>
    <Dialog
        v-model:visible="visible" modal :maximizable="true" :resizable="true"
        @after-hide="close" :header="$t('menu.module')" :style="{ width: form._method === 'POST' ? '75rem' : '35rem' }">
        <form @submit.prevent="submit">
            <div class="grid gap-6 pt-2 pb-8" :class="{ 'lg:grid-cols-2': form._method === 'POST' }">
                <div class="flex flex-col gap-6">
                    <div class="grid lg:grid-cols-3 gap-6">
                        <div>
                            <FloatLabel variant="on">
                                <InputText
                                    :fluid="true" :autofocus="true" id="code" :invalid="form.errors.code !== undefined"
                                    v-model="form.code" type="text" autocomplete="off" />
                                <label for="code" class="text-sm">{{ $t('field.code') }} <span class="text-red-500">*</span></label>
                            </FloatLabel>
                            <Message v-if="form.errors.code" severity="error" size="small" variant="simple">
                                {{ form.errors.code }}
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
                    <div class="grid lg:grid-cols-3 gap-6">
                        <div>
                            <FloatLabel variant="on">
                                <InputNumber
                                    :min="0" :step="1" :show-buttons="true" :show-step-buttons="true"
                                    :fluid="true" id="duration_estimation" :invalid="form.errors.duration_estimation !== undefined"
                                    v-model="form.duration_estimation" />
                                <label for="duration_estimation" class="text-sm">{{ $t('field.duration_estimation') }} <span class="text-red-500">*</span></label>
                            </FloatLabel>
                            <Message v-if="form.errors.duration_estimation" severity="error" size="small" variant="simple">
                                {{ form.errors.duration_estimation }}
                            </Message>
                        </div>
                        <div>
                            <FloatLabel variant="on">
                                <InputNumber
                                    :min="0" :max="100" :step="1" :show-buttons="true" :show-step-buttons="true"
                                    :fluid="true" id="minimum_score" :invalid="form.errors.minimum_score !== undefined"
                                    v-model="form.minimum_score" />
                                <label for="minimum_score" class="text-sm">{{ $t('field.minimum_score') }} <span class="text-red-500">*</span></label>
                            </FloatLabel>
                            <Message v-if="form.errors.minimum_score" severity="error" size="small" variant="simple">
                                {{ form.errors.minimum_score }}
                            </Message>
                        </div>
                        <div>
                            <FloatLabel variant="on">
                                <InputNumber
                                    :min="0" :max="100" :step="1" :show-buttons="true" :show-step-buttons="true"
                                    :fluid="true" id="questions_count" :invalid="form.errors.questions_count !== undefined"
                                    v-model="form.questions_count" />
                                <label for="questions_count" class="text-sm">{{ $t('field.questions_count') }} <span class="text-red-500">*</span></label>
                            </FloatLabel>
                            <Message v-if="form.errors.questions_count" severity="error" size="small" variant="simple">
                                {{ form.errors.questions_count }}
                            </Message>
                        </div>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 font-medium ms-3.5">{{ $t('field.equipment_required') }}</label>
                        <Editor v-model="form.equipment_required" editor-style="height: 135px">
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
                        <FloatLabel variant="on" v-if="false">
                            <Textarea
                                fluid id="equipment_required" :invalid="form.errors.equipment_required !== undefined"
                                v-model="form.equipment_required" rows="2" />
                            <label for="equipment_required" class="text-sm">{{ $t('field.equipment_required') }}</label>
                        </FloatLabel>
                        <Message v-if="form.errors.equipment_required" severity="error" size="small" variant="simple">
                            {{ form.errors.equipment_required }}
                        </Message>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 font-medium ms-3.5">{{ $t('field.procedure') }}</label>
                        <Editor v-model="form.procedure" editor-style="height: 135px">
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
                        <Message v-if="form.errors.procedure" severity="error" size="small" variant="simple">
                            {{ form.errors.procedure }}
                        </Message>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 font-medium ms-3.5">{{ $t('field.reference') }}</label>
                        <Editor v-model="form.reference" editor-style="height: 135px">
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
                        <Message v-if="form.errors.reference" severity="error" size="small" variant="simple">
                            {{ form.errors.reference }}
                        </Message>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 font-medium ms-3.5">{{ $t('field.performance') }}</label>
                        <Editor v-model="form.performance" editor-style="height: 135px">
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
                        <Message v-if="form.errors.performance" severity="error" size="small" variant="simple">
                            {{ form.errors.performance }}
                        </Message>
                    </div>
                </div>
                <div>
                    <FileUpload v-if="form._method === 'POST'" :multiple="true" accept="image/*,application/pdf" :max-file-size="10_000_000" @select="upload">
                        <template #header="{ chooseCallback, clearCallback }">
                            <div class="flex flex-wrap justify-between items-center flex-1 gap-4">
                                <div class="flex gap-2">
                                    <Button v-tooltip.top="$t('action.add')" @click="chooseCallback()" icon="pi pi-file-plus" rounded outlined severity="secondary"></Button>
                                    <Button v-tooltip.top="$t('action.reset')" @click="() => { clearCallback(); form.files = []; }" icon="pi pi-undo" rounded outlined severity="danger" :disabled="!form.files || form.files.length === 0"></Button>
                                </div>
                                <span class="whitespace-nowrap">{{ formatBytes(form.files.reduce((sum, item) => sum + item.size, 0)) }}</span>
                            </div>
                        </template>
                        <template #content="{ files, uploadedFiles, messages }">
                            <div class="flex flex-col gap-8 pt-4">
                                <Message v-for="message of messages" :key="message" :class="{ 'mb-8': !files.length && !uploadedFiles.length}" severity="error">
                                    {{ message }}
                                </Message>
                                <div v-if="form.files.length > 0">
                                    <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-6 w-full">
                                        <div
                                            v-for="(file, index) of form.files" :key="file.name + file.type + file.size"
                                            class="p-3 rounded-xl flex flex-col border border-surface items-center gap-3 relative">
                                            <Image v-if="file.type.startsWith('image')" :src="previewUrls[index]" :alt="file.name" preview class="w-full rounded-2xl" />
                                            <iframe v-else-if="file.type.endsWith('pdf')" :src="previewUrls[index]" width="100%"></iframe>
                                            <span v-tooltip.bottom="file.name" class="font-semibold text-ellipsis max-w-full whitespace-nowrap overflow-hidden">{{ file.name }}</span>
                                            <small class="text-gray-500">{{ formatBytes(file.size) }}</small>
                                            <div class="absolute -top-2.5 -right-2.5">
                                                <Button
                                                    icon="pi pi-trash" @click="() => form.files.splice(index, 1)"
                                                    rounded size="small" severity="danger" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template #empty>
                            <div class="flex items-center justify-center flex-col" v-if="form.files.length === 0">
                                <i class="pi pi-cloud-upload !border-2 !rounded-full !p-8 !text-4xl !text-muted-color" />
                                <p class="mt-6 mb-0">{{ $t('label.drag_and_drop_file_here_or_browse') }}</p>
                            </div>
                        </template>
                    </FileUpload>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <Button type="button" size="small" :label="$t('action.cancel')" severity="secondary" @click="close"></Button>
                <Button type="submit" size="small" :label="$t('action.submit')" :loading="form.processing" :disabled="form.processing"></Button>
            </div>
        </form>
    </Dialog>
</template>

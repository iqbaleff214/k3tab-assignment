<script setup lang="ts">
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Module } from '@/types';
import {
    Dialog, Button, Message,
    FileUpload, FileUploadSelectEvent, Image,
} from 'primevue';
import { formatBytes } from '@/lib/utils';

const visible = ref<boolean>(false);

const form = useForm<{
    [key: string]: any;
    id: string;
    files: File[];
}>({
    id: '',
    files: [],
});


const open = (module: Module) => {
    visible.value = true;

    form.id = module.id;
};

const close = () => {
    visible.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    form.post(route('admin.module.upload-file', form.id), {
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
        @after-hide="close" :header="$t('action.upload')" :style="{ width: '35rem' }">
        <form @submit.prevent="submit">
            <div class="grid gap-6 pt-2 pb-8">
                <FileUpload
                    :multiple="true" accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                    :max-file-size="20_000_000" @select="upload">
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
                                        <div v-else class="h-36 flex items-center justify-center">
                                            <i class="pi pi-file-word text-gray-300" v-if="file.name.endsWith('doc') || file.name.endsWith('docx')" style="font-size: 2rem"></i>
                                        </div>
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

            <div class="flex justify-end gap-2">
                <Button type="button" size="small" :label="$t('action.cancel')" severity="secondary" @click="close"></Button>
                <Button type="submit" size="small" :label="$t('action.upload')" :loading="form.processing" :disabled="form.processing || form.files.length < 1"></Button>
            </div>
        </form>
    </Dialog>
</template>

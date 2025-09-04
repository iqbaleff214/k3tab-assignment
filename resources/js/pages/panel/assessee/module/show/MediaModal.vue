<script setup lang="ts">
import { ref } from 'vue';
import type { Media, Module } from '@/types';
import {
    Dialog, Button, Image
} from 'primevue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';

const visible = ref<boolean>(false);
const selectedMedia = ref<Media>();
const selectedModule = ref<Module>();
const { t } = useI18n();

defineProps<{
    isDoingTest?: boolean
}>();

const open = (media: Media, module?: Module) => {
    visible.value = true;
    selectedMedia.value = media;
    selectedModule.value = module;
};

const close = () => {
    visible.value = false;
};

const downloadFile = (id: number, filename: string) => {
    axios.post(route('assessee.module.download', { module: selectedModule.value?.id, media: id }), {
        filename,
    }).then((response) => {
        const a = document.createElement('a');
        a.href = window.URL.createObjectURL(new Blob([response.data]));
        a.download = filename;
        a.click();
        a.remove();
    });
};

defineExpose({
    open,
});
</script>

<template>
    <Dialog
        v-model:visible="visible" modal
        @after-hide="close" :header="t('field.media')" :style="{ width: '30rem' }">
        <div class="flex flex-col gap-6 pt-2 pb-8">
            <div v-if="selectedMedia?.mime_type?.includes('image')">
                <Image
                    :src="selectedMedia?.path_url" preview
                    :alt="selectedMedia?.filename" class="h-60 rounded-full mt-2" />
            </div>
            <div v-else-if="selectedMedia?.mime_type?.includes('pdf')">
                <embed
                    :src="selectedMedia?.path_url" class="w-full"
                    height='500px'/>
            </div>
            <div v-else-if="selectedMedia?.mime_type?.includes('word') || selectedMedia?.mime_type?.includes('vnd')">
                <iframe
                    :title="selectedMedia?.filename" class="w-full"
                    :src="`https://view.officeapps.live.com/op/embed.aspx?src=${selectedMedia?.path_url}`"
                    height='500px'>
                </iframe>
            </div>
        </div>

        <div class="flex justify-end gap-2" v-if="selectedMedia">
            <Button type="button" size="small" :label="t('action.cancel')" severity="secondary" @click="close"></Button>
            <Button
                type="button" size="small" :label="t('action.download')"
                icon="pi pi-download" @click="() => downloadFile(selectedMedia.id, selectedMedia?.filename)"
                :disabled="isDoingTest"></Button>
        </div>
    </Dialog>
</template>

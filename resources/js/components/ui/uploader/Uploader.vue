<script lang="ts" setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

defineProps<{
    modelValue: File | null;
    accept?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', file: File | null): void;
}>();

const { t } = useI18n();
const fileInput = ref<HTMLInputElement | null>(null);
const dragOver = ref(false);

function openFileDialog() {
    fileInput.value?.click();
}

function handleFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const files = target.files;
    if (files && files.length > 0) {
        emit('update:modelValue', files[0]);
    }
}

function handleDrop(event: DragEvent) {
    dragOver.value = false;
    const files = event.dataTransfer?.files;
    if (files && files.length > 0) {
        emit('update:modelValue', files[0]);
    }
}
</script>

<template>
    <div
        class="relative flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 p-6 text-center transition hover:border-emerald-400 hover:bg-emerald-50"
        @click="openFileDialog"
        @dragover.prevent="dragOver = true"
        @dragleave.prevent="dragOver = false"
        @drop.prevent="handleDrop"
        :class="{ 'border-emerald-400 bg-emerald-50': dragOver || modelValue }">
        <div class="absolute -top-1 -right-1" v-if="modelValue">
            <i class="ki-filled ki-check-circle text-emerald-500"></i>
        </div>

        <svg class="mb-2 h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M7 16V4m0 0L3 8m4-4l4 4M17 8h2a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V10a2 2 0 012-2h2"
            />
        </svg>
        <slot>
            <p class="text-gray-600">{{ t('label.drag_and_drop_file_here_or_browse') }}</p>
        </slot>

        <input
            ref="fileInput" type="file" :accept
            class="hidden" @change="handleFileChange" />

        <div v-if="modelValue" class="mt-4 text-sm text-gray-700 w-full truncate">
            <strong>{{ modelValue.name }}</strong>
        </div>
    </div>
</template>
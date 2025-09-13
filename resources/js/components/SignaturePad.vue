<script setup lang="ts">
import { VueSignaturePad } from "@selemondev/vue3-signature-pad"
import { computed, ref, onMounted, watch } from 'vue';
import { InputText, Button, FloatLabel } from 'primevue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    name: string;
    sign: string;
    label?: string;
    disabled?: boolean;
    hideNameField?: boolean;
}>();

const emit = defineEmits<{
    (e: "update:name", value: string): void
    (e: "update:sign", value: string): void
}>();

const { t } = useI18n();
const uniqueId = ref(Math.random().toString(36).substr(2, 9));

const nameValue = computed({
    get: () => props.name,
    set: (val: string) => emit("update:name", val),
});

const signValue = computed({
    get: () => props.sign,
    set: (val: string) => emit("update:sign", val),
});

const signature = ref();

const handleClear = () => {
    signature.value.clearCanvas();
    signValue.value = '';
};

const handleUndo = () => {
    signature.value.undo();
    updateSignature();
};

const updateSignature = () => {
    if (signature.value) {
        signValue.value = signature.value.saveSignature();
    }
};

// Watch for signature changes and update the model
watch(() => props.sign, (newVal) => {
    if (newVal && signature.value) {
        signature.value.fromDataURL(newVal);
    }
});

onMounted(() => {
    if (props.sign && signature.value) {
        signature.value.fromDataURL(props.sign);
    }
});

</script>

<template>
    <div class="flex flex-col gap-3">
        <div v-if="!hideNameField">
            <FloatLabel variant="on">
                <InputText
                    :fluid="true"
                    :id="`name-${uniqueId}`"
                    :disabled="disabled"
                    v-model="nameValue" />
                <label :for="`name-${uniqueId}`" class="text-sm">
                    {{ label || t('field.name') }}
                </label>
            </FloatLabel>
        </div>

        <div class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 relative">
            <VueSignaturePad
                ref="signature"
                height="160px"
                width="100%"
                :maxWidth="1"
                :minWidth="0.5"
                :disabled="disabled"
                :options="{
                    penColor: 'rgb(0, 0, 0)',
                    backgroundColor: 'transparent'
                }"
                @end-stroke="updateSignature" />

            <div class="absolute top-1 right-1 flex gap-1" v-if="!disabled">
                <Button
                    type="button" icon="pi pi-eraser"
                    @click="handleClear" severity="secondary" variant="text"
                    rounded size="small" />
                <Button
                    type="button" icon="pi pi-undo"
                    @click="handleUndo" severity="secondary" variant="text"
                    rounded size="small" />
            </div>
        </div>
    </div>
</template>

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
        <div>
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

        <div class="border border-gray-300 rounded-lg p-4 bg-white">
            <div class="mb-2 text-sm text-gray-600">{{ t('field.signature') }}</div>
            <div class="border border-gray-200 rounded bg-gray-50">
                <VueSignaturePad
                    ref="signature"
                    height="200px"
                    width="100%"
                    :maxWidth="2"
                    :minWidth="1"
                    :disabled="disabled"
                    :options="{
                        penColor: 'rgb(0, 0, 0)',
                        backgroundColor: 'rgb(255, 255, 255)'
                    }"
                    @end-stroke="updateSignature" />
            </div>

            <div class="flex gap-2 mt-2" v-if="!disabled">
                <Button
                    type="button"
                    @click="handleClear"
                    size="small"
                    severity="secondary"
                    :label="t('action.clear')" />
                <Button
                    type="button"
                    @click="handleUndo"
                    size="small"
                    severity="secondary"
                    :label="t('action.undo')" />
            </div>
        </div>
    </div>
</template>

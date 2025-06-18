<script setup lang="ts">
import { ref } from 'vue';
import { Button, Dialog, FloatLabel, InputText, Message } from 'primevue';
import { useForm } from '@inertiajs/vue3';

const visible = ref<boolean>(false);

const form = useForm<{
    name: string;
    events: string;
}>({
    name: '',
    events: 'Message',
});

const open = () => {
    visible.value = true;
}

const submit = () => {
    form.post(route('configuration.whatsapp.store'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: close,
    })
};

const close = () => {
    visible.value = false
}

defineExpose({
    open,
})
</script>

<template>
    <Dialog
        v-model:visible="visible" modal @after-hide="close"
        :header="$t('label.connect_device')" :style="{ width: '50rem' }">
        <div class="flex flex-col gap-6 my-4">
            <div>
                <FloatLabel variant="on">
                    <InputText :fluid="true" autofocus id="name" v-model="form.name" type="text"  :invalid="form.errors.name !== undefined" />
                    <label for="name" class="text-sm">{{ $t('field.name') }}</label>
                </FloatLabel>
                <Message v-if="form.errors.name" severity="error" size="small" variant="simple">
                    {{ form.errors.name }}
                </Message>
            </div>
        </div>

        <template #footer>
            <Button
                type="button" size="small" :label="$t('action.cancel')"
                severity="secondary" @click="close" />
            <Button
                type="button" size="small" :label="$t('action.connect')" @click="submit"
                :loading="form.processing" :disabled="form.processing"></Button>
        </template>
    </Dialog>
</template>

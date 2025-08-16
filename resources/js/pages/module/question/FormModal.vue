<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Module, Question } from '@/types';
import { Dialog, Button, FloatLabel, Message, RadioButton, Textarea } from 'primevue';

const visible = ref<boolean>(false);
const module = ref<Module>();

const form = useForm<{
    [key: string]: any;
    _method: 'POST' | 'PUT';
    id: number;
    title: string;
    question: string;
    choices: string[];
    correct_answer_index: number;
}>({
    _method: 'POST',
    id: 0,
    title: '',
    question: '',
    choices: [],
    correct_answer_index: 0,
});

const open = (m: Module, question: Question | null = null) => {
    visible.value = true;
    module.value = m;
    if (question === null)
        return;

    form._method = 'PUT';
    form.id = question.id;
    form.title = question.title;
    form.question = question.question;
    form.choices = question.choices;
    form.correct_answer_index = question.correct_answer_index;
};

const close = () => {
    visible.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    const url = form.id === 0 ?
        route('admin.module.question.store', { module: module.value?.id }) :
        route('admin.module.question.update', { module: module.value?.id, question: form.id });
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
        v-model:visible="visible" modal
        @after-hide="close" :header="$t('field.question')" :style="{ width: '30rem' }">
        <form @submit.prevent="submit">
            <div class="flex flex-col gap-6 pt-2 pb-8">
                <div>
                    <FloatLabel variant="on">
                        <Textarea
                            fluid :rows="3" :invalid="form.errors.question !== undefined"
                            v-model="form.question" />
                        <label for="question" class="text-sm">{{ $t('field.question') }} <span class="text-red-500">*</span></label>
                    </FloatLabel>
                    <Message v-if="form.errors.question" severity="error" size="small" variant="simple">
                        {{ form.errors.question }}
                    </Message>
                </div>
                <div>
                    <div class="flex justify-between items-center gap-2 mb-3">
                        <label class="text-sm">{{ $t('field.choices') }} <i v-tooltip="$t('label.mark_the_correct_answer')" class="pi pi-question-circle text-gray-500" style="font-size: 0.6rem"></i></label>
                        <Button
                            icon="pi pi-plus" size="small" variant="text" severity="secondary"
                            @click="() => form.choices.push('')" rounded></Button>
                    </div>
                    <div class="flex flex-col gap-2 items-center">
                        <div v-for="(choice, index) in form.choices" :key="index" class="flex justify-between items-baseline gap-2 w-full">
                            <RadioButton v-model="form.correct_answer_index" :input-id="`correct_${index}`" name="correct_index" :value="index" />
                            <Textarea
                                fluid :rows="1" :invalid="form.choices[index] === ''" v-model="form.choices[index]" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <Button type="button" size="small" :label="$t('action.cancel')" severity="secondary" @click="close"></Button>
                <Button type="submit" size="small" :label="$t('action.submit')" :loading="form.processing" :disabled="form.processing || form.choices.length < 1"></Button>
            </div>
        </form>
    </Dialog>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Module, Question } from '@/types';
import { Dialog, Button, FloatLabel, Message, RadioButton, Textarea, FileUpload, Image } from 'primevue';
import { isHttpUrl } from '@/lib/utils';

const visible = ref<boolean>(false);
const module = ref<Module>();

const form = useForm<{
    [key: string]: any;
    _method: 'POST' | 'PUT';
    id: number;
    title: string;
    question: string | File;
    choices: (string | File)[];
    correct_answer_index: number;
    question_type: 'text' | 'file';
    choices_type: 'text' | 'file';
}>({
    _method: 'POST',
    id: 0,
    title: '',
    question: '',
    choices: [],
    correct_answer_index: 0,
    question_type: 'text',
    choices_type: 'text',
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
    form.question_type = isHttpUrl(question.question) ? 'file' : 'text';
    form.choices_type = isHttpUrl(question.choices[0]) ? 'file' : 'text';
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
                    <div v-if="form.question_type === 'file'" class="w-full flex justify-start">
                        <FileUpload
                            mode="basic" v-if="form._method === 'POST'" accept="image/*" :max-file-size="10_000_000" size="small"
                            @select="(e) => form.question = e.files[0]" id="question" :invalid="form.errors.question !== undefined" />
                        <Image v-else :src="form.question as string" class="w-32 h-32" alt="Question" preview />
                    </div>
                    <FloatLabel variant="on" v-else>
                        <Textarea
                            fluid :rows="3" :invalid="form.errors.question !== undefined"
                            v-model="form.question as string" />
                        <label for="question" class="text-sm">{{ $t('field.question') }} <span class="text-red-500">*</span></label>
                    </FloatLabel>
                    <Message v-if="form.errors.question" severity="error" size="small" variant="simple">
                        {{ form.errors.question }}
                    </Message>
                    <small class="text-end block underline hover:text-amber-600 cursor-pointer" @click="() => form.question_type = (form.question_type === 'text' ? 'file' : 'text' )" v-if="form._method === 'POST'">
                        {{ $t(form.question_type === 'text' ? 'label.n_as_an_image' : 'label.n_as_a_text', { n: $t('field.question') } ) }}
                    </small>
                </div>
                <div>
                    <div class="flex justify-between items-center gap-2 mb-3">
                        <label class="text-sm">{{ $t('field.choices') }} <i v-tooltip="$t('label.mark_the_correct_answer')" class="pi pi-question-circle text-gray-500" style="font-size: 0.6rem"></i></label>
                        <Button
                            icon="pi pi-plus" size="small" variant="text" severity="secondary"
                            @click="() => form.choices.push('')" rounded></Button>
                    </div>
                    <div class="flex flex-col gap-2 items-center">
                        <div v-for="(choice, index) in form.choices" :key="index" class="flex justify-between items-center gap-2 w-full">
                            <RadioButton v-model="form.correct_answer_index" :input-id="`correct_${index}`" name="correct_index" :value="index" />
                            <div class="flex-1 flex justify-start" v-if="form.choices_type === 'file'">
                                <FileUpload
                                    mode="basic" v-if="form._method === 'POST'" accept="image/*" :max-file-size="10_000_000" size="small"
                                    @select="(e) => form.choices[index] = e.files[0]" :id="`choice_${index}`" :invalid="form.errors['choices.' + index] !== undefined" />
                                <Image v-else :src="form.choices[index] as string" class="w-16 h-16" alt="Answer" preview />
                            </div>
                            <Textarea
                                class="flex-1" v-else
                                fluid :rows="1" :invalid="form.choices[index] === ''" v-model="form.choices[index] as string" />
                            <Button
                                icon="pi pi-trash" size="small" variant="text" severity="danger" v-if="form.choices_type === 'text' || form._method === 'POST'"
                                @click="() => form.choices.splice(index, 1)" rounded></Button>
                        </div>
                    </div>
                    <small
                        class="text-end block underline mt-2 hover:text-amber-600 cursor-pointer"
                        @click="() => form.choices_type = (form.choices_type === 'text' ? 'file' : 'text' )"
                        v-if="form._method === 'POST' && form.choices.length > 0">
                        {{ $t(form.choices_type === 'text' ? 'label.n_as_an_image' : 'label.n_as_a_text', { n: $t('field.choices') } ) }}
                    </small>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <Button type="button" size="small" :label="$t('action.cancel')" severity="secondary" @click="close"></Button>
                <Button type="submit" size="small" :label="$t('action.submit')" :loading="form.processing" :disabled="form.processing || form.choices.length < 1"></Button>
            </div>
        </form>
    </Dialog>
</template>

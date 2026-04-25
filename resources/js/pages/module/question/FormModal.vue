<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Module, Question } from '@/types';
import { Dialog, Button, FloatLabel, Message, RadioButton, Textarea, FileUpload, Image } from 'primevue';

const visible = ref<boolean>(false);
const module = ref<Module>();
const existingQuestionImage = ref<string | null>(null);

const form = useForm<{
    [key: string]: any;
    _method: 'POST' | 'PUT';
    id: number;
    title: string;
    type: 'multiple_choice' | 'essay';
    question: string;
    question_image: File | null;
    choices: string[];
    choices_images: (File | string | null)[];
    correct_answer_index: number;
}>({
    _method: 'POST',
    id: 0,
    title: '',
    type: 'multiple_choice',
    question: '',
    question_image: null,
    choices: [],
    choices_images: [],
    correct_answer_index: 0,
});

const open = (m: Module, question: Question | null = null) => {
    visible.value = true;
    module.value = m;
    existingQuestionImage.value = null;

    if (question === null) return;

    form._method = 'PUT';
    form.id = question.id;
    form.title = question.title;
    form.type = question.type ?? 'multiple_choice';
    form.question = question.question ?? '';
    form.question_image = null;
    existingQuestionImage.value = question.question_image;
    form.choices = question.choices ?? [];
    form.choices_images = question.choices_images
        ? [...question.choices_images]
        : (question.choices?.map(() => null) ?? []);
    form.correct_answer_index = question.correct_answer_index ?? 0;
};

const close = () => {
    visible.value = false;
    form.reset();
    form.clearErrors();
    existingQuestionImage.value = null;
};

const addChoice = () => {
    form.choices.push('');
    form.choices_images.push(null);
};

const removeChoice = (index: number) => {
    form.choices.splice(index, 1);
    form.choices_images.splice(index, 1);
    if (form.correct_answer_index >= form.choices.length) {
        form.correct_answer_index = Math.max(0, form.choices.length - 1);
    }
};

const submit = () => {
    const url = form.id === 0
        ? route('admin.module.question.store', { module: module.value?.id })
        : route('admin.module.question.update', { module: module.value?.id, question: form.id });
    form.post(url, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: close,
    });
};

defineExpose({ open });
</script>

<template>
    <Dialog
        v-model:visible="visible" modal
        @after-hide="close" :header="$t('field.question')" :style="{ width: '34rem' }">
        <form @submit.prevent="submit">
            <div class="flex flex-col gap-5 pt-2 pb-8">

                <!-- Type selector -->
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <RadioButton v-model="form.type" value="multiple_choice" input-id="type_mc" name="question_type" />
                        <span class="text-sm">{{ $t('label.multiple_choice') }}</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <RadioButton v-model="form.type" value="essay" input-id="type_essay" name="question_type" />
                        <span class="text-sm">{{ $t('label.essay') }}</span>
                    </label>
                </div>

                <!-- Question text -->
                <div>
                    <FloatLabel variant="on">
                        <Textarea
                            fluid :rows="3" :invalid="form.errors.question !== undefined"
                            v-model="form.question" id="question" />
                        <label for="question" class="text-sm">
                            {{ $t('field.question') }}
                            <span class="text-gray-400 text-xs">({{ $t('field.optional') }})</span>
                        </label>
                    </FloatLabel>
                    <Message v-if="form.errors.question" severity="error" size="small" variant="simple">
                        {{ form.errors.question }}
                    </Message>
                </div>

                <!-- Question image -->
                <div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-500">{{ $t('field.question') }} {{ $t('label.image') }}</span>
                        <FileUpload
                            mode="basic" accept="image/*" :max-file-size="10_000_000" size="small"
                            @select="(e) => form.question_image = e.files[0]"
                            :invalid="form.errors.question_image !== undefined" />
                    </div>
                    <div v-if="existingQuestionImage && !form.question_image" class="mt-2">
                        <Image :src="existingQuestionImage" preview alt="Question image" class="w-24 h-24" />
                    </div>
                    <p v-if="form.question_image" class="text-xs text-gray-500 mt-1">
                        {{ (form.question_image as File).name }}
                    </p>
                    <Message v-if="form.errors.question_image" severity="error" size="small" variant="simple">
                        {{ form.errors.question_image }}
                    </Message>
                </div>

                <!-- Choices (multiple choice only) -->
                <div v-if="form.type === 'multiple_choice'">
                    <div class="flex justify-between items-center gap-2 mb-3">
                        <label class="text-sm">
                            {{ $t('field.choices') }}
                            <i v-tooltip="$t('label.mark_the_correct_answer')" class="pi pi-question-circle text-gray-500" style="font-size: 0.6rem" />
                        </label>
                        <Button icon="pi pi-plus" size="small" variant="text" severity="secondary" @click="addChoice" rounded />
                    </div>
                    <div class="flex flex-col gap-3">
                        <div
                            v-for="(choice, index) in form.choices" :key="index"
                            class="border border-gray-100 dark:border-gray-800 rounded-lg p-3 flex flex-col gap-2">
                            <div class="flex items-start gap-2">
                                <RadioButton
                                    class="mt-1"
                                    v-model="form.correct_answer_index"
                                    :input-id="`correct_${index}`"
                                    name="correct_index"
                                    :value="index" />
                                <Textarea
                                    class="flex-1" fluid :rows="1"
                                    v-model="form.choices[index]"
                                    :placeholder="`${$t('field.choices')} ${index + 1}`" />
                                <Button
                                    icon="pi pi-trash" size="small" variant="text" severity="danger"
                                    @click="removeChoice(index)" rounded />
                            </div>
                            <div class="flex items-center gap-2 pl-7">
                                <FileUpload
                                    mode="basic" accept="image/*" :max-file-size="10_000_000" size="small"
                                    @select="(e) => form.choices_images[index] = e.files[0]"
                                    :label="$t('label.image')" />
                                <template v-if="form.choices_images[index]">
                                    <span v-if="form.choices_images[index] instanceof File" class="text-xs text-gray-500">
                                        {{ (form.choices_images[index] as File).name }}
                                    </span>
                                    <Image
                                        v-else
                                        :src="form.choices_images[index] as string"
                                        preview alt="Choice image" class="w-12 h-12" />
                                </template>
                            </div>
                        </div>
                    </div>
                    <Message v-if="form.errors.choices" severity="error" size="small" variant="simple">
                        {{ form.errors.choices }}
                    </Message>
                </div>

                <!-- Essay notice -->
                <div v-else class="text-sm text-gray-500 italic bg-amber-50 dark:bg-amber-950 rounded-lg px-3 py-2">
                    {{ $t('label.essay_graded_manually') }}
                </div>

            </div>

            <div class="flex justify-end gap-2">
                <Button type="button" size="small" :label="$t('action.cancel')" severity="secondary" @click="close" />
                <Button
                    type="submit" size="small" :label="$t('action.submit')"
                    :loading="form.processing"
                    :disabled="form.processing || (form.type === 'multiple_choice' && form.choices.length < 1)" />
            </div>
        </form>
    </Dialog>
</template>

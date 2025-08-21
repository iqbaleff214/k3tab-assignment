<script setup lang="ts">
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { Dialog, Button, Image } from 'primevue';
import type { Module, Question, Answer } from '@/types';
import { isHttpUrl, shuffleArray } from '@/lib/utils';

const visible = ref<boolean>(false);
const module = ref<Module>();
const selectedQuestions = ref<Question[]>([]);
const currentQuestionIndex = ref<number>(0);

const form = useForm<{
    [key: string]: any;
    module_id: string;
    answers: Answer[];
}>({
    module_id: '',
    answers: [],
});

const open = (m: Module) => {
    module.value = m;
    form.module_id = m.id;
    if (module.value?.assessees?.[0]?.is_doing_test) {
        toggleOpen();
    } else {
        router.post(route('assessee.module.post-test.start', { module: m.id }), {}, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: toggleOpen,
        });
    }
};

const toggleOpen = () => {
    visible.value = true;
    selectedQuestions.value = shuffleArray(module.value?.questions ?? []).slice(0, module.value?.available_questions_count) as Question[];
    form.answers = selectedQuestions.value.map((q: Question): Answer => ({
        id: q.id,
        question: q.question,
        answer_index: null,
        answer: null,
        is_correct: false,
    }));
};

const close = () => {
    router.post(route('assessee.module.post-test.cancel', { module: module.value?.id }), {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            visible.value = false;
            module.value = undefined;
            selectedQuestions.value = [];
            currentQuestionIndex.value = 0;
            form.reset();
        },
    });
};

const submit = () => {
    form.post(route('assessee.module.post-test.finish', { module: module.value?.id }), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            visible.value = false;
            module.value = undefined;
            form.reset();
        },
    });
};

defineExpose({
    open,
});
</script>

<template>
    <Dialog
        v-model:visible="visible" modal maximizable
        @after-hide="close" :header="$t('menu.post_test')" :style="{ width: '30rem' }">
        <form @submit.prevent="submit">
            <div class="flex flex-col gap-6 pb-8">
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="(q, i) in selectedQuestions" :key="q.id" type="button"
                        :class="{ 'bg-gray-200 text-gray-700': currentQuestionIndex !== i, 'bg-amber-400 text-white': currentQuestionIndex === i }"
                        @click="() => currentQuestionIndex = i"
                        class="text-sm px-2 py-1 rounded-md cursor-pointer w-10 relative">
                        {{ i+1 }}
                        <div class="absolute -top-2 -right-0.5" v-if="form.answers[i].answer_index !== null">
                            <i class="pi pi-circle-fill text-emerald-400"
                               title="answered" style="font-size: 0.5rem"></i>
                        </div>
                    </button>
                </div>

                <template v-if="!isHttpUrl(selectedQuestions[currentQuestionIndex]?.question)">
                    <p v-html="selectedQuestions[currentQuestionIndex]?.question"></p>
                </template>
                <template v-else>
                    <Image
                        :src="selectedQuestions[currentQuestionIndex]?.question" preview
                        :alt="selectedQuestions[currentQuestionIndex]?.title"
                        class="max-w-24 rounded-full" />
                </template>

                <div class="grid md:grid-cols-2 gap-4">
                    <button
                        type="button" :class="{ 'bg-amber-400 text-white': form.answers[currentQuestionIndex].answer_index === index, 'hover:bg-gray-100': form.answers[currentQuestionIndex].answer_index !== index }"
                        :key="choice" class="border border-gray-200 rounded-md p-2 cursor-pointer flex items-center gap-2"
                        @click="() => form.answers[currentQuestionIndex].answer_index = index"
                        v-for="(choice, index) in selectedQuestions[currentQuestionIndex]?.choices">
                        <template v-if="!isHttpUrl(choice)">
                            <p v-html="choice"></p>
                        </template>
                        <template v-else>
                            <Image
                                :src="choice" preview
                                :alt="selectedQuestions[currentQuestionIndex]?.question"
                                class="max-w-24 rounded-full" />
                        </template>
                    </button>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <Button type="button" size="small" :label="$t('action.cancel')" severity="secondary" @click="close"></Button>
                <Button
                    type="submit" size="small" :label="$t('action.submit')"
                    :loading="form.processing" :disabled="form.processing || form.answers.some(a => a.answer_index === null)" />
            </div>
        </form>
    </Dialog>
</template>

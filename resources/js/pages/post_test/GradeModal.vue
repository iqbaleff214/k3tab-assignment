<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Dialog, Button, ToggleButton } from 'primevue';
import { useI18n } from 'vue-i18n';
import type { PostTest, Answer } from '@/types';

const { t } = useI18n();

const visible = ref<boolean>(false);
const postTest = ref<PostTest>();

const essayAnswers = computed<Answer[]>(() =>
    (postTest.value?.answers ?? []).filter(a => a.is_correct === null)
);

const form = useForm<{
    essay_grades: { id: number; is_correct: boolean }[];
}>({
    essay_grades: [],
});

const open = (item: PostTest) => {
    postTest.value = item;
    form.essay_grades = essayAnswers.value.map(a => ({
        id: a.id,
        is_correct: false,
    }));
    visible.value = true;
};

const submit = () => {
    const routeName = route().current('assessor.*')
        ? 'assessor.post-test.grade'
        : 'admin.post-test.grade';

    form.put(route(routeName, { test: postTest.value?.id }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            visible.value = false;
            postTest.value = undefined;
            form.reset();
        },
    });
};

defineExpose({ open });
</script>

<template>
    <Dialog
        v-model:visible="visible" modal maximizable
        :header="t('label.grade_essay')" :style="{ width: '36rem' }">
        <form @submit.prevent="submit">
            <div class="flex flex-col gap-6 pb-6">
                <p class="text-sm text-muted-foreground">
                    {{ t('label.grade_essay_subtitle') }}
                </p>

                <div
                    v-for="(answer, i) in essayAnswers" :key="answer.id"
                    class="flex flex-col gap-2 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <p class="text-sm font-medium" v-html="answer.question"></p>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded p-3 text-sm">
                        {{ answer.answer ?? '—' }}
                    </div>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm text-muted-foreground">{{ t('label.correct') }}?</span>
                        <ToggleButton
                            v-model="form.essay_grades[i].is_correct"
                            :on-label="t('label.yes')"
                            :off-label="t('label.no')"
                            on-icon="pi pi-check"
                            off-icon="pi pi-times"
                            size="small" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <Button
                    type="button" size="small" :label="t('action.cancel')"
                    severity="secondary" @click="visible = false" />
                <Button
                    type="submit" size="small" :label="t('action.save')"
                    :loading="form.processing" :disabled="form.processing" />
            </div>
        </form>
    </Dialog>
</template>

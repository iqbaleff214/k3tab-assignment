<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import type { PerformanceGuide, SharedData, User } from '@/types';
import {
    Dialog, Button, FloatLabel, Message, DatePicker, Select, InputText,
} from 'primevue';
import { useI18n } from 'vue-i18n';

const page = usePage<SharedData>();

const visible = ref<boolean>(false);
const props = defineProps<{
    assessors: User[];
    guides: PerformanceGuide[];
}>();

const { t } = useI18n();
interface Form {
    [key: string]: any;
    assessor_id: number;
    skill_number: string;
    available: Date[];
    assessee_no_id: string | null;
    assessee_school: string | null;
}

const form = useForm<Form>({
    assessor_id: props.assessors?.[0]?.id ?? 0,
    skill_number: props.guides?.[0]?.skill_number ?? '',
    available: [],
    assessee_no_id: page.props.auth.user.nim,
    assessee_school: null,
});

const selectedGuide = computed(() => props.guides.find(g => g.skill_number === form.skill_number));


const open = () => {
    visible.value = true;
};

const close = () => {
    visible.value = false;
    form.reset();
};

const submit = () => {
    form.available.forEach(date => console.log(date, date.toISOString()));
    form.transform((data: Form) => ({
        ...data,
        available: data.available.map((date: Date) => date.toISOString()),
    })).post(route('assessee.assessment.store'), {
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
        v-model:visible="visible"
        modal
        maximizable
        @after-hide="close"
        :header="t('action.submit_menu', { menu: t('menu.assessment') })"
        :style="{ width: '30rem' }">
        <form @submit.prevent="submit">
            <div class="flex flex-col gap-6 pt-2 pb-8">
                <div>
                    <FloatLabel variant="on">
                        <Select
                            fluid
                            id="skill_number"
                            autofocus
                            :invalid="form.errors.skill_number !== undefined"
                            filter
                            v-model="form.skill_number"
                            :options="guides"
                            option-value="skill_number"
                            :option-label="(guide: PerformanceGuide) => `${guide.skill_number} (${t(`label.${guide.module?.assessees?.[0]?.status ?? 'n_a'}`)})`"
                        />
                        <label for="skill_number" class="text-sm">{{ t('field.skill_number') }} <span class="text-red-500">*</span></label>
                    </FloatLabel>
                    <Message v-if="form.errors.skill_number" severity="error" size="small" variant="simple">
                        {{ form.errors.skill_number }}
                    </Message>
                    <small class="text-gray-500" v-if="selectedGuide?.module?.assessees?.[0]?.status === 'not_competent'">
                        {{ t('label.you_have_to_retake_post_test') }}. <Link class="text-amber-500 hover:underline" :href="route('assessee.module.show', selectedGuide?.module_id)">{{ t('action.view') }}</Link>
                    </small>
                    <small class="text-gray-500" v-else-if="selectedGuide?.module?.assessees?.[0]?.status !== 'competent'">
                        {{ t('label.you_have_not_taken_post_test') }}. <Link class="text-amber-500 hover:underline" :href="route('assessee.module.show', selectedGuide?.module_id)">{{ t('action.view') }}</Link>
                    </small>
                </div>
                <div>
                    <FloatLabel variant="on">
                        <Select
                            fluid
                            id="assessor_id"
                            :invalid="form.errors.assessor_id !== undefined"
                            filter
                            v-model="form.assessor_id"
                            :options="assessors"
                            option-value="id"
                            option-label="name"
                        />
                        <label for="assessor_id" class="text-sm">{{ t('menu.assessor') }} <span class="text-red-500">*</span></label>
                    </FloatLabel>
                    <Message v-if="form.errors.assessor_id" severity="error" size="small" variant="simple">
                        {{ form.errors.assessor_id }}
                    </Message>
                </div>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <FloatLabel variant="on">
                            <InputText
                                fluid id="assessee_no_id" :invalid="form.errors.assessee_no_id !== undefined"
                                v-model="form.assessee_no_id" type="text" disabled />
                            <label for="assessee_no_id" class="text-sm">{{ t('field.assessee_no_id') }}</label>
                        </FloatLabel>
                        <Message v-if="form.errors.assessee_no_id" severity="error" size="small" variant="simple">
                            {{ form.errors.assessee_no_id }}
                        </Message>
                        <Link class="text-xs text-amber-500 hover:underline" :href="route('profile.edit')">
                            {{ t('label.you_can_change_it_in_profile_page') }}
                        </Link>
                        <small class="text-amber-500 hover:underline">
                        </small>
                    </div>
                    <div>
                        <FloatLabel variant="on">
                            <InputText
                                fluid id="assessee_school" :invalid="form.errors.assessee_school !== undefined"
                                v-model="form.assessee_school" type="text" />
                            <label for="assessee_school" class="text-sm">{{ t('field.assessee_school') }}</label>
                        </FloatLabel>
                        <Message v-if="form.errors.assessee_school" severity="error" size="small" variant="simple">
                            {{ form.errors.assessee_school }}
                        </Message>
                    </div>
                </div>
                <div>
                    <FloatLabel variant="on">
                        <DatePicker
                            fluid
                            id="available"
                            :invalid="form.errors.available !== undefined"
                            show-button-bar
                            :min-date="new Date()"
                            v-model="form.available"
                            selection-mode="multiple"
                            :manual-input="false"
                            date-format="dd/mm"
                        />
                        <label for="available" class="text-sm">{{ t('field.available_dates') }} <span class="text-red-500">*</span></label>
                    </FloatLabel>
                    <Message v-if="form.errors.available" severity="error" size="small" variant="simple">
                        {{ form.errors.available }}
                    </Message>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <Button type="button" size="small" :label="t('action.cancel')" severity="secondary" @click="close"></Button>
                <Button
                    type="submit" size="small" :label="t('action.submit')"
                    :disabled="form.processing || selectedGuide?.module?.assessees?.[0]?.status !== 'competent'"
                    :loading="form.processing" />
            </div>
        </form>
    </Dialog>
</template>

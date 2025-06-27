<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Button, Dialog, FloatLabel, Message, Select } from 'primevue';
import { useForm } from '@inertiajs/vue3';
import type { APIResponse, ApprovalFlow, Paginate, Role } from '@/types';

const visible = ref<boolean>(false);
const props = defineProps<{
    subjects: string[];
}>();
const emit = defineEmits<{
    (e: 'close'): void
}>();
const roles = ref<Role[]>([]);

const form = useForm<{
    _method: 'POST' | 'PUT';
    id: number;
    subject: string;
    role_id: number | null;
    parent_id: number | null;
}>({
    _method: 'POST',
    id: 0,
    subject: '',
    role_id: null,
    parent_id: null,
});

const open = (item: ApprovalFlow | null, subject: string | null, parentId: number | null) => {
    visible.value = true;

    form.subject = subject ?? '';
    form.parent_id = parentId;

    if (!item)
        return;

    form._method = 'PUT';
    form.id = item.id ?? 0;
    form.subject = item.subject;
    form.role_id = item.role_id;
    form.parent_id = item.parent_id;
}

const submit = () => {
    const url = form.id === 0 ?
        route('configuration.approval-flow.store') :
        route('configuration.approval-flow.update', form.id);

    form.post(url, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: close,
    })
};

const close = () => {
    visible.value = false
    form.reset();
    form.clearErrors();
    emit('close');
};

const search = (keyword: string | undefined) => {
    fetch(route('json.role.index', { keyword })).then(res => res.json())
        .then((res: APIResponse<Paginate<Role>>) => {
            if (res.error || !res.data)
                return;

            roles.value = res.data.data;
            if (roles.value.length === 0)
                return;

            form.role_id = roles.value[0].id;
        });
};

defineExpose({
    open,
});

onMounted(search);
</script>

<template>
    <Dialog
        v-model:visible="visible" modal @after-hide="close"
        :header="$t('action.new_menu', { menu: $t('menu.approval_flow') })" :style="{ width: '30rem' }">
        <div class="flex flex-col gap-6 mt-4 pb-8">
            <div v-if="subjects.length > 1 || form.subject === ''">
                <FloatLabel variant="on">
                    <Select
                        :fluid="true" :invalid="form.errors.subject !== undefined"
                        :options="subjects" :option-label="subject => $t(`menu.${subject}`)"
                        autofocus input-id="subject" v-model="form.subject" />
                    <label for="subject" class="text-sm">{{ $t('field.subject') }} <span class="text-red-500">*</span></label>
                </FloatLabel>
                <Message v-if="form.errors.subject" severity="error" size="small" variant="simple">
                    {{ form.errors.subject }}
                </Message>
            </div>
            <div>
                <FloatLabel variant="on">
                    <Select
                        :fluid="true" :invalid="form.errors.role_id !== undefined"
                        :options="roles" option-label="name" option-value="id"
                        input-id="role_id" v-model="form.role_id" />
                    <label for="role_id" class="text-sm">{{ $t('field.role_id') }} <span class="text-red-500">*</span></label>
                </FloatLabel>
                <Message v-if="form.errors.role_id" severity="error" size="small" variant="simple">
                    {{ form.errors.role_id }}
                </Message>
            </div>
        </div>

        <template #footer>
            <Button
                type="button" size="small" :label="$t('action.cancel')"
                severity="secondary" @click="close" />
            <Button
                type="button" size="small" :label="$t('action.submit')" @click="submit"
                :loading="form.processing" :disabled="form.processing"></Button>
        </template>
    </Dialog>
</template>

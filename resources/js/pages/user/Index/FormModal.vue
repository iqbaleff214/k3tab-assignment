<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Role, User as UserBase } from '@/types';
import {
    Dialog, Button, FloatLabel, InputText, Message,
    MultiSelect,
} from 'primevue';

interface User extends UserBase {
    roles: Role[];
}

const visible = ref<boolean>(false);
defineProps<{
    roles: string[];
}>();

const form = useForm<{
    [key: string]: any;
    _method: 'POST' | 'PUT';
    id: number;
    name: string;
    email: string;
    phone: string | null;
    roles: string[];
}>({
    _method: 'POST',
    id: 0,
    name: '',
    email: '',
    phone: null,
    roles: [],
});


const open = (user: User | null) => {
    visible.value = true;
    if (user === null)
        return;

    form._method = 'PUT';
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;
    form.phone = user.phone;
    form.roles = user.roles.map(role => role.name);
};

const close = () => {
    visible.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    const url = form.id === 0 ?
        route('user.store') :
        route('user.update', form.id);
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
        @after-hide="close" :header="$t('menu.user')" :style="{ width: '30rem' }">
        <form @submit.prevent="submit">
            <div class="flex flex-col gap-6 pt-2 pb-8">
                <div>
                    <FloatLabel variant="on">
                        <InputText
                            :fluid="true" :autofocus="true" id="name" :invalid="form.errors.name !== undefined"
                            v-model="form.name" type="text" autocomplete="off" />
                        <label for="name" class="text-sm">{{ $t('field.name') }} <span class="text-red-500">*</span></label>
                    </FloatLabel>
                    <Message v-if="form.errors.name" severity="error" size="small" variant="simple">
                        {{ form.errors.name }}
                    </Message>
                </div>
                <div>
                    <FloatLabel variant="on">
                        <MultiSelect :invalid="form.errors.roles !== undefined"
                                     v-model="form.roles" :options="roles" filter input-id="roles" fluid show-clear
                                     :max-selected-labels="3" class="w-full md:w-80" />
                        <label for="roles" class="text-sm">{{ $t('field.role') }} <span class="text-red-500">*</span></label>
                    </FloatLabel>
                    <Message v-if="form.errors.roles" severity="error" size="small" variant="simple">
                        {{ form.errors.roles }}
                    </Message>
                </div>
                <div>
                    <FloatLabel variant="on">
                        <InputText
                            :fluid="true" id="email" :disabled="form.id > 0" :invalid="form.errors.email !== undefined"
                            v-model="form.email" type="text" autocomplete="off" />
                        <label for="email" class="text-sm">{{ $t('field.email') }} <span class="text-red-500">*</span></label>
                    </FloatLabel>
                    <Message v-if="form.errors.email" severity="error" size="small" variant="simple">
                        {{ form.errors.email }}
                    </Message>
                </div>
                <div>
                    <FloatLabel variant="on">
                        <InputText
                            :fluid="true" id="phone" :disabled="form.id > 0" :invalid="form.errors.phone !== undefined"
                            v-model="form.phone" type="text" autocomplete="off" />
                        <label for="phone" class="text-sm">{{ $t('field.phone') }}</label>
                    </FloatLabel>
                    <Message v-if="form.errors.phone" severity="error" size="small" variant="simple">
                        {{ form.errors.phone }}
                    </Message>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <Button type="button" size="small" :label="$t('action.cancel')" severity="secondary" @click="close"></Button>
                <Button type="submit" size="small" :label="$t('action.submit')" :loading="form.processing" :disabled="form.processing"></Button>
            </div>
        </form>
    </Dialog>
</template>

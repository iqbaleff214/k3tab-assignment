<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import {
    FloatLabel, InputText, Password, Message, Button,
} from 'primevue';

interface Props {
    token: string;
    email: string;
}

const props = defineProps<Props>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <AuthLayout :title="$t('menu.reset_password')" :description="$t('label.enter_new_password')">
        <Head :title="$t('menu.reset_password')" />

        <form @submit.prevent="submit">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <FloatLabel variant="in">
                        <InputText :fluid="true" id="email" :model-value="form.email" disabled autocomplete="email" />
                        <label for="email">{{ $t('field.email_or_phone') }}</label>
                    </FloatLabel>
                    <Message v-if="form.errors.email" severity="error" size="small" variant="simple">{{ form.errors.email }}</Message>
                </div>

                <div class="grid gap-2">
                    <FloatLabel variant="in">
                        <Password v-model="form.password" inputId="password" :fluid="true" autofocus toggleMask />
                        <label for="password">{{ $t('field.password') }}</label>
                    </FloatLabel>
                    <Message v-if="form.errors.password" severity="error" size="small" variant="simple">{{ form.errors.password }}</Message>
                </div>

                <div class="grid gap-2">
                    <FloatLabel variant="in">
                        <Password
                            :invalid="form.password !== form.password_confirmation"
                            v-model="form.password_confirmation"
                            inputId="password_confirmation" :fluid="true"
                            :feedback="false" toggleMask />
                        <label for="password_confirmation">{{ $t('field.password_confirmation') }}</label>
                    </FloatLabel>
                    <Message v-if="form.errors.password_confirmation" severity="error" size="small" variant="simple">{{ form.errors.password_confirmation }}</Message>
                </div>

                <Button type="submit" :fluid="true" :label="$t('action.reset_password')" :loading="form.processing" />
            </div>
        </form>
    </AuthLayout>
</template>

<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Checkbox } from '@/components/ui/checkbox';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import {
    FloatLabel, InputText, Password, Message, Button,
} from 'primevue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const param = new URLSearchParams(window.location.search);
const form = useForm({
    email: param.get('email') ?? '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase :title="$t('label.login_subtitle')" :description="$t('label.login_subtitle_2')">
        <Head :title="$t('menu.login')" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <FloatLabel variant="in">
                        <InputText :fluid="true" :autofocus="true" id="email" v-model="form.email" autocomplete="off" />
                        <label for="email">{{ $t('field.email_or_phone') }}</label>
                    </FloatLabel>
                    <Message v-if="form.errors.email" severity="error" size="small" variant="simple">{{ form.errors.email }}</Message>
                </div>

                <div class="grid gap-2">
                    <FloatLabel variant="in">
                        <Password v-model="form.password" inputId="password" :fluid="true" :feedback="false" toggleMask />
                        <label for="password">{{ $t('field.password') }}</label>
                    </FloatLabel>
                    <Message v-if="form.errors.password" severity="error" size="small" variant="simple">{{ form.errors.password }}</Message>
                </div>

                <div class="flex items-center justify-between" :tabindex="3">
                    <div class="flex items-center gap-2">
                        <Checkbox v-model="form.remember" inputId="remember" value="true" />
                        <label for="remember">{{ $t('label.remember_me') }}</label>
                    </div>
                    <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm" :tabindex="5">
                        {{ $t('label.forgot_password') }}
                    </TextLink>
                </div>

                <Button type="submit" :label="$t('action.log_in')" :loading="form.processing" />
            </div>

            <div class="text-center text-sm text-muted-foreground" v-if="canRegister">
                {{ $t('label.dont_have_an_account') }}
                <TextLink :href="route('register')" :tabindex="5">{{ $t('action.sign_up') }}</TextLink>
            </div>
        </form>
    </AuthBase>
</template>

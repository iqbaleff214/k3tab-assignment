<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import {
    FloatLabel, InputText, Message, Button,
} from 'primevue';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout :title="$t('menu.forgot_password')" :description="$t('label.enter_email_password_reset_link')">
        <Head :title="$t('menu.forgot_password')" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <FloatLabel variant="in">
                        <InputText :fluid="true" id="email" v-model="form.email" autocomplete="off" />
                        <label for="email">{{ $t('field.email_or_phone') }}</label>
                    </FloatLabel>
                    <Message v-if="form.errors.email" severity="error" size="small" variant="simple">{{ form.errors.email }}</Message>
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button type="submit" :fluid="true" :label="$t('action.send_reset_link')" :loading="form.processing" />
                </div>
            </form>

            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span v-text="$t('label.or_return_to')"></span>
                <TextLink :href="route('login')">{{ $t('menu.login') }}</TextLink>
            </div>
        </div>
    </AuthLayout>
</template>

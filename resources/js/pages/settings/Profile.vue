<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import {
    SelectButton, Avatar, FloatLabel, Message, InputText,
    Button,
} from 'primevue';
import { useI18n } from 'vue-i18n';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'menu.profile',
        href: '/settings/profile',
    },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const { locale } = useI18n();

const form = useForm({
    name: user.name,
    email: user.email,
    locale: user.locale,
    phone: user.phone,
});

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            locale.value = form.locale;
        }
    });
};

type localeType = 'id' | 'en' | 'ko' | 'ja' | 'ar' | 'zh-CN';

interface Locale {
    code: localeType;
    name: string;
}

const locales: Locale[] = [
    { code: 'en', name: 'English' },
    { code: 'id', name: 'Bahasa Indonesia' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="$t('menu.profile')" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    :title="$t('menu.profile')"
                    :description="$t('label.profile_subtitle')" />

                <form @submit.prevent="submit" class="space-y-6">
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
                            <InputText
                                :fluid="true" id="email" :invalid="form.errors.email !== undefined"
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
                                :fluid="true" id="phone" :invalid="form.errors.phone !== undefined"
                                v-model="form.phone" type="text" autocomplete="off" />
                            <label for="phone" class="text-sm">{{ $t('field.phone') }}</label>
                        </FloatLabel>
                        <Message v-if="form.errors.phone" severity="error" size="small" variant="simple">
                            {{ form.errors.phone }}
                        </Message>
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div>
                        <SelectButton
                            size="small" v-model="form.locale" option-value="code"
                            :options="locales">
                            <template #option="{ option }: { option: Locale }">
                                <div class="w-6 h-6 rounded-full overflow-hidden bg-gray-200">
                                    <img :src="`/assets/flags/${option.code}.gif`" alt="profile"
                                         class="w-full h-full object-cover" />
                                </div>
                                {{ option.code }}
                            </template>
                        </SelectButton>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            type="button" size="small"
                            :label="$t('action.submit')"
                            :loading="form.processing"
                            :disabled="form.processing"
                            @click="submit"></Button>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>

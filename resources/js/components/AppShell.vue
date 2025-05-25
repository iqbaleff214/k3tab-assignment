<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import { SIDEBAR_COOKIE_NAME } from '@/components/ui/sidebar/utils';

interface Props {
    variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const isOpen = localStorage.getItem(SIDEBAR_COOKIE_NAME) === 'open';
</script>

<template>
    <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col">
        <slot />
    </div>
    <SidebarProvider v-else :default-open="isOpen">
        <slot />
    </SidebarProvider>
</template>

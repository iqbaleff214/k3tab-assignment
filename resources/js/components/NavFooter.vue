<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupContent,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';
import { BellIcon, BellDotIcon } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { useNotificationStore } from '@/store/notification';

interface Props {
    items: NavItem[];
    class?: string;
}

const { unreadCount, getUnreadNotificationCount } = useNotificationStore();
defineProps<Props>();
</script>

<template>
    <SidebarGroup :class="`group-data-[collapsible=icon]:p-0 ${$props.class || ''}`">
        <SidebarGroupContent>
            <SidebarMenu>
                <SidebarMenuItem v-for="item in items" :key="item.title">
                    <SidebarMenuButton
                        class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                        as-child :is-active="item.isActive" :tooltip="$t(item.title)">
                        <a :href="item.href" target="_blank" rel="noopener noreferrer">
                            <component :is="item.icon" />
                            <span>{{ $t(item.title) }}</span>
                        </a>
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                        as-child :is-active="route().current('notification.*')" :tooltip="$t('menu.notification')">
                        <Link :href="route('notification.index')" class="w-full flex">
                            <BellDotIcon v-if="getUnreadNotificationCount() > 0" />
                            <BellIcon v-else />
                            <span class="flex items-baseline justify-between flex-1">
                                <span>{{ $t('menu.notification') }}</span>
                                <span class="px-2 py-0.5 text-xs bg-slate-200 text-slate-800 rounded-lg" v-if="getUnreadNotificationCount() > 0">
                                    {{ getUnreadNotificationCount() }}
                                </span>
                            </span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroupContent>
    </SidebarGroup>
</template>

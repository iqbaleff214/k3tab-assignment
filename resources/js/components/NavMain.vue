<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import type { NavItemGroup, SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps<{
    items: NavItemGroup[];
}>();

const page = usePage<SharedData>();
const { t } = useI18n();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <template v-for="group in items" :key="group.header">
            <SidebarGroupLabel>{{ t(group.header) }}</SidebarGroupLabel>
            <SidebarMenu>
                <template v-for="item in group.items" :key="item.title">
                    <SidebarMenuItem v-if="item.isAvailable">
                        <SidebarMenuButton
                            as-child :is-active="item.isActive || item.href === page.url"
                            :tooltip="t(item.title)">
                            <Link :href="item.href">
                                <component :is="item.icon" />
                                <span>{{ t(item.title) }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </template>
            </SidebarMenu>
        </template>
    </SidebarGroup>
</template>

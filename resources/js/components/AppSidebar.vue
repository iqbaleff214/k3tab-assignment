<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, NavItemGroup, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Folder, LayoutGrid, Users2Icon, Briefcase, Settings2Icon
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage<SharedData>();

const mainNavItems: NavItemGroup[] = [
    {
        header: 'label.platform',
        isAvailable: true,
        items: [
            {
                title: 'menu.dashboard',
                href: route('dashboard'),
                icon: LayoutGrid,
                isAvailable: true,
                isActive: route().current('dashboard')
            },
        ],
    },
    {
        header: 'menu.access',
        isAvailable: page.props.auth.allow.view_user || page.props.auth.allow.view_role,
        items: [
            {
                title: 'menu.user',
                href: route('user.index'),
                icon: Users2Icon,
                isAvailable: page.props.auth.allow.view_user,
                isActive: route().current('user.*')
            },
            {
                title: 'menu.role',
                href: route('role.index'),
                icon: Briefcase,
                isAvailable: page.props.auth.allow.view_role,
                isActive: route().current('role.*')
            },
        ],
    }
];

const footerNavItems: NavItem[] = [
    {
        title: 'menu.configuration',
        href: route('configuration.index'),
        icon: Settings2Icon,
        isAvailable: page.props.auth.allow.view_whatsapp || page.props.auth.allow.add_approval_flow,
        isActive: route().current('configuration.*'),
    },
    {
        title: '404 Not Found Indonesia',
        href: 'https://github.com/404NotFoundIndonesia',
        icon: Folder,
        isAvailable: true,
        isExternal: true,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, NavItemGroup, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Folder,
    LayoutGrid,
    UserPenIcon,
    Settings2Icon,
    UserCheck2Icon,
    BookTextIcon,
    ListCheckIcon,
    NotebookPenIcon,
    CalendarDaysIcon, BookUp2Icon
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
                isActive: route().current('*.dashboard')
            },
        ],
    },
    {
        header: 'menu.assessment',
        isAvailable: page.props.auth.user.type !== 'assessor',
        items: [
            {
                title: 'menu.module',
                href: route('admin.module.index'),
                icon: BookTextIcon,
                isAvailable: page.props.auth.user.type === 'admin',
                isActive: route().current('admin.module.*')
            },
            {
                title: 'menu.module',
                href: route('assessee.module.index'),
                icon: BookTextIcon,
                isAvailable: page.props.auth.user.type === 'assessee',
                isActive: route().current('assessee.module.*')
            },
            {
                title: 'menu.performance_guide',
                href: route('admin.performance-guide.index'),
                icon: ListCheckIcon,
                isAvailable: page.props.auth.user.type === 'admin',
                isActive: route().current('admin.performance-guide.*')
            },
            {
                title: 'menu.assessment',
                href: route('assessee.assessment.index'),
                icon: NotebookPenIcon,
                isAvailable: page.props.auth.user.type === 'assessee',
                isActive: route().current('assessee.assessment.index') ||
                    route().current('assessee.assessment.show'),
            },
            {
                title: 'menu.post_test',
                href: route('admin.post-test.index'),
                icon: BookUp2Icon,
                isAvailable: page.props.auth.user.type === 'admin',
                isActive: route().current('admin.post-test.index'),
            },
            {
                title: 'menu.assessment',
                href: route('admin.assessment.index'),
                icon: NotebookPenIcon,
                isAvailable: page.props.auth.user.type === 'admin',
                isActive: route().current('admin.assessment.index'),
            },
        ],
    },
    {
        header: 'menu.management',
        isAvailable: page.props.auth.user.type !== 'assessee',
        items: [
            {
                title: 'menu.assessor',
                href: route('assessor.index'),
                icon: UserPenIcon,
                isAvailable: page.props.auth.user.type === 'admin',
                isActive: route().current('assessor.*')
            },
            {
                title: 'menu.assessee',
                href: route('assessee.index'),
                icon: UserCheck2Icon,
                isAvailable: page.props.auth.user.type !== 'assessee',
                isActive: route().current('assessee.*')
            },
        ],
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'menu.configuration',
        href: route('configuration.index'),
        icon: Settings2Icon,
        isAvailable: false, // page.props.auth.allow.view_whatsapp || page.props.auth.allow.add_approval_flow,
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

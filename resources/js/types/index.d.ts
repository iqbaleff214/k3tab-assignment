import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';
import { ColumnFilterMatchModeOptions } from 'primevue';

export interface Auth {
    user: User;
    allow: Allow;
}

export interface Allow {
    view_user: boolean;
    add_user: boolean;
    edit_user: boolean;
    delete_user: boolean;
    view_role: boolean;
    add_role: boolean;
    edit_role: boolean;
    delete_role: boolean;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
    isAvailable: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    flash: { success: string|null; error: string|null; };
    assistantAIAvailability: boolean;
}

export interface Link {
    url: string | null;
    label: string;
    active: boolean;
}

export interface Paginate<T> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: Link[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    locale: 'id' | 'en' | 'ko' | 'ja' | 'ar' | 'zh-CN';
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface TableColumnItem {
    field: string;
    header: string;
    slot: Slots;
    isVisible: boolean;
    isSortable: boolean;
}

export interface TableItem {
    name: string;
    items: any[];
    selection: boolean;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface FilterColumn {
    label: string;
    value: any;
    matchMode:
        | 'equals'
        | 'startsWith'
        | 'contains'
        | 'notContains'
        | 'endsWith'
        | 'notEquals'
        | 'greaterThan'
        | 'greaterThanOrEqualTo'
        | 'lessThan'
        | 'lessThanOrEqualTo'
        | 'in'
        | 'between'
        | 'dateIs'
        | 'dateIsNot'
        | 'dateBefore'
        | 'dateAfter'
        | 'dateBetween';
    canChange: boolean;
    options?: string[] | { code: string; label: string }[];
}

export interface Session {
    id: string;
    ip_address: string;
    user_agent: string;
    is_current: boolean;
    last_active: Date;
}

export interface ParsedUserAgent {
    os: string;
    browser: string;
    version: string;
    deviceType: 'Desktop' | 'Mobile' | 'Tablet' | 'Unknown';
}

export interface Role {
    id: number;
    name: string;
    guard_name: 'web';
    created_at: Date;
    updated_at: Date;
}

export interface Permission {
    id: number;
    name: string;
    created_at: Date;
    updated_at: Date;
}

export interface ActivityLog {
    id: int;
    log_name: string;
    description: string;
    subject_id: string | number;
    subject_type: string;
    causer_id: number;
    causer_type: string;
    module: string;
    properties: object;
    created_at: Date;
    updated_at: Date;
}

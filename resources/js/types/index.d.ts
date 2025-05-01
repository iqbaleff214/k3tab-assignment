import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';
import { Feature as FeatureEnum } from '@/types/enum';

export interface Auth {
    user: User;
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
    email_verified_at: string | null;
    role: 'admin' | 'user';
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

// DON'T MOVE OR CHANGE THIS FEATURE TYPE MANUALLY
export interface Feature {
    [FeatureEnum.MaxDeviceLogin]: number;
}

export interface PricingPlan {
    id: string;
    title: string;
    description: string|null;
    price: number;
    features: Feature;
    created_at: Date;
    updated_at: Date;
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

export interface Subscription {
    id: string;
    user_id: number;
    pricing_plan_id: string;
    status: 'pending' | 'active' | 'expired' | 'cancelled';
    metadata: object;
    started_at: Date;
    expires_at: Date;
    canceled_at: Date | null;
    created_at: Date;
    updated_at: Date;
}

export interface Invoice {
    id: string;
    subscription_id: string;
    user_id: number;
    invoice_number: string;
    amount: number;
    status: 'unpaid' | 'paid' | 'failed' | 'refunded';
    metadata: object;
    file_url: string | null;
    paid_at: Date | null;
    created_at: Date;
    updated_at: Date;
}

import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
    allow: Allow;
}

export interface Allow {
    view_user: boolean;
    add_user: boolean;
    edit_user: boolean;
    delete_user: boolean;
    approve_user: boolean;
    audit_user: boolean;
    view_role: boolean;
    add_role: boolean;
    edit_role: boolean;
    delete_role: boolean;
    approve_role: boolean;
    audit_role: boolean;
    view_whatsapp: boolean;
    add_whatsapp: boolean;
    edit_whatsapp: boolean;
    delete_whatsapp: boolean;
    approve_whatsapp: boolean;
    audit_whatsapp: boolean;
    view_approval_flow: boolean;
    add_approval_flow: boolean;
    edit_approval_flow: boolean;
    delete_approval_flow: boolean;
    approve_approval_flow: boolean;
    audit_approval_flow: boolean;
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
    isExternal?: boolean;
    children?: NavItem[];
}

export interface NavItemGroup {
    header: string;
    items: NavItem[];
    isAvailable?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    flash: { success: string|null; error: string|null; };
    assistantAIAvailability: boolean;
    unreadNotification: number;
    env: 'production' | 'staging' | 'local' | 'demo';
}

export interface Link {
    url: string | null;
    label: string;
    active: boolean;
}

export interface APIResponse<T> {
    data: T;
    message: string;
    error: boolean;
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

export interface CursorPaginate<T> {
    data: T[];
    path: string;
    per_page: number;
    next_cursor: string | null;
    prev_cursor: string | null;
    next_page_url: string | null;
    prev_page_url: string | null;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    locale: 'id' | 'en' | 'ko' | 'ja' | 'ar' | 'zh-CN';
    email_verified_at: string | null;
    phone: string | null;
    international_phone: string | null;
    has_whatsapp: boolean;
    type: 'admin' | 'assessor' | 'assessee';
    nim: string | null;
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
    icon: string;
    properties: Record<string, any>;
    created_at: Date;
    updated_at: Date;
}

export interface Notification {
    id: string;
    type: string;
    notifiable_type: string;
    notifiable_id: number;
    data: NotificationData;
    read_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface NotificationData {
    title: string;
    message: string;
    link?: string;
}

type WhatsAppEvent =
    | "Message"
    | "ReadReceipt"
    | "Presence"
    | "HistorySync"
    | "ChatPresence";

interface WhatsAppDevice {
    connected: boolean;
    events: "All" | WhatsAppEvent | WhatsAppEvent[];
    expiration: number;
    id: string;
    jid: string;
    loggedIn: boolean;
    name: string;
    proxy_config: {
        enabled: boolean;
        proxy_url: string;
    };
    proxy_url: string;
    qrcode?: string;
    s3_config: {
        access_key: string;
        bucket: string;
        enabled: boolean;
        endpoint: string;
        media_delivery: string;
        path_style: boolean;
        public_url: string;
        region: string;
        retention_days: number;
    };
    token: string;
    webhook: string;
}

export interface ApprovalFlow {
    id: number;
    subject: string;
    parent_id: number | null;
    role_id: number;
    role?: Role;
    children?: ApprovalFlow[];
    created_at: string;
    updated_at: string;
}

export interface Media {
    id: number;
    name: string;
    filename: string;
    mime_type: string;
    is_image: boolean;
    size: number;
    path_url: string;
    created_at: string;
    updated_at: string;
}

export interface Module {
    id: string;
    title: string;
    code: string | null;
    equipment_required: string | null;
    procedure: string | null;
    reference: string | null;
    performance: string | null;
    slug: string;
    description: string;
    body: string;
    duration_estimation: number;
    minimum_score: number;
    questions_count: number;
    questions?: Question[];
    available_questions_count: number;
    media?: Media[];
    assessees?: ModuleAssessee[];
    created_at: string;
    updated_at: string;
}

export interface ModuleAssessee {
    id: number;
    module_id: string;
    user_id: number;
    score: number;
    status: 'read' | 'competent' | 'not_competent';
    downloaded_files: string[];
    is_doing_test: boolean;
    read_at: string | null;
    result?: PostTest | null;
    results?: PostTest[] | null;
    completed_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Question {
    id: number;
    module_id: string;
    title: string;
    question: string;
    choices: string[];
    correct_answer_index: number;
}

export interface PostTest {
    id: number;
    module_id: string;
    user_id: number;
    answers: Answer[];
    score: number;
    minimum_score: number;
    is_passed: boolean;
    created_at: string;
    updated_at: string;
}

export interface Answer {
    id: number;
    question: string;
    answer: string | null;
    answer_index: number | null;
    is_correct: boolean;
}

export interface PerformanceGuide {
    id: string;
    module_id: string | null;
    module?: Module;
    skill_number: string;
    title: string;
    performance_task: string;
    tasks: TaskGroup[];
    created_at: string;
    updated_at: string;
}

export interface TaskGroup {
    title: string;
    child: Task[];
}

export interface Task {
    title: string;
    status?: "completed" | "not_completed" | "not_available";
    hint: string;
}

export interface Assessment {
    id: string;
    assessor_id: number;
    assessee_id: number;
    assessee_name: string;
    assessee_no_id: string | null;
    assessee_school: string | null;
    assessee?: User;
    assessor?: User;
    performance_guide_id: string;
    guide?: PerformanceGuide;
    tasks: TaskGroup[];
    schedules?: AssessmentSchedule[];
    result: 'competent' | 'not_competent' | null;
    status: 'pending' | 'scheduled' | 'completed' | 'cancelled';
    comment: string | null;
    scheduled_at: string | null;
    started_at: string | null;
    finished_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface AssessmentSchedule {
    id: number;
    assessment_id: string;
    proposed_date: string;
    current_status: 'proposed' | 'accepted' | 'rejected';
    created_at: string;
    updated_at: string;
}

export interface CalendarEvent {
    id: string;
    title: string;
    date: string;
    start?: string;
    end?: string;
    allDay?: boolean;
    backgroundColor?: string;
    borderColor?: string;
    textColor?: string;
    extendedProps?: { detail: Assessment };
}

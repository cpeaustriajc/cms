import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

interface Link {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedData<T> {
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

export type Value = string | number | readonly string[] | undefined;

export interface Locale {
    id: number;
    code: string;
    name: string;
}

export interface StatusOption {
    id: string;
    label: string;
}

interface Status {
    id: number;
    code: string;
}

interface Type {
    id: number;
    name: string;
    slug: string;
}

interface Content {
    id: number;
    path: string | null;
    published_at: string | null;
    status: Status;
    type: Type;
    title: string | null;
}

export interface ContentRoute {
    is_primary?: boolean;
    path?: string | null;
}

export interface ContentWithValues extends Content {
    content_type_id: number;
    status_id?: string | null;
    route?: ContentRoute | null;
    values?: Record<string, Value>;
}

export interface ContentTypeWithFields extends ContentType {
    fields: Field[];
}

export type ContentFormErrors = Record<string, string> & {
    fields?: Record<number, Record<string, string>>;
};

interface Field {
    id: number;
    name: string;
    handle: string;
    data_type: string;
    is_required?: boolean;
    is_translatable?: boolean;
}

interface ContentType {
    id: number;
    name: string;
    slug: string;
    description?: string | null;
}

interface ContentTypeLite extends ContentType {
    contents_count: number;
    fields_count: number;
}

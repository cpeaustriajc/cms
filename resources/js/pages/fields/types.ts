export type ContentTypeLite = {
    id: number;
    name: string;
    slug: string;
};

export type FieldItem = {
    id: number;
    name: string;
    handle: string;
    data_type: string;
    is_required?: boolean;
    is_unique?: boolean;
    is_translatable?: boolean;
    is_repeatable?: boolean;
    content_type?: ContentTypeLite;
};

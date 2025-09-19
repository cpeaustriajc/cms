import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { create as createField, index as indexFields } from '@/routes/fields';
import { BreadcrumbItem, PaginatedData } from '@/types';
import { Link, router, usePage } from '@inertiajs/react';
import { ChangeEvent } from 'react';
import { columns } from './columns';
import { DataTable } from './data-table';

type ContentTypeLite = {
    id: number;
    name: string;
    slug: string;
};

type FieldItem = {
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

interface IndexProps {
    fields: PaginatedData<FieldItem>;
    contentTypes: ContentTypeLite[];
    filters: {
        content_type?: string | null;
        search?: string | null;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Fields', href: indexFields().url },
];

export default function Index({ fields, contentTypes, filters }: IndexProps) {
    const { url } = usePage();

    const onFilter = (name: string, value: string) => {
        const params = new URLSearchParams(window.location.search);

        if (value) params.set(name, value);
        else params.delete(name);

        router.visit(`${url}?${params.toString()}`);
    };

    const onSearch = (event: ChangeEvent<HTMLInputElement>) => {
        onFilter('search', event.target.value);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="p-6">
                <div className="mb-6 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <h1 className="text-2xl font-semibold">Fields</h1>
                    <div className="flex items-center gap-3">
                        <div className="flex items-center gap-2">
                            <Label htmlFor="content_type" className="sr-only">
                                Content Type
                            </Label>
                            <Select value={filters.content_type ?? undefined} onValueChange={(value) => onFilter('content_type', value)}>
                                <SelectTrigger className="w-[220px]">
                                    <SelectValue placeholder="All content types" />
                                </SelectTrigger>
                                <SelectContent>
                                    {contentTypes.map((ct) => (
                                        <SelectItem key={ct.id} value={ct.slug}>
                                            {ct.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>
                        <Input
                            type="search"
                            name="search"
                            placeholder="Search fields..."
                            defaultValue={filters.search ?? undefined}
                            onChange={onSearch}
                        />
                        <Button render={<Link href={createField().url}>Create</Link>} />
                    </div>
                </div>

                {fields.data.length > 0 ? (
                    <DataTable columns={columns} data={fields.data} />
                ) : (
                    <Card>
                        <CardContent className="flex items-center justify-center py-12">
                            <div className="text-center">
                                <p className="mb-4 text-muted-foreground">No fields found.</p>
                                <Button render={<Link href={createField().url}>Create your first field</Link>} />
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </AppLayout>
    );
}

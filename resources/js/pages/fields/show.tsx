import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { edit as editField, index as indexFields } from '@/routes/fields';
import { BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/react';

interface ContentTypeLite {
    id: number;
    name: string;
    slug: string;
}

interface FieldItem {
    id: number;
    name: string;
    handle: string;
    data_type: string;
    is_required?: boolean;
    is_unique?: boolean;
    is_translatable?: boolean;
    is_repeatable?: boolean;
    sort_order?: number;
    content_type?: ContentTypeLite;
}

export default function Show({ field }: { field: FieldItem }) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: dashboard().url },
        { title: 'Fields', href: indexFields().url },
        { title: field.name, href: '#' },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="p-6">
                <div className="mb-6 flex items-center justify-between">
                    <h1 className="text-2xl font-semibold">{field.name}</h1>
                    <div className="flex items-center gap-2">
                        <Button variant="outline" render={<Link href={indexFields().url}>Back</Link>} />
                        <Button render={<Link href={editField.url(field.id)}>Edit</Link>} />
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Overview</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3 text-sm">
                            <div>
                                <div className="text-muted-foreground">Handle</div>
                                <div className="font-mono">{field.handle}</div>
                            </div>
                            <div>
                                <div className="text-muted-foreground">Type</div>
                                <Badge variant="secondary">{field.data_type}</Badge>
                            </div>
                            <div>
                                <div className="text-muted-foreground">Content Type</div>
                                {field.content_type ? (
                                    <div>
                                        {field.content_type.name}
                                        <Badge className="ml-2" variant="outline">
                                            {field.content_type.slug}
                                        </Badge>
                                    </div>
                                ) : (
                                    <div className="text-muted-foreground">—</div>
                                )}
                            </div>
                            <div>
                                <div className="text-muted-foreground">Sort Order</div>
                                <div>{field.sort_order ?? 0}</div>
                            </div>
                            <div>
                                <div className="text-muted-foreground">Flags</div>
                                <div className="mt-1 flex flex-wrap gap-2">
                                    {field.is_required && <Badge variant="outline">required</Badge>}
                                    {field.is_unique && <Badge variant="outline">unique</Badge>}
                                    {field.is_translatable && <Badge variant="outline">i18n</Badge>}
                                    {field.is_repeatable && <Badge variant="outline">repeatable</Badge>}
                                    {!field.is_required && !field.is_unique && !field.is_translatable && !field.is_repeatable && (
                                        <span className="text-muted-foreground">None</span>
                                    )}
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}

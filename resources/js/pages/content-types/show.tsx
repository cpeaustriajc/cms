import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { edit as editTypes, index as indexTypes, show as showTypes } from '@/routes/content-types';
import { BreadcrumbItem, Content, ContentType, Field } from '@/types';
import { Link } from '@inertiajs/react';

interface ContentTypeDetail extends ContentType {
    fields: Field[];
    contents: Pick<Content, 'id' | 'title' | 'path'>[];
}

export default function Show({ contentType }: { contentType: ContentTypeDetail }) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: dashboard().url },
        { title: 'Content Types', href: indexTypes().url },
        { title: contentType.name, href: showTypes.url(contentType.id) },
    ];
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="mx-auto max-w-5xl space-y-6 p-6">
                <div className="flex items-center justify-between">
                    <div className="text-2xl font-semibold">{contentType.name}</div>
                    <div className="flex gap-2">
                        <Button variant="outline" asChild>
                            <Link href={indexTypes().url}>Back</Link>
                        </Button>
                        <Button asChild>
                            <Link href={editTypes.url(contentType.id)}>Edit</Link>
                        </Button>
                    </div>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Details</CardTitle>
                        <CardDescription>Basic information about this content type.</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-3">
                        <div>
                            <div className="text-sm text-muted-foreground">Slug</div>
                            <Badge>/{contentType.slug}</Badge>
                        </div>

                        {contentType.description && (
                            <div>
                                <div className="text-sm text-muted-foreground">Description</div>
                                <div>{contentType.description}</div>
                            </div>
                        )}
                    </CardContent>
                </Card>

                <div className="grid gap-6 md:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Fields</CardTitle>
                            <CardDescription>Fields defined for this content type.</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            {contentType.fields.length === 0 && <div className="text-sm text-muted-foreground">No fields defined.</div>}
                            {contentType.fields.map((field) => (
                                <div key={field.id} className="space-y-1">
                                    <div className="font-medium">
                                        {field.name} <span className="text-muted-foreground">({field.handle})</span>
                                    </div>
                                    <Badge variant="outline">
                                        <code>{field.data_type}</code>
                                    </Badge>
                                </div>
                            ))}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Recent Contents</CardTitle>
                            <CardDescription>Recently created contents of this type.</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            {contentType.contents.length === 0 && <div className="text-sm text-muted-foreground">No contents created.</div>}
                            {contentType.contents.map((content) => (
                                <div key={content.id} className="space-y-1">
                                    <Link href={content.path ?? '#'} className="font-medium hover:underline">
                                        {content.title ?? 'Untitled'}
                                    </Link>
                                    {content.path && (
                                        <div>
                                            <Badge variant="outline">/{content.path}</Badge>
                                        </div>
                                    )}
                                </div>

                            ))}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}

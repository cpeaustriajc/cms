import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import content, { create, destroy, edit } from '@/routes/contents';
import { BreadcrumbItem, Content, PaginatedData } from '@/types';
import { Link, router } from '@inertiajs/react';

interface IndexProps {
    contents: PaginatedData<Content>;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: content.index().url,
    },
];

export default function Index(props: IndexProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="p-6">
                <div className="mb-6 flex items-center justify-between">
                    <h1 className="text-2xl font-semibold">Contents</h1>
                    <Button render={<Link href={create().url}>Create Content</Link>} />
                </div>

                {props.contents.data.length === 0 ? (
                    <Card>
                        <CardContent className="flex items-center justify-center py-12">
                            <div className="text-center">
                                <p className="mb-4 text-muted-foreground">No contents found.</p>
                                <Button render={<Link href={create().url}>Create your first content</Link>} />
                            </div>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {props.contents.data.map((content) => (
                            <Card key={content.id} className="transition-shadow hover:shadow-md">
                                <CardHeader>
                                    <div className="flex items-start justify-between">
                                        <div className="space-y-1">
                                            <CardTitle className="text-lg">{content.title || `Content #${content.id}`}</CardTitle>
                                            <CardDescription>{content.type.name}</CardDescription>
                                        </div>
                                        <Badge variant={content.status.code === 'published' ? 'default' : 'secondary'}>{content.status.code}</Badge>
                                    </div>
                                </CardHeader>

                                <CardContent className="space-y-2">
                                    {content.path && (
                                        <div className="text-sm">
                                            <span className="text-muted-foreground">Path: </span>
                                            <code className="rounded bg-muted px-1 py-0.5 text-xs">{content.path}</code>
                                        </div>
                                    )}

                                    {content.published_at && (
                                        <div className="text-sm">
                                            <span className="text-muted-foreground">Published: </span>
                                            {new Date(content.published_at).toLocaleDateString()}
                                        </div>
                                    )}
                                </CardContent>

                                <CardFooter className="gap-2">
                                    <Button variant="outline" size="sm" render={<Link href={edit.url(content.id)}>Edit</Link>} />
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        onClick={() => {
                                            if (confirm('Delete this content?')) {
                                                router.delete(destroy.url(content.id));
                                            }
                                        }}
                                    >
                                        Delete
                                    </Button>
                                </CardFooter>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

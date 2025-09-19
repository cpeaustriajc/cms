import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { create as createType, edit as editType, show as showType } from '@/routes/content-types';
import { BreadcrumbItem, ContentTypeLite, PaginatedData } from '@/types';
import { Link, router, usePage } from '@inertiajs/react';
import { ChangeEvent } from 'react';

interface IndexProps {
    contentTypes: PaginatedData<ContentTypeLite>;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Content Types', href: createType().url.replace('/create', '') },
];

export default function Index({ contentTypes }: IndexProps) {
    const { url } = usePage();

    const onFilter = (event: ChangeEvent<HTMLInputElement>) => {
        const params = new URLSearchParams(window.location.search);

        const { name, value } = event.target;

        if (value) params.set(name, value);
        else params.delete(name);

        router.visit(`${url}?${params.toString()}`);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="p-6">
                <div className="mb-6 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <h1 className="text-2xl font-semibold">Content Types</h1>
                    <div className="flex items-center gap-3">
                        <Input
                            type="search"
                            name="search"
                            placeholder="Search content types..."
                            defaultValue={new URLSearchParams(window.location.search).get('search') ?? undefined}
                            onChange={onFilter}
                        />
                        <Button render={<Link href={createType().url}>Create</Link>} />
                    </div>
                </div>
                {contentTypes.data.length > 0 && (
                    <div className="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        {contentTypes.data.map((type) => (
                            <Card key={type.id} className="hover:shadow-lg">
                                <CardHeader>
                                    <CardTitle>
                                        <Link href={createType().url.replace('/create', `/${type.id}`)}>{type.name}</Link>
                                        <Badge className="ml-2">{type.slug}</Badge>
                                    </CardTitle>
                                    {type.description && <CardDescription>{type.description}</CardDescription>}
                                </CardHeader>
                                <CardContent>
                                    <div className="text-sm text-muted-foreground">
                                        <span className="mr-4">Fields: {type.fields_count}</span>
                                        <span>Contents: {type.contents_count}</span>
                                    </div>
                                </CardContent>
                                <CardFooter className="gap-2">
                                    <Button variant="outline" size="sm" render={<Link href={editType.url(type.id)}>Edit</Link>} />
                                    <Button variant="outline" size="sm" render={<Link href={showType.url(type.id)}>View</Link>} />
                                </CardFooter>
                            </Card>
                        ))}
                    </div>
                )}

                {contentTypes.data.length === 0 && (
                    <Card>
                        <CardContent className="flex items-center justify-center py-12">
                            <div className="text-center">
                                <p className="mb-4 text-muted-foreground">No content types found.</p>
                                <Button render={<Link href={createType().url}>Create your first content type</Link>} />
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </AppLayout>
    );
}

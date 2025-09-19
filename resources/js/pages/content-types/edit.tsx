import ContentTypeController from '@/actions/App/Http/Controllers/ContentTypeController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { edit as editTypes, index as indexTypes, show as showTypes } from '@/routes/content-types';
import { BreadcrumbItem, ContentType } from '@/types';
import { Form, Link } from '@inertiajs/react';
import { useState } from 'react';

export default function Edit({ contentType }: { contentType: ContentType }) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: dashboard().url },
        { title: 'Content Types', href: indexTypes().url },
        { title: contentType.name, href: showTypes.url(contentType.id) },
        { title: 'Edit', href: editTypes.url(contentType.id) },
    ];

    const [name, setName] = useState(contentType.name);
    const [slug, setSlug] = useState(contentType.slug);
    const [description, setDescription] = useState(contentType.description ?? '');

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="mx-auto max-w-3xl p-6">
                <div className="mb-6 flex items-center justify-between">
                    <h1 className="text-2xl font-semibold">Edit Content Type</h1>
                    <Button render={<Link href={showTypes.url(contentType.id)}>Back</Link>} variant="outline" />
                </div>

                <Form {...ContentTypeController.update['/content-types/{content_type}'].form(contentType.id)} className="space-y-6">
                    {({ errors }) => (
                        <>
                            <Card>
                                <CardHeader>
                                    <CardTitle>Details</CardTitle>
                                    <CardDescription>Structure defined for contents of this type.</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-3">
                                    <div>
                                        <Label htmlFor="name">Name</Label>
                                        <Input id="name" name="name" value={name} onChange={(e) => setName(e.target.value)} />
                                        <InputError message={errors.name} />
                                    </div>
                                    <div>
                                        <Label htmlFor="slug">Slug</Label>
                                        <Input id="slug" name="slug" value={slug} onChange={(e) => setSlug(e.target.value)} />
                                        <InputError message={errors.slug} />
                                    </div>
                                    <div>
                                        <Label htmlFor="description">Description</Label>
                                        <Textarea
                                            id="description"
                                            name="description"
                                            value={description}
                                            onChange={(e) => setDescription(e.target.value)}
                                            placeholder="Optional description"
                                        />
                                        <InputError message={errors?.description} />
                                    </div>
                                </CardContent>
                            </Card>
                        </>
                    )}
                </Form>
            </div>
        </AppLayout>
    );
}

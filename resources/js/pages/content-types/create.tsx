import ContentController from '@/actions/App/Http/Controllers/ContentController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { index as indexTypes } from '@/routes/content-types';
import { BreadcrumbItem } from '@/types';
import { Form, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Content Types', href: indexTypes().url },
    { title: 'Create', href: '#' },
];
export default function Create() {
    const [slug, setSlug] = useState('');
    const [name, setName] = useState('');

    useEffect(() => {
        const generatedSlug = name
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');

        if (!slug) {
            setSlug(generatedSlug);
        }
    }, [name]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="max-w-3xl p-6">
                <div className="mb-6 flex items-center justify-between">
                    <h1 className="text-2xl font-semibold">Create Content Type</h1>
                    <Button asChild variant="outline">
                        <Link href={indexTypes().url}>Back to Content Types</Link>
                    </Button>
                </div>

                <Form {...ContentController.store['/contents'].form()} className="space-y-6">
                    {({ processing, errors }) => (
                        <>
                            <Card>
                                <CardHeader>
                                    <CardTitle>Details</CardTitle>
                                    <CardDescription>Define the name, slug, and description.</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="name">Name</Label>

                                        <Input
                                            id="name"
                                            name="name"
                                            value={name}
                                            placeholder="Blog Post, Product, etc..."
                                            onChange={(e) => setName(e.target.value)}
                                            required
                                        />

                                        <InputError message={errors?.name} />
                                    </div>
                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="slug">Slug</Label>
                                        <Input
                                            id="slug"
                                            name="slug"
                                            value={slug}
                                            placeholder="blog-post, product, etc..."
                                            onChange={(e) => setSlug(e.target.value)}
                                            required
                                        />
                                        <InputError message={errors?.slug} />
                                    </div>
                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="description">Description</Label>
                                        <Textarea
                                            id="description"
                                            name="description"
                                            placeholder="A brief description of the content type."
                                            rows={3}
                                        />
                                        <InputError message={errors?.description} />
                                    </div>
                                </CardContent>
                                <CardFooter className="gap-3">
                                    <Button type="submit" disabled={processing}>
                                        {processing ? 'Creating...' : 'Create Content Type'}
                                    </Button>
                                    <Button variant="outline" asChild>
                                        <Link href={indexTypes().url}>Cancel</Link>
                                    </Button>
                                </CardFooter>
                            </Card>
                        </>
                    )}
                </Form>
            </div>
        </AppLayout>
    );
}

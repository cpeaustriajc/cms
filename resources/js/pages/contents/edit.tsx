import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { Form, Link, usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';

export default function Edit({ content, types, statuses, locales }: any) {
    const [typeId] = useState(content.content_type_id);
    const page = usePage();
    const selectedType = useMemo(() => types.find((t: any) => t.id === typeId), [types, typeId]);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
        },
        {
            title: 'Contents',
            href: '/content',
        },
        {
            title: 'Edit',
            href: page.url,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="mx-auto max-w-3xl p-6">
                <div className="mb-6 flex items-center justify-between">
                    <h1 className="text-2xl font-semibold">Edit Content #{content.id}</h1>
                    <Button variant="outline" render={<Link href="/content">Back</Link>} />
                </div>

                <Form action={`/content/${content.id}`} method="put">
                    {({ errors, processing }) => (
                        <div className="space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Details</CardTitle>
                                    <CardDescription>Update status and schedule publishing.</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div>
                                            <Label className="mb-1 block text-sm font-medium">Content Type</Label>
                                            <Input value={selectedType?.name ?? ''} disabled />
                                        </div>

                                        <div>
                                            <Label className="mb-1 block text-sm font-medium">Status</Label>
                                            <Select name="status_id" defaultValue={content.status_id ?? ''}>
                                                <SelectTrigger className="w-full rounded border px-3 py-2">
                                                    <SelectValue placeholder="Select status" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {statuses.map((s: any) => (
                                                        <SelectItem key={s.id} value={s.id}>
                                                            {s.label}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            {errors?.status_id && <p className="mt-1 text-sm text-red-600">{errors.status_id}</p>}
                                        </div>

                                        <div>
                                            <Label className="mb-1 block text-sm font-medium">Published At</Label>
                                            <Input type="datetime-local" name="published_at" defaultValue={content.published_at ?? ''} />
                                            {errors?.published_at && <p className="mt-1 text-sm text-red-600">{errors.published_at}</p>}
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Route</CardTitle>
                                    <CardDescription>Optional URL settings for this content.</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div>
                                            <Label className="mb-1 block text-sm font-medium">Locale</Label>
                                            <Select name="route[locale_code]" defaultValue="">
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Default" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {locales.map((l: any) => (
                                                        <SelectItem key={l.id} value={l.code}>
                                                            {l.name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        <div className="sm:col-span-2">
                                            <Label className="mb-1 block text-sm font-medium">Path</Label>
                                            <Input
                                                type="text"
                                                name="route[path]"
                                                defaultValue={content.route?.path ?? ''}
                                                placeholder="/blog/hello-world"
                                            />
                                        </div>
                                        <Label className="inline-flex items-center gap-2">
                                            <Checkbox name="route[is_primary]" defaultChecked={content.route?.is_primary ?? false} />
                                            <span>Primary</span>
                                        </Label>
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Fields</CardTitle>
                                    <CardDescription>Content fields for this type.</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    {!selectedType?.fields?.length && <p className="text-sm text-gray-500">No fields defined for this type.</p>}
                                    {selectedType?.fields?.map((field: any, index: any) => (
                                        <FieldRow
                                            key={field.handle}
                                            field={field}
                                            index={index}
                                            locales={locales}
                                            errors={errors}
                                            defaultValue={content.values?.[field.handle] ?? ''}
                                        />
                                    ))}
                                </CardContent>
                            </Card>

                            <Card>
                                <CardFooter className="gap-3">
                                    <Button type="submit" disabled={processing}>
                                        {processing ? 'Saving...' : 'Update'}
                                    </Button>
                                    <Button variant="outline" render={<Link href="/admin/contents">Cancel</Link>} />
                                </CardFooter>
                            </Card>
                        </div>
                    )}
                </Form>
            </div>
        </AppLayout>
    );
}

function FieldRow({ field, index, locales, errors, defaultValue }: any) {
    const inputName = (key: string) => `fields[${index}][${key}]`;

    return (
        <div className="grid grid-cols-1 gap-4 rounded-xl border p-4 sm:grid-cols-3">
            <div className="sm:col-span-3">
                <div className="text-sm font-medium">
                    {field.name} <span className="text-muted-foreground">({field.handle})</span>
                </div>
                <input type="hidden" name={inputName('handle')} value={field.handle} />
            </div>

            {field.is_translatable && (
                <div>
                    <Label className="mb-1 block text-sm font-medium">Locale</Label>
                    <Select name={inputName('locale_code')} defaultValue="">
                        <SelectTrigger className="w-full">
                            <SelectValue placeholder="Default" />
                            <SelectContent>
                                <SelectItem value="">Default</SelectItem>
                                {locales.map((locale) => (
                                    <SelectItem key={locale.id} value={locale.code}>
                                        {locale.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </SelectTrigger>
                    </Select>
                </div>
            )}

            <div className={field.is_translatable ? 'sm:col-span-2' : 'sm:col-span-3'}>
                <Label className="mb-1 block text-sm font-medium">Value</Label>
                <FieldInput name={inputName('value')} dataType={field.data_type} defaultValue={defaultValue} />
                {errors?.fields?.[index]?.value && <p className="mt-1 text-sm text-red-600">{errors.fields[index].value}</p>}
            </div>
        </div>
    );
}

function FieldInput({ name, dataType, defaultValue }) {
    switch (dataType) {
        case 'text':
        case 'richtext':
            return <Textarea name={name} defaultValue={defaultValue ?? ''} className="h-28" />;
        case 'integer':
        case 'reference':
            return <Input type="number" name={name} defaultValue={defaultValue ?? ''} />;
        case 'decimal':
            return <Input type="number" step="0.01" name={name} defaultValue={defaultValue ?? ''} />;
        case 'boolean':
            return (
                <Select name={name} defaultValue={defaultValue === true ? '1' : defaultValue === false ? '0' : ''}>
                    <SelectTrigger className="w-full rounded border px-3 py-2">
                        <SelectValue placeholder="Default" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="none">-</SelectItem>
                        <SelectItem value="1">True</SelectItem>
                        <SelectItem value="0">False</SelectItem>
                    </SelectContent>
                </Select>
            );
        case 'datetime':
            return <Input type="datetime-local" name={name} defaultValue={defaultValue ?? ''} />;
        default:
            return <Input type="text" name={name} defaultValue={defaultValue ?? ''} />;
    }
}

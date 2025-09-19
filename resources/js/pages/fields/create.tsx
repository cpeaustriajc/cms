import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { index as indexFields, store as storeField } from '@/routes/fields';
import { BreadcrumbItem } from '@/types';
import { Form, Link } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';

interface ContentTypeLite {
    id: number;
    name: string;
    slug: string;
}

interface CreateProps {
    contentTypes: ContentTypeLite[];
    selectedContentType?: string;
    fieldTypes: string[];
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Fields', href: indexFields().url },
    { title: 'Create', href: '#' },
];

export default function Create({ contentTypes, selectedContentType, fieldTypes }: CreateProps) {
    const [contentTypeId, setContentTypeId] = useState<string>('');
    const [name, setName] = useState('');
    const [handle, setHandle] = useState('');
    const [dataType, setDataType] = useState('');

    useEffect(() => {
        if (!contentTypeId && selectedContentType) {
            const found = contentTypes.find((c) => c.slug === selectedContentType);
            if (found) setContentTypeId(String(found.id));
        }
    }, [selectedContentType, contentTypes]);

    useEffect(() => {
        if (!handle && name) {
            const generated = name
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9_]+/g, '_')
                .replace(/^_+|_+$/g, '');
            setHandle(generated);
        }
    }, [name]);

    const selectedName = useMemo(() => {
        const found = contentTypes.find((c) => String(c.id) === contentTypeId);
        return found?.name ?? 'Select content type';
    }, [contentTypes, contentTypeId]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="max-w-3xl p-6">
                <div className="mb-6 flex items-center justify-between">
                    <h1 className="text-2xl font-semibold">Create Field</h1>
                    <Button variant="outline" render={<Link href={indexFields().url}>Back to Fields</Link>} />
                </div>

                <Form {...storeField.form()} className="space-y-6">
                    {({ processing, errors }) => (
                        <>
                            <Card>
                                <CardHeader>
                                    <CardTitle>Details</CardTitle>
                                    <CardDescription>Attach the field to a content type and define its structure.</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="content_type_id">Content Type</Label>
                                        <input type="hidden" name="content_type_id" value={contentTypeId} />
                                        <Select value={contentTypeId} onValueChange={(v) => setContentTypeId(v)}>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select content type">{selectedName}</SelectValue>
                                            </SelectTrigger>
                                            <SelectContent>
                                                {contentTypes.map((ct) => (
                                                    <SelectItem key={ct.id} value={String(ct.id)}>
                                                        {ct.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        <InputError message={errors?.content_type_id} />
                                    </div>

                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div className="flex flex-col gap-2">
                                            <Label htmlFor="name">Name</Label>
                                            <Input id="name" name="name" value={name} onChange={(e) => setName(e.target.value)} required />
                                            <InputError message={errors?.name} />
                                        </div>
                                        <div className="flex flex-col gap-2">
                                            <Label htmlFor="handle">Handle</Label>
                                            <Input id="handle" name="handle" value={handle} onChange={(e) => setHandle(e.target.value)} required />
                                            <InputError message={errors?.handle} />
                                        </div>
                                    </div>

                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="data_type">Type</Label>
                                        <input type="hidden" name="data_type" value={dataType} />
                                        <Select value={dataType} onValueChange={(v) => setDataType(v)}>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select a type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {fieldTypes.map((t) => (
                                                    <SelectItem key={t} value={t}>
                                                        {t}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        <InputError message={errors?.data_type} />
                                    </div>

                                    <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
                                        <NamedCheckbox name="is_required" label="Required" />
                                        <NamedCheckbox name="is_unique" label="Unique" />
                                        <NamedCheckbox name="is_translatable" label="Translatable" />
                                        <NamedCheckbox name="is_repeatable" label="Repeatable" />
                                    </div>

                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="sort_order">Sort Order</Label>
                                        <Input id="sort_order" name="sort_order" type="number" min={0} defaultValue={0} />
                                        <InputError message={errors?.sort_order} />
                                    </div>
                                </CardContent>
                                <CardFooter className="gap-3">
                                    <Button type="submit" disabled={processing}>
                                        {processing ? 'Creating…' : 'Create Field'}
                                    </Button>
                                    <Button variant="outline" render={<Link href={indexFields().url}>Cancel</Link>} />
                                </CardFooter>
                            </Card>
                        </>
                    )}
                </Form>
            </div>
        </AppLayout>
    );
}

function NamedCheckbox({ name, label, defaultChecked = false }: { name: string; label: string; defaultChecked?: boolean }) {
    const [checked, setChecked] = useState<boolean>(!!defaultChecked);
    return (
        <>
            {checked && <input type="hidden" name={name} value="1" />}
            <div className="inline-flex items-center gap-2">
                <Checkbox checked={checked} onCheckedChange={(v) => setChecked(!!v)} id={name} />
                <Label htmlFor={name}>{label}</Label>
            </div>
        </>
    );
}

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { Form, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';

type Status = { id: number; label: string };
type Locale = { id: number; code: string; name: string };
type Field = {
    handle: string;
    name: string;
    is_translatable?: boolean;
    data_type: string;
};
type ContentType = {
    id: number;
    name: string;
    fields?: Field[];
};

interface CreateProps {
    types: ContentType[];
    statuses: Status[];
    locales: Locale[];
}

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
        title: 'Create',
        href: '/content/create',
    },
];

export default function Create({ types, statuses, locales }: CreateProps) {
    const [typeId, setTypeId] = useState<number | null>(types[0]?.id ?? null);
    const selectedType = useMemo(() => types.find((t: ContentType) => t.id === typeId), [types, typeId]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="mx-auto max-w-3xl p-6">
                <div className="mb-6 flex items-center justify-between">
                    <h1 className="text-2xl font-semibold">Create Content</h1>
                    <Button asChild variant="outline">
                        <Link href="/admin/contents">Back</Link>
                    </Button>
                </div>

                <Form action="/admin/contents" method="post">
                    {({ errors, processing }) => (
                        <div className="space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Details</CardTitle>
                                    <CardDescription>Select type, status and schedule publishing.</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div>
                                            <Label className="mb-1 block">Content Type</Label>
                                            <NamedSelect
                                                name="content_type_id"
                                                value={String(typeId ?? '')}
                                                onChange={(val: string) => setTypeId(Number(val))}
                                                placeholder="Select type"
                                                options={types.map((t: ContentType) => ({ value: String(t.id), label: t.name }))}
                                            />
                                            {errors?.content_type_id && <p className="mt-1 text-sm text-red-600">{errors.content_type_id}</p>}
                                        </div>

                                        <div>
                                            <Label className="mb-1 block">Status</Label>
                                            <NamedSelect
                                                name="status_id"
                                                placeholder="Select status"
                                                options={statuses.map((s: Status) => ({ value: String(s.id), label: s.label }))}
                                            />
                                            {errors?.status_id && <p className="mt-1 text-sm text-red-600">{errors.status_id}</p>}
                                        </div>

                                        <div>
                                            <Label className="mb-1 block">Published At</Label>
                                            <Input type="datetime-local" name="published_at" />
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
                                            <Label className="mb-1 block">Locale</Label>
                                            <NamedSelect
                                                name="route[locale_code]"
                                                placeholder="Default"
                                                allowEmpty
                                                options={locales.map((l: Locale) => ({ value: String(l.code), label: l.name }))}
                                            />
                                        </div>
                                        <div className="sm:col-span-2">
                                            <Label className="mb-1 block">Path</Label>
                                            <Input name="route[path]" placeholder="/blog/hello-world" />
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <NamedCheckbox name="route[is_primary]" label="Primary" />
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Fields</CardTitle>
                                    <CardDescription>Content fields for the selected type.</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    {!selectedType?.fields?.length && (
                                        <p className="text-sm text-muted-foreground">No fields defined for this type.</p>
                                    )}
                                    {selectedType?.fields?.map((f: Field, idx: number) => (
                                        <FieldRow key={f.handle} field={f} index={idx} locales={locales} errors={errors} />
                                    ))}
                                </CardContent>
                            </Card>

                            <Card>
                                <CardFooter className="gap-3">
                                    <Button type="submit" disabled={processing}>
                                        {processing ? 'Saving...' : 'Create'}
                                    </Button>
                                    <Button asChild variant="outline">
                                        <Link href="/admin/contents">Cancel</Link>
                                    </Button>
                                </CardFooter>
                            </Card>
                        </div>
                    )}
                </Form>
            </div>
        </AppLayout>
    );
}

function FieldRow({ field, index, locales, errors }: { field: Field; index: number; locales: Locale[]; errors: any }) {
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
                    <Label className="mb-1 block">Locale</Label>
                    <NamedSelect
                        name={inputName('locale_code')}
                        placeholder="Default"
                        allowEmpty
                        options={locales.map((l: Locale) => ({ value: String(l.code), label: l.name }))}
                    />
                </div>
            )}

            <div className={field.is_translatable ? 'sm:col-span-2' : 'sm:col-span-3'}>
                <Label className="mb-1 block">Value</Label>
                <FieldInput name={inputName('value')} dataType={field.data_type} />
                {errors?.fields?.[index]?.value && <p className="mt-1 text-sm text-red-600">{errors.fields[index].value}</p>}
            </div>
        </div>
    );
}

function FieldInput({ name, dataType, defaultValue }: { name: string; dataType: string; defaultValue?: any }) {
    switch (dataType) {
        case 'text':
        case 'richtext':
            return <Textarea name={name} className="h-28" defaultValue={defaultValue} />;
        case 'integer':
        case 'reference':
            return <Input type="number" name={name} defaultValue={defaultValue} />;
        case 'decimal':
            return <Input type="number" step="0.01" name={name} defaultValue={defaultValue} />;
        case 'boolean':
            return (
                <NamedSelect
                    name={name}
                    allowEmpty
                    placeholder="—"
                    options={[
                        { value: '1', label: 'True' },
                        { value: '0', label: 'False' },
                    ]}
                />
            );
        case 'datetime':
            return <Input type="datetime-local" name={name} defaultValue={defaultValue} />;
        default:
            return <Input type="text" name={name} defaultValue={defaultValue} />;
    }
}

// Helper components to bridge shadcn (Radix) controls with native form submission
function NamedSelect({
    name,
    options,
    value,
    onChange,
    placeholder = 'Select',
    allowEmpty = false,
    defaultValue,
}: {
    name: string;
    options: { value: string; label: string }[];
    value?: string;
    onChange?: (value: string) => void;
    placeholder?: string;
    allowEmpty?: boolean;
    defaultValue?: string;
}) {
    const [val, setVal] = useState<string>(value ?? defaultValue ?? '');
    const handleChange = (v: string) => {
        setVal(v);
        if (onChange) onChange(v);
    };
    return (
        <>
            <input type="hidden" name={name} value={val} />
            <Select value={val} onValueChange={handleChange}>
                <SelectTrigger>
                    <SelectValue placeholder={placeholder} />
                </SelectTrigger>
                <SelectContent>
                    {allowEmpty && <SelectItem value="none">{placeholder}</SelectItem>}
                    {options.map((opt) => (
                        <SelectItem key={opt.value} value={opt.value}>
                            {opt.label}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
        </>
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

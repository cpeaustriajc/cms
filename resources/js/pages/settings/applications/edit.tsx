import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { Form, Head, Link, usePage } from '@inertiajs/react';

interface Props {
    application: {
        id: string;
        name?: string;
        redirect_uris?: string | string[];
        confidential?: boolean;
        type?: string;
        created_at?: string | null;
    };
    [k: string]: unknown;
}

export default function Edit() {
    const { props } = usePage<Props>();
    const application = props.application;

    const redirectValue = (() => {
        const r = application?.redirect_uris;
        if (!r) return '';
        return Array.isArray(r) ? r.join(',') : String(r);
    })();

    if (!application) {
        return (
            <AppLayout>
                <SettingsLayout>
                    <div className="p-6">
                        <p className="text-sm text-muted-foreground">Application not found.</p>
                        <Link href="/settings/applications" className="text-sm underline">
                            Back to applications
                        </Link>
                    </div>
                </SettingsLayout>
            </AppLayout>
        );
    }

    return (
        <AppLayout>
            <Head title={`Edit — ${application.name ?? 'Application'}`} />

            <SettingsLayout>
                <div className="mx-auto max-w-3xl space-y-6">
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-xl font-semibold">Edit application</h1>
                            <p className="text-sm text-muted-foreground">Update application name, redirect URIs and confidentiality.</p>
                        </div>

                        <Link href="/settings/applications" className="ml-4">
                            <Button variant="outline">Back</Button>
                        </Link>
                    </div>

                    <div className="rounded bg-white p-6 dark:bg-neutral-900">
                        <div className="mb-4 text-sm text-muted-foreground">
                            <div>
                                Client ID: <code className="ml-2">{application.id}</code>
                            </div>
                            {application.created_at && <div>Created: {String(application.created_at)}</div>}
                        </div>

                        <Form method="post" action={`/settings/applications/${application.id}`} className="space-y-4">
                            {/* Patch method */}
                            <input type="hidden" name="_method" value="PATCH" />

                            <div>
                                <Label htmlFor="name">Name</Label>
                                <Input id="name" name="name" defaultValue={application.name ?? ''} required />
                            </div>

                            <div>
                                <Label htmlFor="redirect_uris">Redirect URIs (comma separated)</Label>
                                <Textarea
                                    id="redirect_uris"
                                    name="redirect_uris"
                                    className="mt-1 block w-full rounded-md border px-3 py-2"
                                    rows={3}
                                    defaultValue={redirectValue}
                                    placeholder="https://example.com/callback,https://other.example/callback"
                                />
                                <p className="mt-1 text-xs text-muted-foreground">Enter one or more redirect URIs separated by commas.</p>
                            </div>

                            <div>
                                <Label htmlFor="type">Client type</Label>
                                <Select defaultValue={application.type ?? 'authorization_code'} name="type">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select type" className="w-full" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="authorization_code">Authorization Code</SelectItem>
                                        <SelectItem value="device">Device</SelectItem>
                                        <SelectItem value="personal_access">Personal Access</SelectItem>
                                        <SelectItem value="client_credentials">Client Credentials</SelectItem>
                                        <SelectItem value="password">Password</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div className="flex flex-col gap-2">
                                <div className="inline-flex items-center gap-2">
                                    <Checkbox
                                        id="device_flow"
                                        name="device_flow"
                                        defaultChecked={application.type === 'device'}
                                        className="rounded"
                                    />
                                    <Label htmlFor="device_flow">Device Flow</Label>
                                </div>
                                <p className="text-sm text-gray-500">
                                    Device Flow is a type of OAuth 2.0 flow that enables devices with limited input capabilities to obtain user
                                    authorization.
                                </p>
                            </div>

                            <div className="flex flex-col gap-2">
                                <label className="inline-flex items-center gap-2">
                                    <Checkbox name="confidential" defaultChecked={application.confidential ?? true} className="rounded" />
                                    <span className="text-sm">Confidential</span>
                                </label>
                                <p className="text-sm text-gray-500">
                                    Check if the client is a confidential client (e.g., server-side applications). Uncheck for public clients (e.g.,
                                    mobile or single-page applications).
                                </p>
                            </div>
                            <Button type="submit">Save changes</Button>
                        </Form>

                        <div className="mt-6 border-t pt-4">
                            <h3 className="mb-2 text-sm font-medium">Danger zone</h3>

                            <form
                                action={`/settings/applications/${application.id}`}
                                method="post"
                                onSubmit={(e) => {
                                    if (!confirm('Delete this application? This cannot be undone.')) {
                                        e.preventDefault();
                                    }
                                }}
                            >
                                <input type="hidden" name="_method" value="DELETE" />
                                <button type="submit" className="text-sm text-red-600 underline">
                                    Delete application
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}

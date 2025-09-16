import ApplicationsController from '@/actions/App/Http/Controllers/ApplicationsController';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { Form, Head, Link, usePage } from '@inertiajs/react';

type Application = {
    id: string;
    name?: string;
    redirect?: string | string[];
    created_at?: string | null;
    secret?: string; // only for new applications and confidential apps.
    [k: string]: unknown;
};

interface Props {
    applications: Application[];
    newApplication: Application;
    [k: string]: unknown;
}

export default function ApplicationsIndex() {
    const { props } = usePage<Props>();
    const applications: Application[] = props.applications ?? [];
    const newApplication = props.newApplication ?? null;

    return (
        <AppLayout>
            <Head title="Applications" />

            <SettingsLayout>
                <div className="space-y-6">
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-xl font-semibold">OAuth applications</h1>
                            <p className="text-sm text-muted-foreground">Manage the OAuth apps tied to your account.</p>
                        </div>

                        <Link href="/settings/applications/create" className="ml-4">
                            <Button>Create application</Button>
                        </Link>
                    </div>

                    {newApplication && (
                        <div className="rounded bg-neutral-50 p-4 dark:bg-neutral-900">
                            <div className="mb-1 font-medium">New application created</div>
                            <div className="text-sm">
                                <div>
                                    <strong>Client ID:</strong> <code className="ml-2">{newApplication.id}</code>
                                </div>
                                {newApplication.secret && (
                                    <div className="mt-2">
                                        <strong>Client secret (shown once):</strong>
                                        <div className="mt-1 rounded border bg-white px-3 py-2 break-all dark:bg-neutral-800">
                                            <code>{newApplication.secret}</code>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    )}

                    <div>
                        {applications.length === 0 ? (
                            <p className="text-sm text-muted-foreground">You don't have any applications yet.</p>
                        ) : (
                            <ul className="space-y-4">
                                {applications.map((app) => (
                                    <li
                                        key={app.id}
                                        className="flex flex-col gap-3 rounded border bg-white p-4 md:flex-row md:items-center md:justify-between dark:bg-neutral-900"
                                    >
                                        <div>
                                            <div className="flex items-center gap-2">
                                                <h3 className="font-medium">{app.name ?? 'Untitled'}</h3>
                                                <div className="text-xs text-muted-foreground">
                                                    ID: <code className="ml-1">{app.id}</code>
                                                </div>
                                            </div>

                                            {app.created_at && (
                                                <div className="mt-1 text-sm text-muted-foreground">Created: {String(app.created_at)}</div>
                                            )}

                                            {app.redirect && (
                                                <div className="mt-2 text-sm text-muted-foreground">
                                                    <div className="mb-1 text-xs font-medium">Redirect URIs</div>
                                                    {Array.isArray(app.redirect) ? (
                                                        <ul className="space-y-1 text-xs">
                                                            {app.redirect.map((r: string, i: number) => (
                                                                <li key={i} className="break-all">
                                                                    {r}
                                                                </li>
                                                            ))}
                                                        </ul>
                                                    ) : (
                                                        <div className="text-xs break-all">{String(app.redirect)}</div>
                                                    )}
                                                </div>
                                            )}
                                        </div>

                                        <div className="flex items-center gap-3">
                                            <Link href={`/settings/applications/${app.id}/edit`} className="text-sm">
                                                <Button variant="outline">Edit</Button>
                                            </Link>

                                            <Form {...ApplicationsController.destroy.form(app.id)}>
                                                <button
                                                    type="submit"
                                                    className="text-sm text-red-600 underline"
                                                    onClick={(e) => {
                                                        // small confirm prompt
                                                        if (!confirm('Delete this application? This cannot be undone.')) {
                                                            e.preventDefault();
                                                        }
                                                    }}
                                                >
                                                    Delete
                                                </button>
                                            </Form>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}

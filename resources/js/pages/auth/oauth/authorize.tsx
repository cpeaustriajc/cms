import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Head, usePage } from '@inertiajs/react';

type Props = {
    request: Record<string, unknown>;
    authToken?: string | null;
    client: {
        id: string;
        name: string;
        redirect?: string | string[];
        [k: string]: unknown;
    };
    user: {
        id: string;
        name?: string;
        email?: string;
        [k: string]: unknown;
    };
    scopes: Array<{ id: string; description?: string } | string>;
};

export default function OAuthAuthorize() {
    const { props } = usePage<Props>();
    const { request, client, scopes } = props;
    const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';

    function renderHiddenRequestInputs() {
        if (!request || typeof request !== 'object') {
            return null;
        }

        return Object.entries(request).map(([key, value]) => {
            if (value === null || value === undefined) return null;
            const t = typeof value;
            if (t === 'string' || t === 'number' || t === 'boolean') {
                return <input key={key} type="hidden" name={key} value={String(value)} />;
            }

            if (Array.isArray(value) && value.every((v) => ['string', 'number', 'boolean'].includes(typeof v))) {
                return <input key={key} type="hidden" name={key} value={String(value.join(','))} />;
            }

            return null;
        });
    }

    return (
        <div>
            <Head title="Authorize application" />

            <div className="mx-auto max-w-3xl px-4 py-12">
                <div className="rounded bg-white p-6 shadow dark:bg-neutral-900">
                    <h1 className="mb-2 text-xl font-semibold">Authorize application</h1>

                    <p className="mb-4 text-sm text-muted-foreground">
                        <strong>{client?.name ?? 'Unknown app'}</strong> is requesting permission to access your account.
                    </p>

                    {scopes && scopes.length > 0 && (
                        <div className="mb-4">
                            <h2 className="font-medium">Requested permissions</h2>
                            <p className="mb-2 text-sm text-muted-foreground">Select the permissions you want to grant.</p>

                            <div className="mt-2 space-y-2">
                                {scopes.map((s, idx) => {
                                    if (typeof s === 'string') {
                                        return (
                                            <label key={idx} className="flex items-start gap-2">
                                                <Checkbox defaultChecked name="scopes[]" disabled value={s} className="mt-1" />
                                                <span className="text-sm">{s}</span>
                                            </label>
                                        );
                                    }

                                    return (
                                        <label key={String(s.id ?? idx)} className="flex items-start gap-2">
                                            <Checkbox defaultChecked name="scopes[]" disabled value={s.id} className="mt-1" />
                                            <div className="text-sm">
                                                <div>{s.id}</div>
                                                {s.description && <div className="text-xs text-muted-foreground">{s.description}</div>}
                                            </div>
                                        </label>
                                    );
                                })}
                            </div>
                        </div>
                    )}

                    <div className="my-4 text-sm text-muted-foreground">
                        Authorizing will allow <strong>{client?.name}</strong> to access your account.
                    </div>

                    <div className="flex justify-end gap-3">
                        <form method="post" action="/oauth/authorize" className="space-y-2">
                            {renderHiddenRequestInputs()}
                            <input type="hidden" name="auth_token" value={String(props.authToken ?? '')} />
                            <input type="hidden" name="_token" value={csrf} />

                            <div className="flex items-center gap-3">
                                <Button type="submit">Authorize</Button>
                            </div>
                        </form>

                        <form method="post" action="/oauth/authorize">
                            {renderHiddenRequestInputs()}
                            <input type="hidden" name="auth_token" value={String(props.authToken ?? '')} />
                            <input type="hidden" name="_token" value={csrf} />
                            <input type="hidden" name="_method" value="DELETE" />
                            <Button variant="secondary" type="submit">
                                Deny
                            </Button>
                        </form>
                    </div>

                    <div className="mt-6 text-xs text-muted-foreground">
                        Requesting app ID: <code className="ml-1">{client?.id}</code>
                    </div>
                </div>
            </div>
        </div>
    );
}

import ApplicationsController from '@/actions/App/Http/Controllers/ApplicationsController';
import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { create as createApplications } from '@/routes/applications';
import { BreadcrumbItem } from '@/types';
import { Form, Head } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'OAuth Applications',
        href: createApplications().url,
    },
];

export default function ApplicationsCreate() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="OAuth Applications" />
            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="OAuth Applications" description="Manage your OAuth applications" />
                    <div>
                        <Form {...ApplicationsController.store.form()} className="space-y-4">
                            {({ processing, errors }) => (
                                <>
                                    <div>
                                        <Label htmlFor="name">Application Name</Label>
                                        <Input id="name" name="name" placeholder="My Application Name" required />
                                        <InputError message={errors.name} />
                                    </div>
                                    <div>
                                        <Label htmlFor="redirect_uris">Redirect URIs</Label>
                                        <Input
                                            id="redirect_uris"
                                            name="redirect_uris"
                                            placeholder="https://example.com/callback,https://example.org/callback"
                                            required
                                        />
                                        <InputError message={errors.redirect_uris} />
                                    </div>
                                    <div>
                                        <Label htmlFor="response_type">Client Type</Label>
                                        <Select name="response_type" defaultValue="authorization_code">
                                            <SelectTrigger id="response_type" className="w-full">
                                                <SelectValue placeholder="Select a client type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="authorization_code">Authorization Code</SelectItem>
                                                <SelectItem value="personal_access">Personal Access</SelectItem>
                                                <SelectItem value="client_credentials">Client Credentials</SelectItem>
                                                <SelectItem value="password">Password Grant</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <InputError message={errors.response_type} />
                                    </div>

                                    <div>
                                        <Checkbox id="is_confidential" name="is_confidential" className="mr-2" />
                                        <Label htmlFor="is_confidential">Confidential Client</Label>
                                        <p className="text-sm text-gray-500">
                                            Check if the client is a confidential client (e.g., server-side applications). Uncheck for public clients
                                            (e.g., mobile or single-page applications).
                                        </p>
                                        <InputError message={errors.is_confidential} />
                                    </div>
                                    <div>
                                        <Checkbox id="device_flow" name="device_flow" className="mr-2" />
                                        <Label htmlFor="device_flow">Enable Device Flow</Label>
                                        <p className="text-sm text-gray-500">
                                            Device Flow is a type of OAuth 2.0 flow that enables devices with limited input capabilities to obtain
                                            user authorization.
                                        </p>
                                        <InputError message={errors.device_flow} />
                                    </div>
                                    <Button disabled={processing} type="submit">
                                        {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                                        Create Application
                                    </Button>
                                </>
                            )}
                        </Form>
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}

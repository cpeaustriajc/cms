import { useNavigate } from 'react-router';
import { LoginForm } from '~/components/login-form';
import { Button } from '~/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '~/components/ui/card';
import { apiFetch, getTokens, logout } from '~/lib/oauth';
import type { Route } from './+types/home';

export function meta({}: Route.MetaArgs) {
    return [{ title: 'Blog App' }, { name: 'description', content: 'Integrated app for the CMS Laravel app' }];
}

export async function clientLoader() {
    const tokens = getTokens();

    if (!tokens) {
        return { authenticated: false, me: null };
    }

    const userApiResponse = await apiFetch('http://127.0.0.1:8000/api/user');

    const contentsRes = await apiFetch('http://127.0.0.1:8000/api/contents');

    const me = await userApiResponse.json();
    const contents = await contentsRes.json();
    return {
        me,
        contents,
    };
}

export default function Home({ loaderData }: Route.ComponentProps) {
    const navigate = useNavigate();

    const onLogout = () => {
        logout();
        navigate('.', { replace: true });
    };

    return (
        <div className="flex min-h-svh flex-col">
            <header className="flex justify-between border-b px-8 py-4">
                <h1 className="text-2xl font-bold">Blog App</h1>

                {loaderData.me && (
                    <Button className="self-end" onClick={onLogout}>
                        Sign Out
                    </Button>
                )}
            </header>
            <div className="flex w-full items-center justify-center p-6 md:p-10">
                <div className="w-full">
                    {!loaderData.me && <LoginForm />}
                    <div className="my-4 grid w-full grid-cols-3 gap-4">
                        {loaderData.contents &&
                            loaderData.contents.data.map((content: any) => (
                                <Card key={content.id} className="mb-4 rounded border p-4">
                                    <CardHeader>
                                        <CardTitle className="mb-2 text-xl font-bold">{content.title}</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        {content.fields.map((field) => (
                                            <span key={field.id}>{field.value_string}</span>
                                        ))}
                                    </CardContent>
                                </Card>
                            ))}
                    </div>
                </div>
            </div>
        </div>
    );
}

import { useEffect, useState } from 'react';
import { handleOAuthCallback } from '~/lib/oauth';
import type { Route } from './+types/oauth-callback';

export function meta({}: Route.MetaArgs) {
    return [
        {
            title: 'Signing in...',
        },
    ];
}

export default function OAuthCallback() {
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        handleOAuthCallback()
            .then(() => {
                window.location.replace('/');
            })
            .catch((e) => setError(e instanceof Error ? e.message : String(e)));
    }, []);

    if (error) {
        return (
            <div className='p-6'>
                <h1 className='text-2xl font-bold mb-4'>Error during sign-in</h1>
                <p className='text-red-600'>An error occurred: {error}</p>
            </div>
        );
    }

    return <div className='p-6'>Signing you in...</div>;
}

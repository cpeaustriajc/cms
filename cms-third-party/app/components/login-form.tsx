import { File } from 'lucide-react';
import type React from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '~/components/ui/card';
import { beginOAuth } from '~/lib/oauth';
import { cn } from '~/lib/utils';
import { Button } from './ui/button';

export function LoginForm({ className, ...props }: React.ComponentProps<'div'>) {
    return (
        <div className={cn('flex flex-col gap-6', className)} {...props}>
            <Card>
                <CardHeader>
                    <CardTitle>Login to your account</CardTitle>
                    <CardDescription>Sign in by clicking the button below.</CardDescription>
                </CardHeader>
                <CardContent>
                    <Button className="w-full" onClick={() => beginOAuth(['user:read', 'content:read', 'asset:read', 'content:write'])}>
                        <File />
                        <span>Sign In with CMS</span>
                    </Button>
                </CardContent>
            </Card>
        </div>
    );
}

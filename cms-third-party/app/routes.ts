import { type RouteConfig, index, route } from '@react-router/dev/routes';

export default [index('routes/home.tsx'), route('oauth/callback', 'routes/oauth-callback.tsx')] satisfies RouteConfig;

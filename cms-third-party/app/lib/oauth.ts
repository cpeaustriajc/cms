const API_BASE = import.meta.env.VITE_API_BASE ?? 'http://127.0.1:8000';
const OAUTH_CLIENT_ID = import.meta.env.VITE_OAUTH_CLIENT_ID;
const OAUTH_REDIRECT_URI = import.meta.env.VITE_OAUTH_REDIRECT_URI ?? 'http://127.0.0.1:5174/oauth/callback';

const AUTHORIZATION_ENDPOINT = `${API_BASE}/oauth/authorize`;
const TOKEN_ENDPOINT = `${API_BASE}/oauth/token`;

function base64url(input: ArrayBuffer): string {
    const bytes = new Uint8Array(input);
    let str = '';

    for (let i = 0; i < bytes.byteLength; i++) {
        str += String.fromCharCode(bytes[i]);
    }

    return btoa(str).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
}

function randomUrlSafe(size = 96): string {
    const bytes = new Uint8Array(size);

    crypto.getRandomValues(bytes);

    return base64url(bytes.buffer);
}

async function sha256(input: string): Promise<string> {
    const data = new TextEncoder().encode(input);
    const digest = await crypto.subtle.digest('SHA-256', data);

    return base64url(digest);
}

const PKCE_VERIFIER_KEY = 'oauth.pkce_verifier';
const OAUTH_STATE_KEY = 'oauth.state';

export type Tokens = {
    token_type: 'Bearer';
    access_token: string;
    refresh_token?: string;
    expires_in: number;
    scope?: string;
};

export function saveTokens(tokens: Tokens): void {
    localStorage.setItem('oauth.tokens', JSON.stringify(tokens));
}

export function getTokens(): Tokens | null {
    const item = localStorage.getItem('oauth.tokens');
    return item ? (JSON.parse(item) as Tokens) : null;
}

export async function beginOAuth(scopes: string[] = []): Promise<void> {
    const codeVerifier = randomUrlSafe(96);
    const codeChallenge = await sha256(codeVerifier);
    const state = randomUrlSafe(32);

    sessionStorage.setItem(PKCE_VERIFIER_KEY, codeVerifier);
    sessionStorage.setItem(OAUTH_STATE_KEY, state);

    const params = new URLSearchParams({
        response_type: 'code',
        client_id: OAUTH_CLIENT_ID,
        redirect_uri: OAUTH_REDIRECT_URI,
        scope: scopes.join(' '),
        state,
        code_challenge: codeChallenge,
        code_challenge_method: 'S256',
    });

    window.location.href = `${AUTHORIZATION_ENDPOINT}?${params.toString()}`;
}

export async function handleOAuthCallback(): Promise<Tokens> {
    const url = new URL(window.location.href);
    const code = url.searchParams.get('code');
    const state = url.searchParams.get('state');
    const originalState = sessionStorage.getItem(OAUTH_STATE_KEY);
    const codeVerifier = sessionStorage.getItem(PKCE_VERIFIER_KEY);

    if (!code) throw new Error('Authorization code not found in URL');
    if (!state || !originalState || state !== originalState) throw new Error('Invalid OAuth state');
    if (!codeVerifier) throw new Error('Missing PKCE verifier (session expired?)');

    const body = new URLSearchParams({
        grant_type: 'authorization_code',
        client_id: OAUTH_CLIENT_ID,
        code,
        code_verifier: codeVerifier,
        redirect_uri: OAUTH_REDIRECT_URI,
    });

    const res = await fetch(TOKEN_ENDPOINT, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: body.toString(),
    });

    if (!res.ok) {
        const error = await res.json().catch(() => null);
        throw new Error(error?.error_description ?? `Token exchange failed with status ${res.status}`);
    }

    const tokens = (await res.json()) as Tokens;
    saveTokens(tokens);

    sessionStorage.removeItem(PKCE_VERIFIER_KEY);
    sessionStorage.removeItem(OAUTH_STATE_KEY);

    const cleanUrl = new URL(window.location.href);
    cleanUrl.searchParams.delete('code');
    cleanUrl.searchParams.delete('state');
    window.history.replaceState({}, document.title, cleanUrl.toString());

    return tokens;
}


export async function refreshAccessToken(): Promise<Tokens> {
    const tokens = getTokens();
    if (!tokens?.refresh_token) throw new Error('No refresh token available');

    const body = new URLSearchParams({
        grant_type: 'refresh_token',
        client_id: OAUTH_CLIENT_ID,
        refresh_token: tokens.refresh_token,
    });

    const res = await fetch(TOKEN_ENDPOINT, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: body.toString(),
    });

    if (!res.ok) {
        const error = await res.text();
        throw new Error(`Token refresh failed with status ${res.status}: ${error}`);
    }

    const next = (await res.json()) as Tokens;
    saveTokens(next);
    return next;
}

export async function apiFetch(input: RequestInfo, init?: RequestInit): Promise<Response> {
    let tokens = getTokens();
    const headers = new Headers(init?.headers ?? {});

    if (tokens?.access_token) {
        headers.set('Authorization', `Bearer ${tokens.access_token}`);
        headers.set('Accept', 'application/json');
    }

    return fetch(input, { ...init, headers });
}

export function logout(): void {
    localStorage.removeItem('oauth.tokens');
}

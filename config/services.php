<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    | Central place for credentials + options used across the app.
    | NOTE: Do NOT use url()/route()/asset() here—CLI has no HTTP request.
    */

    // --- Mail providers (leave if unused) ---
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // --- Slack notifications (optional) ---
    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payments (Zeno)
    |--------------------------------------------------------------------------
    | webhook_url MUST be a plain string (absolute or relative).
    | If you set a relative path, convert to absolute at runtime with URL::to($path).
    */
    'zeno' => [
        // Support both new and old env names
        'base'            => env('ZENO_BASE_URL', env('ZENO_API_BASE', 'https://zenoapi.com/api')),
        'key'             => env('ZENO_API_KEY'),
        'webhook_secret'  => env('ZENO_WEBHOOK_SECRET'),
        'webhook_url'     => env('ZENO_WEBHOOK_URL', '/webhooks/zeno'),
        'currency'        => env('APP_CURRENCY', 'TZS'),
        // HTTP client options
        'timeout'         => (int) env('ZENO_TIMEOUT', 30),
        'connect_timeout' => (int) env('ZENO_CONNECT_TIMEOUT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webuzo API (Autoprovision + Enduser)
    |--------------------------------------------------------------------------
    | Keys align with usage in Jobs/Controllers:
    |   config('services.webuzo.api_url')
    |   config('services.webuzo.enduser_url')
    |   config('services.webuzo.create_path')
    |   config('services.webuzo.default_ip'), ns1, ns2, default_package, plan_map
    |
    | Auth modes:
    |   - key   => sends header: <key_header>: "<key_prefix> <api_key>"
    |   - basic => uses admin_user/admin_pass
    |
    | ENV fallback strategy: prefer SERVICES_WEBUZO_* then fallback to legacy WEBUZO_*
    */
    'webuzo' => [
        // Base URLs
        'api_url'     => env('SERVICES_WEBUZO_API_URL', env('WEBUZO_API_URL')), // e.g. https://X.X.X.X:2005
        'enduser_url' => env('SERVICES_WEBUZO_ENDUSER_URL', env('WEBUZO_ENDUSER_URL', 'https://example.com:2003')),

        // IMPORTANT: correct action is "adduser" (no underscore)
        'create_path' => env('SERVICES_WEBUZO_CREATE_USER_PATH', env('WEBUZO_CREATE_USER_PATH', '/index.php?api=json&act=adduser')),

        // Auth mode & credentials
        'auth'        => env('SERVICES_WEBUZO_AUTH', env('WEBUZO_AUTH', 'basic')), // 'key' | 'basic'
        'api_key'     => env('SERVICES_WEBUZO_API_KEY', env('WEBUZO_API_KEY')),
        'key_header'  => env('SERVICES_WEBUZO_KEY_HEADER', env('WEBUZO_KEY_HEADER', 'Authorization')),
        'key_prefix'  => env('SERVICES_WEBUZO_KEY_PREFIX', env('WEBUZO_KEY_PREFIX', 'Bearer')),

        // Basic auth
        'admin_user'  => env('SERVICES_WEBUZO_ADMIN_USER', env('WEBUZO_ADMIN_USER')),
        'admin_pass'  => env('SERVICES_WEBUZO_ADMIN_PASS', env('WEBUZO_ADMIN_PASS')),

        // Server defaults for new accounts
        'default_ip'      => env('SERVICES_WEBUZO_DEFAULT_IP', env('WEBUZO_DEFAULT_IP')),
        'ns1'             => env('SERVICES_WEBUZO_NS1', env('WEBUZO_NS1')),
        'ns2'             => env('SERVICES_WEBUZO_NS2', env('WEBUZO_NS2')),
        'default_package' => env('SERVICES_WEBUZO_DEFAULT_PACKAGE', env('WEBUZO_DEFAULT_PACKAGE', 'Hollyn Lite')),

        // Map: app plan slug => Webuzo package name
        // Example ENV: WEBUZO_PLAN_MAP="hollyn-boost:Hollyn Boost,hollyn-lite:Hollyn Lite,hollyn-max:Hollyn Max,hollyn-grow:Hollyn Grow"
        'plan_map' => (function () {
            $raw = env('WEBUZO_PLAN_MAP', '');
            $map = [];
            foreach (array_filter(array_map('trim', explode(',', $raw))) as $pair) {
                [$app, $wb] = array_map('trim', array_pad(explode(':', $pair, 2), 2, ''));
                if ($app && $wb) $map[$app] = $wb;
            }
            return $map;
        })(),

        // HTTP client options
        'timeout'         => (int) env('SERVICES_WEBUZO_TIMEOUT', (int) env('WEBUZO_TIMEOUT', 90)),
        'connect_timeout' => (int) env('SERVICES_WEBUZO_CONNECT_TIMEOUT', (int) env('WEBUZO_CONNECT_TIMEOUT', 10)),

        // SSL verify (true on production with valid certs)
        'verify_ssl'      => (bool) env('SERVICES_WEBUZO_VERIFY_SSL', (bool) env('WEBUZO_VERIFY_SSL', true)),

        // Optional SSO switch (UI may read this)
        'sso_enabled'     => (bool) env('WEBUZO_SSO_ENABLED', false),

        // Legacy switch kept for backward compat (prefer 'auth' above)
        'use_basic_auth'  => (bool) env('WEBUZO_USE_BASIC_AUTH', env('WEBUZO_USE_BASIC', false)),
    ],

];

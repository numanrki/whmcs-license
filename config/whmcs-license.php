<?php

return [
    'verify_url' => env('LICENSE_SERVER_VERIFY_URL', ''),
    'base_url' => env('LICENSE_SERVER_URL', ''),
    'secret_key' => env('LICENSE_SERVER_SECRET_KEY', ''),
    'allow_reissue' => env('LICENSE_ALLOW_REISSUE', false),
    'allow_domain_conflict' => env('LICENSE_ALLOW_DOMAIN_CONFLICT', false),
    'allow_ip_conflict' => env('LICENSE_ALLOW_IP_CONFLICT', false),
    'allow_directory_conflict' => env('LICENSE_ALLOW_DIRECTORY_CONFLICT', false),
    'admin_grace_days' => env('LICENSE_ADMIN_GRACE_DAYS', 10),
    'force_check_cooldown' => env('LICENSE_FORCE_CHECK_COOLDOWN', 60),
    'timeout' => env('LICENSE_TIMEOUT', 10),
    'retries' => env('LICENSE_RETRIES', 2),
    'retry_sleep_ms' => env('LICENSE_RETRY_SLEEP_MS', 300),
    'cache_ttl' => env('LICENSE_CACHE_TTL', 3600),
    'enable_route' => env('LICENSE_ENABLE_ROUTE', true),
];
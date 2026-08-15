<?php
declare(strict_types=1);

header('Content-Type: application/json');

$keys = [
    'VERCEL',
    'VERCEL_ENV',
    'VERCEL_REGION',
    'DB_HOST',
    'DB_NAME',
    'DB_USER',
    'SUPABASE_URL',
    'SUPABASE_SERVICE_ROLE_KEY',
    'SUPABASE_STORAGE_BUCKET',
];

$result = [];

foreach ($keys as $key) {
    $value = getenv($key);

    $result[$key] = [
        'getenv_exists' => $value !== false && $value !== '',
        'env_exists'   => isset($_ENV[$key]) && $_ENV[$key] !== '',
        'server_exists' => isset($_SERVER[$key]) && $_SERVER[$key] !== '',
    ];
}

echo json_encode($result, JSON_PRETTY_PRINT);
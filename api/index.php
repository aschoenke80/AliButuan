<?php

/**
 * Vercel entry point for AliButuan Laravel app.
 *
 * Vercel's filesystem is read-only except /tmp. We:
 *  1. Create required writable dirs in /tmp
 *  2. Copy the seeded SQLite DB to /tmp on first request
 *  3. Point Laravel's storage path at /tmp/storage
 */

// ── 1. Create writable storage dirs in /tmp ──────────────────────────────────
$tmpStorage = '/tmp/laravel/storage';
foreach ([
    "$tmpStorage/framework/cache/data",
    "$tmpStorage/framework/sessions",
    "$tmpStorage/framework/views",
    "$tmpStorage/framework/testing",
    "$tmpStorage/logs",
    "$tmpStorage/app/public",
    '/tmp/laravel/bootstrap/cache',
] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// ── 2. Copy seeded SQLite DB to /tmp (writable) ──────────────────────────────
$dbSource = __DIR__ . '/../database/database.sqlite';
$dbDest   = '/tmp/database.sqlite';
if (!file_exists($dbDest) && file_exists($dbSource)) {
    copy($dbSource, $dbDest);
}

// ── 3. Override env vars before Laravel boots ────────────────────────────────
$overrides = [
    'DB_DATABASE'    => $dbDest,
    'CACHE_DRIVER'   => 'array',
    'SESSION_DRIVER' => 'cookie',
    'LOG_CHANNEL'    => 'stderr',
    'STORAGE_PATH'   => $tmpStorage,
];
foreach ($overrides as $key => $value) {
    $_ENV[$key]    = $value;
    $_SERVER[$key] = $value;
    putenv("$key=$value");
}

// ── 4. Boot Laravel ──────────────────────────────────────────────────────────
require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Http\Request;

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Redirect storage and bootstrap/cache to writable /tmp paths
$app->useStoragePath($tmpStorage);
$app->useBootstrapPath('/tmp/laravel/bootstrap');

$app->handleRequest(Request::capture());

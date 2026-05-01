<?php

/**
 * Vercel entry point for AliButuan Laravel app.
 *
 * Vercel's filesystem is read-only except for /tmp.
 * We copy the pre-seeded SQLite database to /tmp so the app can read it.
 */

$dbSource = __DIR__ . '/../database/database.sqlite';
$dbDest   = '/tmp/database.sqlite';

if (!file_exists($dbDest) && file_exists($dbSource)) {
    copy($dbSource, $dbDest);
}

// Point Laravel at the writable copy
$_ENV['DB_DATABASE']    = $dbDest;
$_SERVER['DB_DATABASE'] = $dbDest;
putenv("DB_DATABASE=$dbDest");

require __DIR__ . '/../public/index.php';

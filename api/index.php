<?php

// 1. Move to the project root
chdir(__DIR__ . '/..');

// 2. Point all Laravel internal caches to /tmp (the only writable directory on Vercel)
$writableDir = '/tmp';

putenv("APP_PACKAGES_CACHE={$writableDir}/packages.php");
putenv("APP_SERVICES_CACHE={$writableDir}/services.php");
putenv("APP_CONFIG_CACHE={$writableDir}/config.php");
putenv("APP_ROUTES_CACHE={$writableDir}/routes-v7.php");
putenv("APP_EVENTS_CACHE={$writableDir}/events.php");

// 3. Load the standard Laravel entry point
require __DIR__ . '/../public/index.php';
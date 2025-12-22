<?php

// 1. Move to the project root
chdir(__DIR__ . '/..');

// 2. Setup the writable directory for Serverless
$writableDir = '/tmp';

// Ensure the necessary temporary directories exist
if (!is_dir($writableDir . '/views')) {
    mkdir($writableDir . '/views', 0755, true);
}
if (!is_dir($writableDir . '/cache')) {
    mkdir($writableDir . '/cache', 0755, true);
}

// 3. Override Laravel paths and drivers via Environment Variables

// Point system caches to /tmp
putenv("APP_PACKAGES_CACHE={$writableDir}/packages.php");
putenv("APP_SERVICES_CACHE={$writableDir}/services.php");
putenv("APP_CONFIG_CACHE={$writableDir}/config.php");
putenv("APP_ROUTES_CACHE={$writableDir}/routes-v7.php");
putenv("APP_EVENTS_CACHE={$writableDir}/events.php");

// Point compiled views to /tmp
putenv("VIEW_COMPILED_PATH={$writableDir}/views");

// FORCE drivers that do not write to disk
// Crucial: Default 'file' drivers will crash Vercel.
putenv("SESSION_DRIVER=cookie"); 
putenv("LOG_CHANNEL=stderr");    
putenv("CACHE_STORE=array");     

// 4. Load the standard Laravel entry point
require __DIR__ . '/../public/index.php';
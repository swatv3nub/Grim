<?php

/**
 * Bootstrap file for GRIM Security Scanner tests
 */

// Set error reporting for tests
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('UTC');

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

// Set test environment
$_ENV['APP_ENV'] = 'testing';
$_ENV['LOG_LEVEL'] = 'DEBUG';

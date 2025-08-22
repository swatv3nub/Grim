#!/usr/bin/env php
<?php

/**
 * GRIM Security Scanner v3.0.0
 * Advanced Information Gathering and Vulnerability Scanning Tool
 * 
 * @author GRIM Security Team
 * @license GPL-3.0
 */

// Check if running from CLI
if (php_sapi_name() !== 'cli') {
    die('This tool is designed to run from the command line only.');
}

// Autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('UTC');

// Set unlimited execution time for long scans
set_time_limit(0);

// Memory limit for large scans
ini_set('memory_limit', '512M');

try {
    // Initialize the application
    $app = new \Grim\GrimApplication();
    
    // Run the application
    exit($app->run());
    
} catch (\Throwable $e) {
    // Fatal error handling
    echo "\n\033[31m[FATAL ERROR] " . $e->getMessage() . "\033[0m\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    
    // Log the error if logger is available
    if (class_exists('\Grim\Utils\Logger')) {
        try {
            $logger = \Grim\Utils\Logger::getInstance();
            $logger->critical('Fatal error in GRIM application', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } catch (\Throwable $logError) {
            // If logging fails, just continue
        }
    }
    
    exit(1);
}

<?php
/**
 * Dashboard Wrapper
 * Handles authentication and loads the dashboard
 */

// Enable error reporting for debugging
// Error display controlled by config.php DEBUG_MODE



// Load auth (which loads config, database, Logger)
require_once __DIR__ . '/auth.php';

// Require authentication
try {
    Auth::requireAuth();
} catch (Exception $e) {
    die('<h3>Authentication Error: ' . htmlspecialchars($e->getMessage()) . '</h3>');
}

// Include the actual dashboard
try {
    require_once __DIR__ . '/dashboard.php';
} catch (Exception $e) {
    die('<h3>Dashboard Error: ' . htmlspecialchars($e->getMessage()) . '</h3>');
}

<?php
/**
 * Wrapper file for consolidated management system
 * Auto-generated to fix missing file issues
 */

// Set the type parameter based on the filename
$type = basename(__FILE__, '.php');

// Map file names to types
$typeMap = [
    'drivers' => 'drivers',
    'vehicles' => 'vehicles',
    'tour-guides' => 'tour_guides',
    'users' => 'users',
    'Vcdashboard' => 'dashboard'
];

if (isset($typeMap[$type])) {
    $_GET['type'] = $typeMap[$type];
}

// Include the consolidated management file
require_once __DIR__ . '/consolidated-management.php';

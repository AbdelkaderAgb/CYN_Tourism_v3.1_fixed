<?php
/**
 * CYN Tourism - Application Bootstrap
 * 
 * This file initializes the application by:
 * - Setting up paths
 * - Loading configuration
 * - Registering autoloader
 * - Starting session
 * 
 * @package CYN_Tourism
 * @version 3.0.0
 */

// Define application root paths
define('APP_ROOT', dirname(__DIR__));
define('APP_PATH', APP_ROOT . '/app');
define('CONFIG_PATH', APP_ROOT . '/config');
define('PUBLIC_PATH', APP_ROOT . '/public');
define('STORAGE_PATH', APP_ROOT . '/storage');

// Load configuration
require_once CONFIG_PATH . '/config.php';

// Register autoloader for new structure
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $classPath = str_replace('\\', '/', $class);
    
    // Try app directory first
    $file = APP_PATH . '/' . $classPath . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }
    
    // Try legacy flat structure (backward compatibility)
    $file = APP_ROOT . '/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }
});

// Load core services
require_once APP_PATH . '/Models/Database.php';
require_once APP_PATH . '/Services/Auth.php';
require_once APP_PATH . '/Services/Logger.php';

// Load legacy functions for backward compatibility
if (file_exists(APP_ROOT . '/functions.php')) {
    require_once APP_ROOT . '/functions.php';
}

// Load language support
if (file_exists(APP_ROOT . '/language.php')) {
    require_once APP_ROOT . '/language.php';
}

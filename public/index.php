<?php
/**
 * CYN Tourism - Public Entry Point (MVC Router)
 * 
 * This serves as the front-controller for the new MVC architecture.
 * On shared hosting, configure .htaccess to route all requests here,
 * or use this alongside the legacy files for gradual migration.
 *
 * @package CYN_Tourism
 * @version 3.1.0
 * @requires PHP 8.2+
 */

declare(strict_types=1);

// Define application root
define('APP_ROOT', dirname(__DIR__));

// Load configuration and core dependencies
require_once APP_ROOT . '/config.php';
require_once APP_ROOT . '/database.php';
require_once APP_ROOT . '/Logger.php';
require_once APP_ROOT . '/auth.php';
require_once APP_ROOT . '/language.php';
require_once APP_ROOT . '/functions.php';

// Autoload src/ classes (PSR-4 style: CYN\ -> src/)
spl_autoload_register(function (string $class): void {
    $prefix = 'CYN\\';
    $baseDir = APP_ROOT . '/src/';

    if (str_starts_with($class, $prefix)) {
        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// Initialize Router
$router = new CYN\Router();

// Register routes
$router->get('/dashboard', CYN\Controllers\DashboardController::class, 'index');

// Dispatch the current request
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($uri, $method);

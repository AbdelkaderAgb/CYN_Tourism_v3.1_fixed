<?php
/**
 * CYN Tourism - Logger Class
 * Simple logging utility for the application
 * 
 * @package CYN_Tourism
 * @version 2.0.0
 */

class Logger {
    
    /** @var string Log directory path */
    private static $logDir = null;
    
    /** @var bool Whether logging is enabled */
    private static $enabled = true;
    
    /**
     * Initialize logger
     */
    private static function init() {
        // Set log directory using STORAGE_PATH or APP_ROOT if defined
        if (self::$logDir === null) {
            if (defined('STORAGE_PATH')) {
                self::$logDir = STORAGE_PATH . '/logs/';
            } elseif (defined('APP_ROOT')) {
                self::$logDir = APP_ROOT . '/logs/';
            } else {
                self::$logDir = dirname(__DIR__, 2) . '/logs/';
            }
        }
        
        if (!is_dir(self::$logDir)) {
            @mkdir(self::$logDir, 0755, true);
        }
    }
    
    /**
     * Write log entry
     * 
     * @param string $level Log level (error, warning, info, debug)
     * @param string $message Log message
     * @param array $context Additional context data
     */
    private static function write($level, $message, $context = []) {
        if (!self::$enabled) {
            return;
        }
        
        self::init();
        
        $logFile = self::$logDir . date('Y-m-d') . '.log';
        
        $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        
        $logEntry = sprintf(
            "[%s] [%s] %s%s | IP: %s | URL: %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            $contextStr,
            $_SERVER['REMOTE_ADDR'] ?? 'CLI',
            $_SERVER['REQUEST_URI'] ?? 'N/A'
        );
        
        @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log error message
     * 
     * @param string $message Error message
     * @param array $context Additional context
     */
    public static function error($message, $context = []) {
        self::write('error', $message, $context);
    }
    
    /**
     * Log warning message
     * 
     * @param string $message Warning message
     * @param array $context Additional context
     */
    public static function warning($message, $context = []) {
        self::write('warning', $message, $context);
    }
    
    /**
     * Log info message
     * 
     * @param string $message Info message
     * @param array $context Additional context
     */
    public static function info($message, $context = []) {
        self::write('info', $message, $context);
    }
    
    /**
     * Log debug message
     * 
     * @param string $message Debug message
     * @param array $context Additional context
     */
    public static function debug($message, $context = []) {
        self::write('debug', $message, $context);
    }
    
    /**
     * Log security-related event
     * 
     * @param string $event Security event type
     * @param array $context Additional context
     */
    public static function security($event, $context = []) {
        self::write('security', 'Security Event: ' . $event, $context);
    }
    
    /**
     * Log authentication event
     * 
     * @param string $type Event type (login, logout, failed)
     * @param int|null $userId User ID
     * @param array $context Additional context
     */
    public static function auth($type, $userId = null, $context = []) {
        if ($userId !== null) {
            $context['user_id'] = $userId;
        }
        self::write('auth', 'Auth: ' . $type, $context);
    }
    
    /**
     * Log user activity
     * 
     * @param string $action Action performed
     * @param string $resource Resource type
     * @param int|null $resourceId Resource ID
     * @param array $context Additional context
     */
    public static function activity($action, $resource, $resourceId = null, $context = []) {
        $message = sprintf('Activity: %s %s', $action, $resource);
        if ($resourceId !== null) {
            $context['resource_id'] = $resourceId;
        }
        self::write('activity', $message, $context);
    }
    
    /**
     * Set logging enabled/disabled
     * 
     * @param bool $enabled Whether logging is enabled
     */
    public static function setEnabled($enabled) {
        self::$enabled = (bool) $enabled;
    }
    
    /**
     * Set log directory
     * 
     * @param string $dir Directory path
     */
    public static function setLogDir($dir) {
        self::$logDir = rtrim($dir, '/') . '/';
    }
}

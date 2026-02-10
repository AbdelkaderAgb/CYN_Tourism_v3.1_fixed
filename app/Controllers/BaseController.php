<?php
/**
 * CYN Tourism - Base Controller Class
 * 
 * Provides common functionality for all controllers.
 * Implements separation of concerns between presentation and business logic.
 * 
 * @package CYN_Tourism
 * @version 3.0.0
 */

abstract class BaseController {
    
    /** @var array View data */
    protected $data = [];
    
    /**
     * Render a view
     * 
     * @param string $view View file name (without .php)
     * @param array $data Data to pass to view
     */
    protected function view($view, $data = []) {
        // Merge with controller data
        $data = array_merge($this->data, $data);
        
        // Extract data to variables
        extract($data);
        
        // Try new views directory first
        $viewFile = dirname(__DIR__) . '/Views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            // Fallback to root directory for backward compatibility
            $viewFile = dirname(__DIR__, 2) . '/' . $view . '.php';
        }
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            throw new Exception("View not found: $view");
        }
    }
    
    /**
     * Return JSON response
     * 
     * @param mixed $data Response data
     * @param int $status HTTP status code
     */
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Redirect to URL
     * 
     * @param string $url Target URL
     */
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    /**
     * Get POST data
     * 
     * @param string $key Field key
     * @param mixed $default Default value
     * @return mixed Field value
     */
    protected function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Get GET data
     * 
     * @param string $key Field key
     * @param mixed $default Default value
     * @return mixed Field value
     */
    protected function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Validate CSRF token
     * 
     * @return bool Valid or not
     */
    protected function validateCsrf() {
        $token = $this->post('csrf_token') ?? $this->get('csrf_token');
        return isset($_SESSION['csrf_token']) && $token === $_SESSION['csrf_token'];
    }
    
    /**
     * Generate CSRF token
     * 
     * @return string CSRF token
     */
    protected function generateCsrf() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Check if user is authenticated
     * 
     * @return bool Authenticated or not
     */
    protected function isAuthenticated() {
        if (!class_exists('Auth')) {
            require_once dirname(__DIR__) . '/Services/Auth.php';
        }
        return Auth::check();
    }
    
    /**
     * Require authentication
     */
    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            $this->redirect('/login.php');
        }
    }
    
    /**
     * Get current user
     * 
     * @return array|null User data
     */
    protected function user() {
        if (!class_exists('Auth')) {
            require_once dirname(__DIR__) . '/Services/Auth.php';
        }
        return Auth::user();
    }
}

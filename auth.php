<?php
/**
 * CYN Tourism - Authentication System
 * Secure authentication with session management and CSRF protection
 *
 * @package CYN_Tourism
 * @version 2.0.0
 */

// Load configuration (defines constants, starts session)
if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__);
}
require_once __DIR__ . '/config.php';

// Load real dependencies (Database and Logger)
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/Logger.php';

// Minimal translation function if language.php not yet loaded
if (!function_exists('__')) {
    function __($key, $params = [], $default = null) {
        $map = [
            'field_required' => 'All fields are required',
            'account_locked' => 'Account locked due to too many failed attempts. Please try again later.',
            'login_failed' => 'Invalid email or password.',
            'error_occurred' => 'An error occurred. Please try again.',
        ];
        return $map[$key] ?? ($default !== null ? $default : $key);
    }
}

// --- AUTH CLASS ---

if (!class_exists('Auth')) {
/**
 * Authentication class
 */
class Auth {
    
    private static $currentUser = null;
    private static $checked = false;
    
    /**
     * Check if user is authenticated
     *
     * @return bool True if authenticated
     */
    public static function check() {
        // Return cached result
        if (self::$checked && self::$currentUser !== null) {
            return true;
        }
        
        // Check session variables
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth_time'])) {
            return false;
        }
        
        // Check session expiration
        if (time() - $_SESSION['auth_time'] > SESSION_LIFETIME) {
            self::logout();
            return false;
        }
        
        // Update last activity
        $_SESSION['auth_time'] = time();
        
        // Load user from database
        try {
            $user = Database::getInstance()->fetchOne(
                "SELECT * FROM users WHERE id = ? AND status = 'active'",
                [$_SESSION['user_id']]
            );
            
            if (!$user) {
                self::logout();
                return false;
            }
            
            self::$currentUser = $user;
            self::$checked = true;
            
            return true;
            
        } catch (Exception $e) {
            Logger::error('Auth check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Get current user
     *
     * @return array|null User data or null
     */
    public static function user() {
        if (!self::check()) {
            return null;
        }
        return self::$currentUser;
    }
    
    /**
     * Get current user ID
     *
     * @return int|null User ID or null
     */
    public static function id() {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }
    
    /**
     * Get current user role
     *
     * @return string|null User role or null
     */
    public static function role() {
        $user = self::user();
        return $user ? $user['role'] : null;
    }
    
    /**
     * Check if user is admin
     *
     * @return bool True if admin
     */
    public static function isAdmin() {
        return self::role() === 'admin';
    }
    
    /**
     * Check if user is manager
     *
     * @return bool True if manager
     */
    public static function isManager() {
        return in_array(self::role(), ['admin', 'manager']);
    }
    
    /**
     * Login user
     *
     * @param string $email User email
     * @param string $password User password
     * @return array Login result
     */
    public static function login($email, $password) {
        // Validate input
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => __('field_required')];
        }
        
        // Check for brute force
        if (self::isLockedOut($email)) {
            Logger::security('Login attempt during lockout', ['email' => $email]);
            return ['success' => false, 'message' => __('account_locked')];
        }
        
        try {
            // Get user by email
            $user = Database::getInstance()->fetchOne(
                "SELECT * FROM users WHERE email = ?",
                [$email]
            );
            
            // Check if user exists
        if (!$user) {
            self::recordFailedAttempt($email);
            Logger::auth('failed', null, ['email' => $email, 'reason' => 'user_not_found']);
            return ['success' => false, 'message' => __('login_failed')];
        }

        // Verify password (support both hash and legacy plain text)
        $passwordValid = false;
        $needsRehash = false;

        if (password_verify($password, $user['password'])) {
            $passwordValid = true;
            if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                $needsRehash = true;
            }
        } elseif ($password === $user['password']) {
            // Fallback for plain text (legacy)
            $passwordValid = true;
            $needsRehash = true;
        }

        if (!$passwordValid) {
            self::recordFailedAttempt($email);
            Logger::auth('failed', null, ['email' => $email, 'reason' => 'invalid_password']);
            return ['success' => false, 'message' => __('login_failed')];
        }

        // Update password hash if needed
        if ($needsRehash) {
            Database::getInstance()->query(
                "UPDATE users SET password = ? WHERE id = ?",
                [password_hash($password, PASSWORD_DEFAULT), $user['id']]
            );
        }
            
            // Check if user is active
            if ($user['status'] !== 'active') {
                Logger::auth('failed', $user['id'], ['reason' => 'inactive_account']);
                return ['success' => false, 'message' => 'Account is not active'];
            }
            
            // Clear failed attempts
            self::clearFailedAttempts($email);
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['auth_time'] = time();
            $_SESSION['user_role'] = $user['role'];
            
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            // Update last login (wrapped in try-catch so login doesn't fail if column is missing)
            try {
                Database::getInstance()->query(
                    "UPDATE users SET last_login = NOW() WHERE id = ?",
                    [$user['id']]
                );
            } catch (Exception $e) {
                // Non-critical: ignore if last_login or login_count column doesn't exist
            }
            
            self::$currentUser = $user;
            self::$checked = true;
            
            // Log success
            Logger::auth('login', $user['id']);
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => $user
            ];
            
        } catch (Exception $e) {
            Logger::error('Login error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => __('error_occurred')];
        }
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        $userId = self::id();
        
        // Log logout
        if ($userId) {
            Logger::auth('logout', $userId);
        }
        
        // Clear session
        $_SESSION = [];
        
        // Destroy session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => defined('SECURE_COOKIES') ? SECURE_COOKIES : false,
                'httponly' => defined('HTTP_ONLY_COOKIES') ? HTTP_ONLY_COOKIES : true,
                'samesite' => 'Strict'
            ]);
        }
        
        session_destroy();
        
        // Restart session for flash messages etc.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        self::$currentUser = null;
        self::$checked = false;
    }
    
    /**
     * Require authentication
     * Redirects to login if not authenticated
     */
    public static function requireAuth() {
        if (!self::check()) {
            // Store intended URL
            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? '';
            }
            
            header('Location: login.php');
            exit;
        }
    }
    
    /**
     * Require admin role
     */
    public static function requireAdmin() {
        self::requireAuth();
        if (!self::isAdmin()) {
            header('HTTP/1.1 403 Forbidden');
            include __DIR__ . '/403.php';
            exit;
        }
    }
    
    /**
     * Require manager role
     */
    public static function requireManager() {
        self::requireAuth();
        if (!self::isManager()) {
            header('HTTP/1.1 403 Forbidden');
            include __DIR__ . '/403.php';
            exit;
        }
    }

    /**
     * Check if user is locked out
     */
    private static function isLockedOut($email) {
        $key = 'login_attempts_' . md5($email);
        
        if (!isset($_SESSION[$key])) {
            return false;
        }
        
        $attempts = $_SESSION[$key];
        
        // Check if max attempts reached and lockout period not expired
        if (count($attempts) >= MAX_LOGIN_ATTEMPTS) {
            $lastAttempt = end($attempts);
            if (time() - $lastAttempt < LOCKOUT_DURATION) {
                return true;
            }
            
            // Clear old attempts
            self::clearFailedAttempts($email);
        }
        
        return false;
    }
    
    /**
     * Record failed login attempt
     */
    private static function recordFailedAttempt($email) {
        $key = 'login_attempts_' . md5($email);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        
        $_SESSION[$key][] = time();
        
        // Keep only recent attempts
        $_SESSION[$key] = array_filter($_SESSION[$key], function($time) {
            return time() - $time < LOCKOUT_DURATION;
        });
    }
    
    /**
     * Clear failed login attempts
     */
    private static function clearFailedAttempts($email) {
        $key = 'login_attempts_' . md5($email);
        unset($_SESSION[$key]);
    }
}
} // end if (!class_exists('Auth'))

/**
 * CSRF Protection class
 */
if (!class_exists('CSRF')) {
class CSRF {
    public static function token() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
    
    public static function validate($token = null) {
        $token = $token ?? ($_POST[CSRF_TOKEN_NAME] ?? '');
        $storedToken = $_SESSION[CSRF_TOKEN_NAME] ?? '';
        if (empty($token) || empty($storedToken)) return false;
        return hash_equals($storedToken, $token);
    }
    
    public static function field() {
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . self::token() . '">';
    }
}
}

/**
 * Input sanitization class
 */
if (!class_exists('Input')) {
class Input {
    public static function string($input) {
        if (is_array($input)) return array_map([self::class, 'string'], $input);
        return htmlspecialchars(trim($input ?? ''), ENT_QUOTES, 'UTF-8');
    }
    
    public static function email($email) {
        return filter_var(trim($email ?? ''), FILTER_SANITIZE_EMAIL);
    }
}
}

// --- HELPER FUNCTIONS ---

if (!function_exists('flash')) {
    function flash($key, $value = null) {
        if ($value !== null) {
            if (!isset($_SESSION['flash'])) {
                $_SESSION['flash'] = [];
            }
            $_SESSION['flash'][$key] = $value;
            return null;
        }
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
}

if (!function_exists('redirect')) {
    function redirect($url, $message = '', $type = 'success') {
        if ($message) {
            flash($type, $message);
        }
        header("Location: {$url}");
        exit;
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        return CSRF::field();
    }
}

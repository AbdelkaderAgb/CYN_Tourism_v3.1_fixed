<?php
/**
 * CYN Tourism - Login Page
 * Provides the login form and handles authentication
 * 
 * @package CYN_Tourism
 * @version 2.0.0
 */

// Load configuration first (defines APP_ROOT, starts session, etc.)
if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__);
}
require_once __DIR__ . '/config.php';

// Load the real database BEFORE auth (so the mock doesn't take over)
require_once __DIR__ . '/database.php';

// Load Logger
require_once __DIR__ . '/Logger.php';

// Load language support
require_once __DIR__ . '/language.php';

// Now load auth (it will skip mock classes since real ones are already loaded)
require_once __DIR__ . '/auth.php';

// If already logged in, redirect to dashboard
if (Auth::check()) {
    header('Location: Vcdashboard.php');
    exit;
}

// Handle login form submission
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate CSRF
    if (!CSRF::validate()) {
        $error = __('invalid_csrf', [], 'Invalid security token. Please try again.');
    } else {
        $result = Auth::login($email, $password);
        
        if ($result['success']) {
            // Redirect to intended URL or dashboard
            $intended = $_SESSION['intended_url'] ?? 'Vcdashboard.php';
            unset($_SESSION['intended_url']);
            header('Location: ' . $intended);
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

$currentLang = getCurrentLang();
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('login', [], 'Login'); ?> - <?php echo COMPANY_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 50%, #c7d2fe 100%);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--space-4);
        }
        .login-container { width: 100%; max-width: 420px; }
        .login-logo {
            width: 72px; height: 72px;
            background: var(--primary-gradient);
            border-radius: var(--radius-lg);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto var(--space-4); color: #fff; font-size: 28px;
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.25);
        }
        .login-header { text-align: center; margin-bottom: var(--space-6); }
        @media (max-width: 480px) {
            .login-container .card { padding: var(--space-5) !important; }
            .login-logo { width: 56px; height: 56px; font-size: 22px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card" style="padding: var(--space-8);">
            <div class="login-header">
                <div class="login-logo" style="background: transparent; box-shadow: none;">
                    <img src="logo.png" alt="<?php echo COMPANY_NAME; ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
                <h1 style="font-size: var(--text-2xl); font-weight: 700; color: var(--text-primary); margin-bottom: 4px;"><?php echo COMPANY_NAME; ?></h1>
                <p style="color: var(--text-secondary); font-size: var(--text-sm);"><?php echo __('login_subtitle', [], 'Tourism Management System'); ?></p>
            </div>
            
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php<?php echo isset($_GET['lang']) ? '?lang=' . htmlspecialchars($_GET['lang']) : ''; ?>" id="loginForm">
                <?php echo CSRF::field(); ?>
                
                <div class="form-group mb-4">
                    <label class="form-label" for="email"><?php echo __('email', [], 'Email'); ?></label>
                    <div class="input-icon-wrapper" style="position:relative;">
                        <i class="fas fa-envelope" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-tertiary);"></i>
                        <input type="email" id="email" name="email" class="form-control" style="padding-left:42px;"
                               placeholder="name@company.com" 
                               value="<?php echo htmlspecialchars($email); ?>" 
                               required autocomplete="email" autofocus>
                    </div>
                </div>
                
                <div class="form-group mb-6">
                    <label class="form-label" for="password"><?php echo __('password', [], 'Password'); ?></label>
                    <div class="input-icon-wrapper" style="position:relative;">
                        <i class="fas fa-lock" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-tertiary);"></i>
                        <input type="password" id="password" name="password" class="form-control" style="padding-left:42px;"
                               placeholder="••••••••" 
                               required autocomplete="current-password">
                        <button type="button" class="btn-icon" onclick="togglePassword()" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--text-tertiary);">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-full" id="loginBtn" style="width:100%; justify-content:center; padding:12px;">
                    <i class="fas fa-sign-in-alt"></i> <?php echo __('login', [], 'Login'); ?>
                </button>
            </form>
            
            <div class="auth-language-switcher">
                <?php
                $params = $_GET;
                unset($params['lang']);
                $qs = !empty($params) ? '&' . http_build_query($params) : '';
                ?>
                <a href="?lang=tr<?php echo $qs; ?>" class="btn btn-sm <?php echo $currentLang === 'tr' ? 'btn-primary' : 'btn-secondary'; ?>">TR</a>
                <a href="?lang=en<?php echo $qs; ?>" class="btn btn-sm <?php echo $currentLang === 'en' ? 'btn-primary' : 'btn-secondary'; ?>">EN</a>
            </div>
        </div>
        
        <p class="text-center mt-6" style="color: var(--text-tertiary); font-size: var(--text-xs);">
            &copy; <?php echo date('Y'); ?> <?php echo COMPANY_NAME; ?><br>
            <span style="opacity: 0.8;">TURSAB License No: 11738</span>
        </p>
    </div>
    
    <script>
    function togglePassword() {
        var pwd = document.getElementById('password');
        var icon = document.getElementById('toggleIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    document.getElementById('loginForm').addEventListener('submit', function() {
        var btn = document.getElementById('loginBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';
    });
    </script>
</body>
</html>

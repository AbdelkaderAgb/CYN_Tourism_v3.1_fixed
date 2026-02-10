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
<html lang="<?php echo $currentLang; ?>" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('login', [], 'Login'); ?> - <?php echo COMPANY_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                colors: {
                    primary: { 50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81' },
                }
            }
        }
    }
    </script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="h-full bg-gradient-to-br from-primary-50 via-primary-100 to-primary-200 font-sans flex items-center justify-center p-4"
      x-data="{ showPassword: false, submitting: false }">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-10">
            <!-- Logo & Header -->
            <div class="text-center mb-8">
                <div class="mx-auto w-[72px] h-[72px] rounded-xl flex items-center justify-center mb-4">
                    <img src="logo.png" alt="<?php echo COMPANY_NAME; ?>" class="max-w-full max-h-full object-contain">
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1"><?php echo COMPANY_NAME; ?></h1>
                <p class="text-sm text-gray-500"><?php echo __('login_subtitle', [], 'Tourism Management System'); ?></p>
            </div>

            <?php if (!empty($error)): ?>
            <div class="flex items-center gap-3 px-4 py-3 mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" action="login.php<?php echo isset($_GET['lang']) ? '?lang=' . htmlspecialchars($_GET['lang']) : ''; ?>"
                  id="loginForm" @submit="submitting = true">
                <?php echo CSRF::field(); ?>

                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5"><?php echo __('email', [], 'Email'); ?></label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="email" id="email" name="email"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-shadow"
                               placeholder="name@company.com"
                               value="<?php echo htmlspecialchars($email); ?>"
                               required autocomplete="email" autofocus>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5"><?php echo __('password', [], 'Password'); ?></label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                               class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-shadow"
                               placeholder="••••••••"
                               required autocomplete="current-password">
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="loginBtn"
                        :disabled="submitting"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition-colors disabled:opacity-60 disabled:cursor-not-allowed shadow-sm">
                    <template x-if="!submitting">
                        <span class="flex items-center gap-2"><i class="fas fa-sign-in-alt"></i> <?php echo __('login', [], 'Login'); ?></span>
                    </template>
                    <template x-if="submitting">
                        <span class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> ...</span>
                    </template>
                </button>
            </form>

            <!-- Language Switcher -->
            <div class="flex items-center justify-center gap-2 mt-6">
                <?php
                $params = $_GET;
                unset($params['lang']);
                $qs = !empty($params) ? '&' . http_build_query($params) : '';
                ?>
                <a href="?lang=tr<?php echo $qs; ?>"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors <?php echo $currentLang === 'tr' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">TR</a>
                <a href="?lang=en<?php echo $qs; ?>"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors <?php echo $currentLang === 'en' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">EN</a>
            </div>
        </div>

        <p class="text-center mt-6 text-xs text-gray-400">
            &copy; <?php echo date('Y'); ?> <?php echo COMPANY_NAME; ?><br>
            <span class="opacity-80">TURSAB License No: 11738</span>
        </p>
    </div>
</body>
</html>

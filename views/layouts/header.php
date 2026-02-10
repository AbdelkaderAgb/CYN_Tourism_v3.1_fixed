<?php
/**
 * CYN Tourism - Header Layout (Tailwind CSS + Alpine.js)
 * Modern responsive layout using Tailwind utility classes and Alpine.js
 * 
 * @package CYN_Tourism
 * @version 3.1.0
 */

require_once __DIR__ . '/../../auth.php';
require_once __DIR__ . '/../../language.php';
require_once __DIR__ . '/../../functions.php';

Auth::requireAuth();

$user = Auth::user();
$pageTitle = isset($pageTitle) ? $pageTitle : __('dashboard');

$notificationCount = 0;
try {
    if (function_exists('get_notification_count')) {
        $notificationCount = get_notification_count();
    }
} catch (Exception $e) {
    $notificationCount = 0;
}

$currentLang = getCurrentLang();
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': darkMode }" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - <?php echo COMPANY_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS CDN (buildless setup for shared hosting) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        darkMode: 'class',
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
    <style>
        [x-cloak] { display: none !important; }
        /* Minimal custom styles only for scrollbar & transitions */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 2px; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 font-sans text-gray-800 dark:text-gray-200 transition-colors duration-200"
      x-data="appShell()"
      x-init="init()"
>
    <!-- Page Loader -->
    <div x-show="loading" x-transition.opacity.duration.300ms x-cloak
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-white dark:bg-gray-900">
        <div class="text-center">
            <div class="inline-block w-8 h-8 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Loading...</p>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-transition.opacity
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden no-print" x-cloak></div>

    <div class="flex h-full">
        <!-- Sidebar -->
        <aside :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'lg:w-20' : 'lg:w-64']"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-primary-700 via-primary-800 to-primary-900 text-white transition-all duration-300 lg:translate-x-0 lg:static no-print flex flex-col">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-white/10 shrink-0">
                <a href="Vcdashboard.php" class="flex items-center gap-3 overflow-hidden">
                    <div class="flex-shrink-0 w-9 h-9 bg-white/15 rounded-lg flex items-center justify-center">
                        <img src="logo.png" alt="<?php echo COMPANY_NAME; ?>" class="max-w-full max-h-full object-contain">
                    </div>
                    <span x-show="!sidebarCollapsed" x-transition class="font-semibold text-sm truncate"><?php echo COMPANY_NAME; ?></span>
                </a>
                <button @click="toggleCollapse()" class="hidden lg:flex items-center justify-center w-7 h-7 rounded-md hover:bg-white/10 transition-colors">
                    <i class="fas fa-chevron-left text-xs transition-transform duration-300" :class="sidebarCollapsed && 'rotate-180'"></i>
                </button>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 overflow-y-auto sidebar-scroll px-3 py-4 space-y-6">
                <!-- Main Menu -->
                <div>
                    <p x-show="!sidebarCollapsed" class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-wider text-primary-300"><?php echo __('main_menu'); ?></p>
                    <a href="Vcdashboard.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'dashboard') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-home w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('dashboard'); ?></span>
                    </a>
                </div>

                <!-- Transfers -->
                <div>
                    <p x-show="!sidebarCollapsed" class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-wider text-primary-300"><?php echo __('transfers'); ?></p>
                    <a href="consolidated-calendar.php?type=transfer" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'calendar' && ($type ?? '') == 'transfer') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-calendar-alt w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('transfer_calendar'); ?></span>
                    </a>
                    <a href="transfer-voucher-form.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'transfer-voucher') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-ticket-alt w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('transfer_voucher'); ?></span>
                    </a>
                    <a href="transfer-invoice-form.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'transfer-invoice') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-file-invoice w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('transfer_invoice'); ?></span>
                    </a>
                </div>

                <!-- Hotels -->
                <div>
                    <p x-show="!sidebarCollapsed" class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-wider text-primary-300"><?php echo __('hotels'); ?></p>
                    <a href="consolidated-calendar.php?type=hotel" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'calendar' && ($type ?? '') == 'hotel') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-building w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('hotel_calendar'); ?></span>
                    </a>
                    <a href="form.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'hotel-voucher') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-hotel w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('hotel_voucher'); ?></span>
                    </a>
                    <a href="hotel-invoice-form.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'hotel-invoice') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('hotel_invoice'); ?></span>
                    </a>
                </div>

                <!-- Tours -->
                <div>
                    <p x-show="!sidebarCollapsed" class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-wider text-primary-300"><?php echo __('tours'); ?></p>
                    <a href="consolidated-calendar.php?type=tour" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'calendar' && ($type ?? '') == 'tour') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-map-marked-alt w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('tour_calendar'); ?></span>
                    </a>
                    <a href="tour-voucher-form.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'tour-voucher') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-map-marked-alt w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('tour_voucher'); ?></span>
                    </a>
                </div>

                <!-- Finance -->
                <div>
                    <p x-show="!sidebarCollapsed" class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-wider text-primary-300"><?php echo __('finance'); ?></p>
                    <a href="receipt-form.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'receipt') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-receipt w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('receipt'); ?></span>
                    </a>
                    <a href="dashboard.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'receipt-dashboard') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('receipt_dashboard'); ?></span>
                    </a>
                    <a href="hotel-invoice-list.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors text-primary-100 hover:bg-white/10 hover:text-white">
                        <i class="fas fa-list-alt w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('invoice_list'); ?></span>
                    </a>
                </div>

                <!-- Documents -->
                <div>
                    <p x-show="!sidebarCollapsed" class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-wider text-primary-300"><?php echo __('documents'); ?></p>
                    <a href="letterhead.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'letterhead') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-file-alt w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('letterhead'); ?></span>
                    </a>
                </div>

                <?php if (Auth::isAdmin()): ?>
                <!-- Management -->
                <div>
                    <p x-show="!sidebarCollapsed" class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-wider text-primary-300"><?php echo __('management'); ?></p>
                    <a href="management.php?type=partners" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'partners') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-handshake w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('partners'); ?></span>
                    </a>
                    <a href="vehicles.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'vehicles') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-car w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('vehicles'); ?></span>
                    </a>
                    <a href="drivers.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'drivers') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-id-card w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('drivers'); ?></span>
                    </a>
                    <a href="tour-guides.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'tour-guides') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-user-tie w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('tour_guides'); ?></span>
                    </a>
                    <a href="users.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'users') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-users w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('users'); ?></span>
                    </a>
                    <a href="reports.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo (isset($activePage) && $activePage === 'reports') ? 'bg-white/15 text-white font-medium' : 'text-primary-100 hover:bg-white/10 hover:text-white'; ?>">
                        <i class="fas fa-chart-bar w-5 text-center"></i>
                        <span x-show="!sidebarCollapsed"><?php echo __('reports'); ?></span>
                    </a>
                </div>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-h-full overflow-x-hidden" :class="sidebarCollapsed ? 'lg:ml-0' : 'lg:ml-0'">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 lg:px-6 no-print shrink-0">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-bars"></i>
                    </button>

                    <?php if (isset($breadcrumbs)): ?>
                    <nav class="hidden lg:flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <a href="Vcdashboard.php" class="hover:text-primary-600"><i class="fas fa-home"></i></a>
                        <?php foreach ($breadcrumbs as $i => $crumb): ?>
                            <span class="text-gray-300">/</span>
                            <?php if ($i < count($breadcrumbs) - 1): ?>
                            <a href="<?php echo $crumb['url']; ?>" class="hover:text-primary-600"><?php echo $crumb['title']; ?></a>
                            <?php else: ?>
                            <span class="text-gray-700 dark:text-gray-300 font-medium"><?php echo $crumb['title']; ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </nav>
                    <?php endif; ?>

                    <form action="search.php" method="GET" class="hidden sm:flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg px-3 py-2 w-64 focus-within:ring-2 focus-within:ring-primary-500 transition-shadow">
                        <?php echo csrf_field(); ?>
                        <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
                        <input type="text" name="q" placeholder="<?php echo __('search'); ?>..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                               class="bg-transparent border-none outline-none text-sm w-full text-gray-700 dark:text-gray-200 placeholder-gray-400">
                    </form>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Theme Toggle -->
                    <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')"
                            class="flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i x-show="!darkMode" class="fas fa-moon"></i>
                        <i x-show="darkMode" class="fas fa-sun text-yellow-400" x-cloak></i>
                    </button>

                    <!-- Language Switcher -->
                    <?php echo getLanguageSwitcher(); ?>

                    <!-- Notifications -->
                    <a href="notifications.php" class="relative flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-bell"></i>
                        <?php if ($notificationCount > 0): ?>
                        <span class="absolute top-1 right-1 flex items-center justify-center min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold rounded-full px-1"><?php echo $notificationCount; ?></span>
                        <?php endif; ?>
                    </a>

                    <!-- User Dropdown -->
                    <div x-data="{ userOpen: false }" class="relative">
                        <button @click="userOpen = !userOpen" @click.outside="userOpen = false"
                                class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center text-white text-xs font-semibold">
                                <?php echo htmlspecialchars(strtoupper(substr($user['first_name'] ?: 'U', 0, 1) . substr($user['last_name'] ?: '', 0, 1))); ?>
                            </div>
                            <span class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300">
                                <?php echo htmlspecialchars(($user['first_name'] ?: '') . ' ' . ($user['last_name'] ?: '')); ?>
                            </span>
                            <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
                        </button>
                        <div x-show="userOpen" x-transition.origin.top.right x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                            <a href="profile.php" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-user w-4 text-center text-gray-400"></i>
                                <?php echo __('profile'); ?>
                            </a>
                            <hr class="my-1 border-gray-200 dark:border-gray-700">
                            <a href="logout.php" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <i class="fas fa-sign-out-alt w-4 text-center"></i>
                                <?php echo __('logout'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <?php if ($flash = flash('success')): ?>
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="mx-4 lg:mx-6 mt-4 flex items-center gap-3 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl text-sm">
                <i class="fas fa-check-circle"></i>
                <span class="flex-1"><?php echo htmlspecialchars($flash); ?></span>
                <button @click="show = false" class="text-green-500 hover:text-green-700"><i class="fas fa-times"></i></button>
            </div>
            <?php endif; ?>

            <?php if ($flash = flash('error')): ?>
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="mx-4 lg:mx-6 mt-4 flex items-center gap-3 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl text-sm">
                <i class="fas fa-exclamation-circle"></i>
                <span class="flex-1"><?php echo htmlspecialchars($flash); ?></span>
                <button @click="show = false" class="text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>
            </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="flex-1 p-4 lg:p-6">

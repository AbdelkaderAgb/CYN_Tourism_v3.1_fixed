<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/language.php';
require_once __DIR__ . '/functions.php';

Auth::requireAuth();

$user = Auth::user();
$pageTitle = isset($pageTitle) ? $pageTitle : __('dashboard');

// Safely get notification count (may fail if table doesn't exist)
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
<html lang="<?php echo $currentLang; ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - <?php echo COMPANY_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Page Loader -->
    <div class="page-loader">
        <div class="page-loader-content">
            <div class="spinner spinner-lg"></div>
            <p class="page-loader-text">Loading...</p>
        </div>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" onclick="SidebarManager.closeMobile()"></div>

    <div class="app-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="Vcdashboard.php" class="sidebar-brand">
                    <div style="background: white; padding: 4px; border-radius: 8px; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px;">
                        <img src="logo.png" alt="<?php echo COMPANY_NAME; ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <span><?php echo COMPANY_NAME; ?></span>
                </a>
                <button class="sidebar-toggle" onclick="SidebarManager.toggle()" title="Toggle sidebar">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <!-- Main Menu -->
                <div class="nav-section">
                    <div class="nav-section-title"><?php echo __('main_menu'); ?></div>
                    <a href="Vcdashboard.php" class="nav-item <?php echo (isset($activePage) && $activePage === 'dashboard') ? 'active' : ''; ?>" data-title="<?php echo __('dashboard'); ?>">
                        <i class="fas fa-home"></i>
                        <span><?php echo __('dashboard'); ?></span>
                    </a>
                </div>

                <!-- Transfers -->
                <div class="nav-section">
                    <div class="nav-section-title"><?php echo __('transfers'); ?></div>
                    <a href="consolidated-calendar.php?type=transfer" class="nav-item <?php echo (isset($activePage) && $activePage === 'calendar' && ($type ?? '') == 'transfer') ? 'active' : ''; ?>" data-title="<?php echo __('transfer_calendar'); ?>">
                        <i class="fas fa-calendar-alt"></i>
                        <span><?php echo __('transfer_calendar'); ?></span>
                    </a>
                    <a href="transfer-voucher-form.php" class="nav-item <?php echo (isset($activePage) && $activePage === 'transfer-voucher') ? 'active' : ''; ?>" data-title="<?php echo __('transfer_voucher'); ?>">
                        <i class="fas fa-ticket-alt"></i>
                        <span><?php echo __('transfer_voucher'); ?></span>
                    </a>
                    <a href="transfer-invoice-form.php" class="nav-item <?php echo (isset($activePage) && $activePage === 'transfer-invoice') ? 'active' : ''; ?>" data-title="<?php echo __('transfer_invoice'); ?>">
                        <i class="fas fa-file-invoice"></i>
                        <span><?php echo __('transfer_invoice'); ?></span>
                    </a>
                </div>

                <!-- Hotels -->
                <div class="nav-section">
                    <div class="nav-section-title"><?php echo __('hotels'); ?></div>
                    <a href="consolidated-calendar.php?type=hotel" class="nav-item <?php echo (isset($activePage) && $activePage === 'calendar' && ($type ?? '') == 'hotel') ? 'active' : ''; ?>" data-title="<?php echo __('hotel_calendar'); ?>">
                        <i class="fas fa-building"></i>
                        <span><?php echo __('hotel_calendar'); ?></span>
                    </a>
                    <a href="form.php" class="nav-item <?php echo (isset($activePage) && $activePage === 'hotel-voucher') ? 'active' : ''; ?>" data-title="<?php echo __('hotel_voucher'); ?>">
                        <i class="fas fa-hotel"></i>
                        <span><?php echo __('hotel_voucher'); ?></span>
                    </a>
                    <a href="hotel-invoice-form.php" class="nav-item <?php echo (isset($activePage) && $activePage === 'hotel-invoice') ? 'active' : ''; ?>" data-title="<?php echo __('hotel_invoice'); ?>">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span><?php echo __('hotel_invoice'); ?></span>
                    </a>
                </div>

                <!-- Tours -->
                <div class="nav-section">
                    <div class="nav-section-title"><?php echo __('tours'); ?></div>
                    <a href="consolidated-calendar.php?type=tour" class="nav-item <?php echo (isset($activePage) && $activePage === 'calendar' && ($type ?? '') == 'tour') ? 'active' : ''; ?>" data-title="<?php echo __('tour_calendar'); ?>">
                        <i class="fas fa-map-marked-alt"></i>
                        <span><?php echo __('tour_calendar'); ?></span>
                    </a>
                    <a href="tour-voucher-form.php" class="nav-item <?php echo (isset($activePage) && $activePage === 'tour-voucher') ? 'active' : ''; ?>" data-title="<?php echo __('tour_voucher'); ?>">
                        <i class="fas fa-map-marked-alt"></i>
                        <span><?php echo __('tour_voucher'); ?></span>
                    </a>
                </div>

                <!-- Finance -->
                <div class="nav-section">
                    <div class="nav-section-title"><?php echo __('finance'); ?></div>
                    <a href="receipt-form.php" class="nav-item <?php echo (isset($activePage) && $activePage === 'receipt') ? 'active' : ''; ?>" data-title="<?php echo __('receipt'); ?>">
                        <i class="fas fa-receipt"></i>
                        <span><?php echo __('receipt'); ?></span>
                    </a>
                    <a href="dashboard.php" class="nav-item <?php echo (isset($activePage) && $activePage === 'receipt-dashboard') ? 'active' : ''; ?>" data-title="<?php echo __('receipt_dashboard'); ?>">
                        <i class="fas fa-chart-pie"></i>
                        <span><?php echo __('receipt_dashboard'); ?></span>
                    </a>
                    <a href="hotel-invoice-list.php" class="nav-item" data-title="<?php echo __('invoice_list'); ?>">
                        <i class="fas fa-list-alt"></i>
                        <span><?php echo __('invoice_list'); ?></span>
                    </a>
                </div>

                <!-- Documents -->
                <div class="nav-section">
                    <div class="nav-section-title"><?php echo __('documents'); ?></div>
                    <a href="letterhead.php" class="nav-item <?php echo (isset($activePage) && $activePage === 'letterhead') ? 'active' : ''; ?>" data-title="<?php echo __('letterhead'); ?>">
                        <i class="fas fa-file-alt"></i>
                        <span><?php echo __('letterhead'); ?></span>
                    </a>
                </div>

                <?php if (Auth::isAdmin()): ?>
                <!-- Management -->
                <div class="nav-section">
                    <div class="nav-section-title"><?php echo __('management'); ?></div>
                    <a href="management.php?type=partners" class="nav-item <?php echo (isset($activePage) && $activePage === 'partners') ? 'active' : ''; ?>" data-title="<?php echo __('partners'); ?>">
                        <i class="fas fa-handshake"></i>
                        <span><?php echo __('partners'); ?></span>
                    </a>
                    <a href="vehicles.php" class="nav-item <?php echo (isset($activePage) && $activePage === 'vehicles') ? 'active' : ''; ?>" data-title="<?php echo __('vehicles'); ?>">
                        <i class="fas fa-car"></i>
                        <span><?php echo __('vehicles'); ?></span>
                    </a>
                    <a href="drivers.php" class="nav-item <?php echo (isset($activePage) && $activePage === 'drivers') ? 'active' : ''; ?>" data-title="<?php echo __('drivers'); ?>">
                        <i class="fas fa-id-card"></i>
                        <span><?php echo __('drivers'); ?></span>
                    </a>
                    <a href="tour-guides.php" class="nav-item <?php echo (isset($activePage) && $activePage === 'tour-guides') ? 'active' : ''; ?>" data-title="<?php echo __('tour_guides'); ?>">
                        <i class="fas fa-user-tie"></i>
                        <span><?php echo __('tour_guides'); ?></span>
                    </a>
                    <a href="users.php" class="nav-item <?php echo (isset($activePage) && $activePage === 'users') ? 'active' : ''; ?>" data-title="<?php echo __('users'); ?>">
                        <i class="fas fa-users"></i>
                        <span><?php echo __('users'); ?></span>
                    </a>
                    <a href="reports.php" class="nav-item <?php echo (isset($activePage) && $activePage === 'reports') ? 'active' : ''; ?>" data-title="<?php echo __('reports'); ?>">
                        <i class="fas fa-chart-bar"></i>
                        <span><?php echo __('reports'); ?></span>
                    </a>
                </div>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="top-bar-left">
                    <button class="menu-toggle" onclick="SidebarManager.toggleMobile()" title="Menu">
                        <i class="fas fa-bars"></i>
                    </button>

                    <!-- Breadcrumbs -->
                    <?php if (isset($breadcrumbs)): ?>
                    <nav class="breadcrumbs d-none d-lg-flex">
                        <a href="Vcdashboard.php"><i class="fas fa-home"></i></a>
                        <span class="separator">/</span>
                        <?php foreach ($breadcrumbs as $i => $crumb): ?>
                            <?php if ($i < count($breadcrumbs) - 1): ?>
                            <a href="<?php echo $crumb['url']; ?>"><?php echo $crumb['title']; ?></a>
                            <span class="separator">/</span>
                            <?php else: ?>
                            <span class="current"><?php echo $crumb['title']; ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </nav>
                    <?php endif; ?>

                    <form class="search-global" action="search.php" method="GET">
                    <?php echo csrf_field(); ?>
                        <i class="fas fa-search"></i>
                        <input type="text" name="q" placeholder="<?php echo __('search'); ?>..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                    </form>
                </div>

                <div class="top-bar-right">
                    <!-- Theme Toggle -->
                    <button class="icon-btn theme-toggle" onclick="ThemeManager.toggle()" title="Toggle theme">
                        <i class="fas fa-moon"></i>
                    </button>

                    <!-- Language Switcher -->
                    <?php echo getLanguageSwitcher(); ?>

                    <!-- Notifications -->
                    <div class="dropdown">
                        <a href="notifications.php" class="icon-btn" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <?php if ($notificationCount > 0): ?>
                            <span class="badge"><?php echo $notificationCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </div>

                    <!-- User Dropdown -->
                    <div class="user-dropdown">
                        <button class="user-dropdown-toggle">
                            <div class="user-avatar">
                                <?php echo htmlspecialchars(strtoupper(substr($user['first_name'] ?: 'U', 0, 1) . substr($user['last_name'] ?: '', 0, 1))); ?>
                            </div>
                            <span class="user-name">
                                <?php echo htmlspecialchars(($user['first_name'] ?: '') . ' ' . ($user['last_name'] ?: '')); ?>
                            </span>
                            <i class="fas fa-chevron-down" style="font-size: 0.75rem; color: var(--text-tertiary);"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="profile.php" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                <span><?php echo __('profile'); ?></span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i>
                                <span><?php echo __('logout'); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <?php if ($flash = flash('success')): ?>
            <div class="alert alert-success" style="margin: var(--space-4) var(--space-5) 0;">
                <i class="fas fa-check-circle alert-icon"></i>
                <div class="alert-content"><?php echo htmlspecialchars($flash); ?></div>
                <button class="alert-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php endif; ?>

            <?php if ($flash = flash('error')): ?>
            <div class="alert alert-danger" style="margin: var(--space-4) var(--space-5) 0;">
                <i class="fas fa-exclamation-circle alert-icon"></i>
                <div class="alert-content"><?php echo htmlspecialchars($flash); ?></div>
                <button class="alert-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="page-content">

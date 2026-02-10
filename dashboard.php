<?php
/**
 * CYN Tourism - Dashboard Page
 * 
 * Modernized with Tailwind CSS + Alpine.js
 * Backend uses PDO prepared statements via Database singleton
 * 
 * @package CYN_Tourism
 * @version 3.1.0
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

// Require authentication
Auth::requireAuth();

$today = date('Y-m-d');
$weekStart = date('Y-m-d', strtotime('-7 days'));
$monthStart = date('Y-m-01');

// Safely fetch dashboard stats (wrapped in try-catch for missing tables)
$todayTransfers = 0;
$weeklyRevenue = 0;
$monthRevenue = 0;
$monthVouchers = 0;
$pendingInvoices = 0;
$totalPartners = 0;
$totalVehicles = 0;
$totalDrivers = 0;
$upcomingTransfers = [];

$statQueries = [
    'todayTransfers' => [
        "SELECT COUNT(*) as val FROM vouchers WHERE pickup_date = ?",
        [$today],
    ],
    'weeklyRevenue' => [
        "SELECT SUM(total_amount) as val FROM invoices WHERE created_at >= ? AND status = 'paid'",
        [$weekStart . ' 00:00:00'],
    ],
    'monthRevenue' => [
        "SELECT SUM(total_amount) as val FROM invoices WHERE created_at >= ? AND status = 'paid'",
        [$monthStart . ' 00:00:00'],
    ],
    'monthVouchers' => [
        "SELECT COUNT(*) as val FROM vouchers WHERE created_at >= ?",
        [$monthStart . ' 00:00:00'],
    ],
    'pendingInvoices' => [
        "SELECT COUNT(*) as val FROM invoices WHERE status = 'pending'",
        [],
    ],
    'totalPartners' => [
        "SELECT COUNT(*) as val FROM partners WHERE status = 'active'",
        [],
    ],
    'totalVehicles' => [
        "SELECT COUNT(*) as val FROM vehicles WHERE status = 'active'",
        [],
    ],
    'totalDrivers' => [
        "SELECT COUNT(*) as val FROM drivers WHERE status = 'active'",
        [],
    ],
];

foreach ($statQueries as $varName => [$sql, $params]) {
    try {
        $row = Database::getInstance()->fetchOne($sql, $params);
        $$varName = $row['val'] ?? 0;
    } catch (Exception $e) {
        // Table may not exist yet
    }
}

try {
    $upcomingTransfers = Database::getInstance()->fetchAll(
        "SELECT id, voucher_no, company_name, pickup_date, pickup_time, pickup_location, dropoff_location, status
         FROM vouchers WHERE pickup_date >= ? ORDER BY pickup_date ASC, pickup_time ASC LIMIT 5",
        [$today]
    );
} catch (Exception $e) {
    $upcomingTransfers = [];
}

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

// Use new Tailwind-based views
include __DIR__ . '/views/layouts/header.php';
include __DIR__ . '/views/pages/dashboard.php';
include __DIR__ . '/views/layouts/footer.php';
?>

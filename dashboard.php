<?php
/**
 * CYN Tourism - Dashboard Page
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

try {
    $row = Database::getInstance()->fetchOne(
        "SELECT COUNT(*) as count FROM vouchers WHERE pickup_date = ?",
        [$today]
    );
    $todayTransfers = $row['count'] ?? 0;
} catch (Exception $e) {}

try {
    $row = Database::getInstance()->fetchOne(
        "SELECT SUM(total_amount) as total FROM invoices WHERE created_at >= ? AND status = 'paid'",
        [$weekStart . ' 00:00:00']
    );
    $weeklyRevenue = $row['total'] ?? 0;
} catch (Exception $e) {}

try {
    $row = Database::getInstance()->fetchOne(
        "SELECT SUM(total_amount) as total FROM invoices WHERE created_at >= ? AND status = 'paid'",
        [$monthStart . ' 00:00:00']
    );
    $monthRevenue = $row['total'] ?? 0;
} catch (Exception $e) {}

try {
    $row = Database::getInstance()->fetchOne(
        "SELECT COUNT(*) as count FROM vouchers WHERE created_at >= ?",
        [$monthStart . ' 00:00:00']
    );
    $monthVouchers = $row['count'] ?? 0;
} catch (Exception $e) {}

try {
    $row = Database::getInstance()->fetchOne(
        "SELECT COUNT(*) as count FROM invoices WHERE status = 'pending'"
    );
    $pendingInvoices = $row['count'] ?? 0;
} catch (Exception $e) {}

try {
    $row = Database::getInstance()->fetchOne(
        "SELECT COUNT(*) as count FROM partners WHERE status = 'active'"
    );
    $totalPartners = $row['count'] ?? 0;
} catch (Exception $e) {}

try {
    $row = Database::getInstance()->fetchOne(
        "SELECT COUNT(*) as count FROM vehicles WHERE status = 'active'"
    );
    $totalVehicles = $row['count'] ?? 0;
} catch (Exception $e) {}

try {
    $row = Database::getInstance()->fetchOne(
        "SELECT COUNT(*) as count FROM drivers WHERE status = 'active'"
    );
    $totalDrivers = $row['count'] ?? 0;
} catch (Exception $e) {}

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
include __DIR__ . '/header.php';
?>

<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Dashboard</h1>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-exchange-alt"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo (int)$todayTransfers; ?></div>
            <div class="stat-label">Bugunku Transferler</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">$<?php echo number_format($weeklyRevenue, 2); ?></div>
            <div class="stat-label">Haftalik Gelir</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">$<?php echo number_format($monthRevenue, 2); ?></div>
            <div class="stat-label">Aylik Gelir</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon pink">
            <i class="fas fa-ticket-alt"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo (int)$monthVouchers; ?></div>
            <div class="stat-label">Aylik Voucher</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo (int)$pendingInvoices; ?></div>
            <div class="stat-label">Bekleyen Faturalar</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon teal">
            <i class="fas fa-handshake"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo (int)$totalPartners; ?></div>
            <div class="stat-label">Partnerler</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon indigo">
            <i class="fas fa-car"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo (int)$totalVehicles; ?></div>
            <div class="stat-label">Araclar</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon brown">
            <i class="fas fa-user-tie"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo (int)$totalDrivers; ?></div>
            <div class="stat-label">Soforler</div>
        </div>
    </div>
</div>

<!-- Upcoming Transfers -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-day"></i> Yaklasan Transferler</h3>
    </div>
    <div class="card-body">
        <?php if (empty($upcomingTransfers)): ?>
            <p class="text-muted">Yaklasan transfer bulunmuyor.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Voucher No</th>
                            <th>Sirket</th>
                            <th>Tarih</th>
                            <th>Saat</th>
                            <th>Alis Yeri</th>
                            <th>Birakma Yeri</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcomingTransfers as $transfer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transfer['voucher_no']); ?></td>
                            <td><?php echo htmlspecialchars($transfer['company_name']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($transfer['pickup_date'])); ?></td>
                            <td><?php echo htmlspecialchars($transfer['pickup_time']); ?></td>
                            <td><?php echo htmlspecialchars($transfer['pickup_location']); ?></td>
                            <td><?php echo htmlspecialchars($transfer['dropoff_location']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo htmlspecialchars($transfer['status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($transfer['status'])); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>

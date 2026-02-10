<?php
/**
 * CYN Tourism - Dashboard Controller
 * Handles dashboard data aggregation and view rendering
 * 
 * @package CYN_Tourism
 * @version 3.1.0
 */

namespace CYN\Controllers;

class DashboardController
{
    /**
     * Display the main dashboard
     */
    public function index(): void
    {
        require_once __DIR__ . '/../../auth.php';
        require_once __DIR__ . '/../../functions.php';

        \Auth::requireAuth();

        $data = $this->getDashboardData();
        $data['pageTitle'] = 'Dashboard';
        $data['activePage'] = 'dashboard';

        extract($data);

        $pageTitle = $data['pageTitle'];
        $activePage = $data['activePage'];

        include __DIR__ . '/../../views/layouts/header.php';
        include __DIR__ . '/../../views/pages/dashboard.php';
        include __DIR__ . '/../../views/layouts/footer.php';
    }

    /**
     * Gather all dashboard statistics
     *
     * @return array<string, mixed>
     */
    private function getDashboardData(): array
    {
        $today = date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('-7 days'));
        $monthStart = date('Y-m-01');

        $stats = [
            'todayTransfers' => 0,
            'weeklyRevenue' => 0,
            'monthRevenue' => 0,
            'monthVouchers' => 0,
            'pendingInvoices' => 0,
            'totalPartners' => 0,
            'totalVehicles' => 0,
            'totalDrivers' => 0,
            'upcomingTransfers' => [],
        ];

        $queries = [
            'todayTransfers' => [
                "SELECT COUNT(*) as count FROM vouchers WHERE pickup_date = ?",
                [$today],
                'count',
            ],
            'weeklyRevenue' => [
                "SELECT SUM(total_amount) as total FROM invoices WHERE created_at >= ? AND status = 'paid'",
                [$weekStart . ' 00:00:00'],
                'total',
            ],
            'monthRevenue' => [
                "SELECT SUM(total_amount) as total FROM invoices WHERE created_at >= ? AND status = 'paid'",
                [$monthStart . ' 00:00:00'],
                'total',
            ],
            'monthVouchers' => [
                "SELECT COUNT(*) as count FROM vouchers WHERE created_at >= ?",
                [$monthStart . ' 00:00:00'],
                'count',
            ],
            'pendingInvoices' => [
                "SELECT COUNT(*) as count FROM invoices WHERE status = 'pending'",
                [],
                'count',
            ],
            'totalPartners' => [
                "SELECT COUNT(*) as count FROM partners WHERE status = 'active'",
                [],
                'count',
            ],
            'totalVehicles' => [
                "SELECT COUNT(*) as count FROM vehicles WHERE status = 'active'",
                [],
                'count',
            ],
            'totalDrivers' => [
                "SELECT COUNT(*) as count FROM drivers WHERE status = 'active'",
                [],
                'count',
            ],
        ];

        foreach ($queries as $key => [$sql, $params, $field]) {
            try {
                $row = \Database::getInstance()->fetchOne($sql, $params);
                $stats[$key] = $row[$field] ?? 0;
            } catch (\Exception $e) {
                // Table may not exist yet
            }
        }

        try {
            $stats['upcomingTransfers'] = \Database::getInstance()->fetchAll(
                "SELECT id, voucher_no, company_name, pickup_date, pickup_time, pickup_location, dropoff_location, status
                 FROM vouchers WHERE pickup_date >= ? ORDER BY pickup_date ASC, pickup_time ASC LIMIT 5",
                [$today]
            );
        } catch (\Exception $e) {
            $stats['upcomingTransfers'] = [];
        }

        return $stats;
    }
}

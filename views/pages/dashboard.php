<?php
/**
 * CYN Tourism - Dashboard Page View (Tailwind CSS)
 * Modern responsive dashboard with stat cards and upcoming transfers table
 * 
 * @package CYN_Tourism
 * @version 3.1.0
 */

// Variables expected: $todayTransfers, $weeklyRevenue, $monthRevenue, $monthVouchers,
//                     $pendingInvoices, $totalPartners, $totalVehicles, $totalDrivers,
//                     $upcomingTransfers
?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
</div>

<!-- Stats Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Today's Transfers -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
            <i class="fas fa-exchange-alt text-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo (int)$todayTransfers; ?></p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Bugunku Transferler</p>
        </div>
    </div>

    <!-- Weekly Revenue -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
            <i class="fas fa-file-invoice-dollar text-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">$<?php echo number_format($weeklyRevenue, 2); ?></p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Haftalik Gelir</p>
        </div>
    </div>

    <!-- Monthly Revenue -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400">
            <i class="fas fa-money-bill-wave text-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">$<?php echo number_format($monthRevenue, 2); ?></p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Aylik Gelir</p>
        </div>
    </div>

    <!-- Monthly Vouchers -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center text-pink-600 dark:text-pink-400">
            <i class="fas fa-ticket-alt text-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo (int)$monthVouchers; ?></p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Aylik Voucher</p>
        </div>
    </div>

    <!-- Pending Invoices -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
            <i class="fas fa-clock text-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo (int)$pendingInvoices; ?></p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Bekleyen Faturalar</p>
        </div>
    </div>

    <!-- Partners -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center text-teal-600 dark:text-teal-400">
            <i class="fas fa-handshake text-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo (int)$totalPartners; ?></p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Partnerler</p>
        </div>
    </div>

    <!-- Vehicles -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
            <i class="fas fa-car text-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo (int)$totalVehicles; ?></p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Araclar</p>
        </div>
    </div>

    <!-- Drivers -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-700 dark:text-amber-400">
            <i class="fas fa-user-tie text-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo (int)$totalDrivers; ?></p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Soforler</p>
        </div>
    </div>
</div>

<!-- Upcoming Transfers -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <i class="fas fa-calendar-day text-primary-600 dark:text-primary-400"></i>
        <h3 class="font-semibold text-gray-900 dark:text-white">Yaklasan Transferler</h3>
    </div>
    <div class="p-5">
        <?php if (empty($upcomingTransfers)): ?>
            <p class="text-sm text-gray-500 dark:text-gray-400">Yaklasan transfer bulunmuyor.</p>
        <?php else: ?>
            <div class="overflow-x-auto -mx-5 px-5">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Voucher No</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sirket</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tarih</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Saat</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 hidden md:table-cell">Alis Yeri</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 hidden md:table-cell">Birakma Yeri</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($upcomingTransfers as $transfer): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="py-3 px-3 font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($transfer['voucher_no']); ?></td>
                            <td class="py-3 px-3 text-gray-600 dark:text-gray-300"><?php echo htmlspecialchars($transfer['company_name']); ?></td>
                            <td class="py-3 px-3 text-gray-600 dark:text-gray-300"><?php echo date('d/m/Y', strtotime($transfer['pickup_date'])); ?></td>
                            <td class="py-3 px-3 text-gray-600 dark:text-gray-300"><?php echo htmlspecialchars($transfer['pickup_time']); ?></td>
                            <td class="py-3 px-3 text-gray-600 dark:text-gray-300 hidden md:table-cell"><?php echo htmlspecialchars($transfer['pickup_location']); ?></td>
                            <td class="py-3 px-3 text-gray-600 dark:text-gray-300 hidden md:table-cell"><?php echo htmlspecialchars($transfer['dropoff_location']); ?></td>
                            <td class="py-3 px-3">
                                <?php
                                $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                    'confirmed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                ];
                                $status = $transfer['status'];
                                $classes = $statusClasses[$status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $classes; ?>">
                                    <?php echo ucfirst(htmlspecialchars($status)); ?>
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

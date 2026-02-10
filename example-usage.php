<?php
/**
 * Example: Using the new VoucherModel
 * 
 * This demonstrates how to use the Data Access Layer pattern
 * instead of direct database queries scattered throughout the code.
 */

// Load the new bootstrap
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/Models/VoucherModel.php';

// OLD WAY (scattered throughout the codebase):
// $db = Database::getInstance();
// $vouchers = $db->fetchAll("SELECT * FROM vouchers WHERE status = ?", ['pending']);

// NEW WAY (using the Data Access Layer):
$voucherModel = new VoucherModel();

// Find all pending vouchers
$pendingVouchers = $voucherModel->findByStatus('pending');

// Find vouchers by date range
$startDate = '2024-01-01';
$endDate = '2024-12-31';
$vouchersInRange = $voucherModel->findByDateRange($startDate, $endDate);

// Generate next voucher number
$nextVoucherNo = $voucherModel->generateVoucherNumber();

// Create a new voucher
$newVoucher = [
    'voucher_no' => $nextVoucherNo,
    'company_name' => 'Example Tours',
    'hotel_name' => 'Example Hotel',
    'pickup_location' => 'Airport',
    'dropoff_location' => 'Hotel',
    'pickup_date' => '2024-03-15',
    'pickup_time' => '14:00:00',
    'transfer_type' => 'one_way',
    'total_pax' => 2,
    'price' => 50.00,
    'currency' => 'USD',
    'status' => 'pending',
    'created_by' => 1
];

// This would insert the voucher:
// $voucherId = $voucherModel->create($newVoucher);

echo "Example complete - new architecture ready to use!\n";

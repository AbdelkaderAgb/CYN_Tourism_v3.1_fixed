<?php
/**
 * CYN Tourism - Voucher Model
 * 
 * Data Access Layer for vouchers table
 * 
 * @package CYN_Tourism
 * @version 3.0.0
 */

require_once __DIR__ . '/BaseModel.php';

class VoucherModel extends BaseModel {
    
    protected $table = 'vouchers';
    
    protected $fillable = [
        'voucher_no',
        'company_name',
        'company_id',
        'hotel_name',
        'pickup_location',
        'dropoff_location',
        'pickup_date',
        'pickup_time',
        'return_date',
        'return_time',
        'transfer_type',
        'total_pax',
        'passengers',
        'flight_number',
        'flight_arrival_time',
        'vehicle_id',
        'driver_id',
        'guide_id',
        'special_requests',
        'price',
        'currency',
        'status',
        'payment_status',
        'notes',
        'created_by',
        'updated_by'
    ];
    
    /**
     * Find vouchers by date range
     * 
     * @param string $startDate Start date
     * @param string $endDate End date
     * @return array Vouchers
     */
    public function findByDateRange($startDate, $endDate) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE pickup_date BETWEEN ? AND ? 
                  ORDER BY pickup_date ASC, pickup_time ASC";
        return $this->db->fetchAll($query, [$startDate, $endDate]);
    }
    
    /**
     * Find vouchers by company
     * 
     * @param string $companyName Company name
     * @return array Vouchers
     */
    public function findByCompany($companyName) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE company_name LIKE ? 
                  ORDER BY pickup_date DESC";
        return $this->db->fetchAll($query, ["%$companyName%"]);
    }
    
    /**
     * Find vouchers by status
     * 
     * @param string $status Status
     * @return array Vouchers
     */
    public function findByStatus($status) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE status = ? 
                  ORDER BY pickup_date ASC";
        return $this->db->fetchAll($query, [$status]);
    }
    
    /**
     * Generate next voucher number
     * 
     * @return string Voucher number
     */
    public function generateVoucherNumber() {
        $prefix = 'VCH-' . date('Ymd') . '-';
        $query = "SELECT voucher_no FROM {$this->table} 
                  WHERE voucher_no LIKE ? 
                  ORDER BY id DESC LIMIT 1";
        $result = $this->db->fetchOne($query, [$prefix . '%']);
        
        if ($result) {
            $lastNumber = (int) substr($result['voucher_no'], -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}

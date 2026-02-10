<?php
/**
 * CYN Tourism - Driver Model
 * 
 * Data Access Layer for drivers table
 * 
 * @package CYN_Tourism
 * @version 3.0.0
 */

require_once __DIR__ . '/BaseModel.php';

class DriverModel extends BaseModel {
    
    protected $table = 'drivers';
    
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'license_number',
        'license_expiry',
        'status',
        'notes'
    ];
    
    /**
     * Find active drivers
     * 
     * @return array Active drivers
     */
    public function findActive() {
        $query = "SELECT * FROM {$this->table} 
                  WHERE status = 'active' 
                  ORDER BY first_name ASC";
        return $this->db->fetchAll($query);
    }
    
    /**
     * Find available drivers for a date
     * 
     * @param string $date Date in Y-m-d format
     * @return array Available drivers
     */
    public function findAvailableForDate($date) {
        $query = "SELECT d.* FROM {$this->table} d
                  WHERE d.status = 'active'
                  AND d.id NOT IN (
                      SELECT driver_id FROM vouchers 
                      WHERE pickup_date = ? 
                      AND driver_id IS NOT NULL
                      AND status NOT IN ('cancelled', 'completed')
                  )
                  ORDER BY d.first_name ASC";
        return $this->db->fetchAll($query, [$date]);
    }
}

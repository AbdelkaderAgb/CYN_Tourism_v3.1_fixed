<?php
/**
 * CYN Tourism - Vehicle Model
 * 
 * Data Access Layer for vehicles table
 * 
 * @package CYN_Tourism
 * @version 3.0.0
 */

require_once __DIR__ . '/BaseModel.php';

class VehicleModel extends BaseModel {
    
    protected $table = 'vehicles';
    
    protected $fillable = [
        'plate_number',
        'model',
        'year',
        'capacity',
        'vehicle_type',
        'status',
        'notes'
    ];
    
    /**
     * Find active vehicles
     * 
     * @return array Active vehicles
     */
    public function findActive() {
        $query = "SELECT * FROM {$this->table} 
                  WHERE status = 'active' 
                  ORDER BY model ASC";
        return $this->db->fetchAll($query);
    }
    
    /**
     * Find vehicles by capacity
     * 
     * @param int $minCapacity Minimum capacity
     * @return array Vehicles
     */
    public function findByCapacity($minCapacity) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE status = 'active' 
                  AND capacity >= ? 
                  ORDER BY capacity ASC";
        return $this->db->fetchAll($query, [$minCapacity]);
    }
    
    /**
     * Find available vehicles for a date
     * 
     * @param string $date Date in Y-m-d format
     * @return array Available vehicles
     */
    public function findAvailableForDate($date) {
        $query = "SELECT v.* FROM {$this->table} v
                  WHERE v.status = 'active'
                  AND v.id NOT IN (
                      SELECT vehicle_id FROM vouchers 
                      WHERE pickup_date = ? 
                      AND vehicle_id IS NOT NULL
                      AND status NOT IN ('cancelled', 'completed')
                  )
                  ORDER BY v.capacity ASC";
        return $this->db->fetchAll($query, [$date]);
    }
}

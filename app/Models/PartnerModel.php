<?php
/**
 * CYN Tourism - Partner Model
 * 
 * Data Access Layer for partners table
 * 
 * @package CYN_Tourism
 * @version 3.0.0
 */

require_once __DIR__ . '/BaseModel.php';

class PartnerModel extends BaseModel {
    
    protected $table = 'partners';
    
    protected $fillable = [
        'company_name',
        'contact_name',
        'email',
        'phone',
        'address',
        'status',
        'notes'
    ];
    
    /**
     * Find active partners
     * 
     * @return array Active partners
     */
    public function findActive() {
        $query = "SELECT * FROM {$this->table} 
                  WHERE status = 'active' 
                  ORDER BY company_name ASC";
        return $this->db->fetchAll($query);
    }
    
    /**
     * Find partner by company name
     * 
     * @param string $companyName Company name
     * @return array|null Partner data or null
     */
    public function findByCompany($companyName) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE company_name LIKE ? 
                  LIMIT 1";
        return $this->db->fetchOne($query, ["%$companyName%"]);
    }
    
    /**
     * Search partners
     * 
     * @param string $search Search term
     * @return array Partners
     */
    public function search($search) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE company_name LIKE ? 
                     OR contact_name LIKE ? 
                     OR email LIKE ? 
                  ORDER BY company_name ASC";
        $term = "%$search%";
        return $this->db->fetchAll($query, [$term, $term, $term]);
    }
}

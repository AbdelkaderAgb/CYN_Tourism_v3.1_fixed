<?php
/**
 * CYN Tourism - Base Model Class
 * 
 * Provides a Data Access Layer (DAL) pattern for all models.
 * This eliminates direct database calls scattered throughout the codebase.
 * 
 * @package CYN_Tourism
 * @version 3.0.0
 */

abstract class BaseModel {
    
    /** @var string The table name */
    protected $table;
    
    /** @var string Primary key column */
    protected $primaryKey = 'id';
    
    /** @var array Fillable columns */
    protected $fillable = [];
    
    /** @var Database Database instance */
    protected $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        if (!class_exists('Database')) {
            require_once __DIR__ . '/Database.php';
        }
        $this->db = Database::getInstance();
    }
    
    /**
     * Find record by ID
     * 
     * @param int $id Record ID
     * @return array|null Record data or null
     */
    public function find($id) {
        $query = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        return $this->db->fetchOne($query, [$id]);
    }
    
    /**
     * Find all records
     * 
     * @param array $conditions Optional WHERE conditions
     * @param array $params Optional parameters
     * @return array Records
     */
    public function findAll($conditions = [], $params = []) {
        $query = "SELECT * FROM {$this->table}";
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(' AND ', $conditions);
        }
        
        return $this->db->fetchAll($query, $params);
    }
    
    /**
     * Create a new record
     * 
     * @param array $data Record data
     * @return int|false Insert ID or false on failure
     */
    public function create($data) {
        // Filter data to only fillable fields
        $data = $this->filterFillable($data);
        
        $columns = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $query = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        
        $stmt = $this->db->query($query, $values);
        return $stmt ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Update a record
     * 
     * @param int $id Record ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update($id, $data) {
        // Filter data to only fillable fields
        $data = $this->filterFillable($data);
        
        $setParts = [];
        $values = [];
        
        foreach ($data as $column => $value) {
            $setParts[] = "$column = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        
        $query = sprintf(
            "UPDATE %s SET %s WHERE %s = ?",
            $this->table,
            implode(', ', $setParts),
            $this->primaryKey
        );
        
        $stmt = $this->db->query($query, $values);
        return $stmt ? $stmt->rowCount() > 0 : false;
    }
    
    /**
     * Delete a record
     * 
     * @param int $id Record ID
     * @return bool Success status
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->query($query, [$id]);
        return $stmt ? $stmt->rowCount() > 0 : false;
    }
    
    /**
     * Filter data to only fillable fields
     * 
     * @param array $data Input data
     * @return array Filtered data
     */
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    /**
     * Count records
     * 
     * @param array $conditions Optional WHERE conditions
     * @param array $params Optional parameters
     * @return int Count
     */
    public function count($conditions = [], $params = []) {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $result = $this->db->fetchOne($query, $params);
        return (int) ($result['count'] ?? 0);
    }
}

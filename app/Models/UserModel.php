<?php
/**
 * CYN Tourism - User Model
 * 
 * Data Access Layer for users table
 * 
 * @package CYN_Tourism
 * @version 3.0.0
 */

require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel {
    
    protected $table = 'users';
    
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'profile_image'
    ];
    
    /**
     * Find user by email
     * 
     * @param string $email Email address
     * @return array|null User data or null
     */
    public function findByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
        return $this->db->fetchOne($query, [$email]);
    }
    
    /**
     * Find active users
     * 
     * @return array Active users
     */
    public function findActive() {
        $query = "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY first_name ASC";
        return $this->db->fetchAll($query);
    }
    
    /**
     * Find users by role
     * 
     * @param string $role User role
     * @return array Users
     */
    public function findByRole($role) {
        $query = "SELECT * FROM {$this->table} WHERE role = ? AND status = 'active' ORDER BY first_name ASC";
        return $this->db->fetchAll($query, [$role]);
    }
    
    /**
     * Update last login
     * 
     * @param int $userId User ID
     * @param string $ip IP address
     * @return bool Success status
     */
    public function updateLastLogin($userId, $ip) {
        $query = "UPDATE {$this->table} 
                  SET last_login = NOW(), last_login_ip = ? 
                  WHERE id = ?";
        $stmt = $this->db->query($query, [$ip, $userId]);
        return $stmt ? $stmt->rowCount() > 0 : false;
    }
    
    /**
     * Increment failed login attempts
     * 
     * @param int $userId User ID
     * @return bool Success status
     */
    public function incrementFailedAttempts($userId) {
        $query = "UPDATE {$this->table} 
                  SET failed_login_attempts = failed_login_attempts + 1 
                  WHERE id = ?";
        $stmt = $this->db->query($query, [$userId]);
        return $stmt ? $stmt->rowCount() > 0 : false;
    }
    
    /**
     * Reset failed login attempts
     * 
     * @param int $userId User ID
     * @return bool Success status
     */
    public function resetFailedAttempts($userId) {
        $query = "UPDATE {$this->table} 
                  SET failed_login_attempts = 0, locked_until = NULL 
                  WHERE id = ?";
        $stmt = $this->db->query($query, [$userId]);
        return $stmt ? $stmt->rowCount() > 0 : false;
    }
}

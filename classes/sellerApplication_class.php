<?php
/**
 * Seller Application Class
 * ThriftHub - Seller Application Management Class
 * 
 * This class handles seller application submissions and admin review
 */

require_once __DIR__ . '/../settings/db_class.php';

class SellerApplication extends Database {
    
    /**
     * Create a new seller application
     * 
     * @param int $userId User ID applying to be a seller
     * @param string $documentationPath Path to documentation CSV file
     * @return int|false Returns application_id on success, false on failure
     */
    public function create_application($userId, $documentationPath) {
        $userId = (int)$userId;
        
        if ($userId <= 0 || empty($documentationPath)) {
            error_log("SellerApplication::create_application - Invalid userId or documentationPath. userId: $userId, path: " . ($documentationPath ?? 'empty'));
            return false;
        }
        
        // Check if user already has a pending or approved application
        $existing = $this->get_application_by_user($userId);
        if ($existing && in_array($existing['status'], ['pending', 'approved'])) {
            error_log("SellerApplication::create_application - User $userId already has an application with status: " . $existing['status']);
            return false; // Already has active application
        }
        
        $documentationPath = $this->escape($documentationPath);
        
        $sql = "INSERT INTO seller_applications (user_id, documentation_path, status, submitted_at) 
                VALUES ($userId, '$documentationPath', 'pending', NOW())";
        
        error_log("SellerApplication::create_application - Executing SQL: $sql");
        
        $result = $this->query($sql);
        if ($result) {
            $insertId = $this->insert_id();
            error_log("SellerApplication::create_application - Success! Insert ID: $insertId");
            return $insertId;
        } else {
            $conn = $this->getConnection();
            $error = $conn ? $conn->error : 'Unknown error';
            error_log("SellerApplication::create_application - Query failed. Error: $error");
        }
        
        return false;
    }
    
    /**
     * Get application by user ID
     * 
     * @param int $userId User ID
     * @return array|false Returns application data or false if not found
     */
    public function get_application_by_user($userId) {
        $userId = (int)$userId;
        $sql = "SELECT sa.*, u.name, u.email 
                FROM seller_applications sa
                JOIN users u ON sa.user_id = u.user_id
                WHERE sa.user_id = $userId
                ORDER BY sa.submitted_at DESC
                LIMIT 1";
        return $this->fetchOne($sql);
    }
    
    /**
     * Get pending applications
     * 
     * @return array Returns array of pending applications
     */
    public function get_pending_applications() {
        $sql = "SELECT sa.*, u.name, u.email, u.phone_number 
                FROM seller_applications sa
                JOIN users u ON sa.user_id = u.user_id
                WHERE sa.status = 'pending'
                ORDER BY sa.submitted_at ASC";
        return $this->fetchAll($sql);
    }
    
    /**
     * Get application by ID
     * 
     * @param int $applicationId Application ID
     * @return array|false Returns application data or false if not found
     */
    public function get_application_by_id($applicationId) {
        $applicationId = (int)$applicationId;
        $sql = "SELECT sa.*, u.name, u.email, u.phone_number 
                FROM seller_applications sa
                JOIN users u ON sa.user_id = u.user_id
                WHERE sa.application_id = $applicationId";
        return $this->fetchOne($sql);
    }
    
    /**
     * Update application status
     * 
     * @param int $applicationId Application ID
     * @param string $status Status (approved/rejected)
     * @param int $reviewedBy User ID of reviewer
     * @return bool Returns true on success, false on failure
     */
    public function update_application_status($applicationId, $status, $reviewedBy) {
        $applicationId = (int)$applicationId;
        $reviewedBy = (int)$reviewedBy;
        $status = $this->escape($status);
        
        if ($applicationId <= 0 || !in_array($status, ['approved', 'rejected'])) {
            return false;
        }
        
        $sql = "UPDATE seller_applications 
                SET status = '$status', 
                    reviewed_at = NOW(), 
                    reviewed_by = $reviewedBy 
                WHERE application_id = $applicationId";
        
        return $this->query($sql) !== false;
    }
    
    /**
     * Get all applications by status
     * 
     * @param string $status Status filter (pending/approved/rejected)
     * @return array Returns array of applications
     */
    public function get_applications_by_status($status) {
        $status = $this->escape($status);
        $sql = "SELECT sa.*, u.name, u.email 
                FROM seller_applications sa
                JOIN users u ON sa.user_id = u.user_id
                WHERE sa.status = '$status'
                ORDER BY sa.submitted_at DESC";
        return $this->fetchAll($sql);
    }
    
    /**
     * Get application count by status
     * 
     * @param string|null $status Status filter (optional)
     * @return int Total count
     */
    public function get_application_count($status = null) {
        $sql = "SELECT COUNT(*) as total FROM seller_applications";
        
        if ($status !== null) {
            $status = $this->escape($status);
            $sql .= " WHERE status = '$status'";
        }
        
        $result = $this->fetchOne($sql);
        return $result ? (int)$result['total'] : 0;
    }
}

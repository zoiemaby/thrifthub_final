<?php
/**
 * Seller Class
 * ThriftHub - Seller Management Class
 * 
 * This class extends the Database class and provides methods
 * for managing sellers (create, update, retrieve, verify)
 */

require_once __DIR__ . '/../settings/db_class.php';

class Seller extends Database {
    
    /**
     * Create a new seller profile
     * 
     * @param int $userId User ID (FK to users.user_id)
     * @param string $shopName Store name
     * @param int $typeId Business type ID (FK to business_types)
     * @param int $sectorId Sector ID (FK to sectors)
     * @param string|null $logo Store logo path
     * @param string|null $banner Store banner path
     * @param string|null $description Store description
     * @return bool Returns true on success, false on failure
     */
    public function create_seller($userId, $shopName, $typeId, $sectorId, $logo = null, $banner = null, $description = null) {
        $userId = (int)$userId;
        $typeId = (int)$typeId;
        $sectorId = (int)$sectorId;
        
        if ($userId <= 0 || $typeId <= 0 || $sectorId <= 0) {
            return false;
        }
        
        $shopName = $this->escape(trim($shopName));
        $logo = $logo ? "'" . $this->escape($logo) . "'" : 'NULL';
        $banner = $banner ? "'" . $this->escape($banner) . "'" : 'NULL';
        $description = $description ? "'" . $this->escape($description) . "'" : 'NULL';
        
        // Check if seller already exists
        if ($this->get_seller_by_user_id($userId)) {
            return false;
        }
        
        $sql = "INSERT INTO sellers (user_id, shop_name, type_id, sector_id, store_logo, store_banner, description, verified) 
                VALUES ($userId, '$shopName', $typeId, $sectorId, $logo, $banner, $description, 0)";
        
        return $this->query($sql) !== false;
    }
    
    /**
     * Get seller profile by user ID
     * 
     * @param int $userId User ID
     * @return array|false Returns seller data or false if not found
     */
    public function get_seller_by_user_id($userId) {
        $userId = (int)$userId;
        $sql = "SELECT s.*, bt.type_description, sec.sector_description 
                FROM sellers s
                LEFT JOIN business_types bt ON s.type_id = bt.type_id
                LEFT JOIN sectors sec ON s.sector_id = sec.sector_id
                WHERE s.user_id = $userId";
        return $this->fetchOne($sql);
    }
    
    /**
     * Update seller profile
     * 
     * @param int $userId User ID
     * @param string $shopName Store name
     * @param int $typeId Business type ID
     * @param int $sectorId Sector ID
     * @param string|null $logo Store logo path
     * @param string|null $banner Store banner path
     * @param string|null $description Store description
     * @return bool Returns true on success, false on failure
     */
    public function update_seller_profile($userId, $shopName, $typeId, $sectorId, $logo = null, $banner = null, $description = null) {
        $userId = (int)$userId;
        $typeId = (int)$typeId;
        $sectorId = (int)$sectorId;
        
        if ($userId <= 0) {
            return false;
        }
        
        // Check if seller exists
        if (!$this->get_seller_by_user_id($userId)) {
            return false;
        }
        
        $shopName = $this->escape(trim($shopName));
        $description = $description ? "'" . $this->escape($description) . "'" : 'NULL';
        
        $updateFields = [
            "shop_name = '$shopName'",
            "type_id = $typeId",
            "sector_id = $sectorId",
            "description = $description"
        ];
        
        // Only update logo/banner if provided
        if ($logo !== null) {
            $logo = $this->escape($logo);
            $updateFields[] = "store_logo = '$logo'";
        }
        
        if ($banner !== null) {
            $banner = $this->escape($banner);
            $updateFields[] = "store_banner = '$banner'";
        }
        
        $sql = "UPDATE sellers SET " . implode(', ', $updateFields) . " WHERE user_id = $userId";
        
        return $this->query($sql) !== false;
    }
    
    /**
     * Set seller verification status
     * 
     * @param int $userId User ID
     * @param int $verified Verified status (0 or 1)
     * @return bool Returns true on success, false on failure
     */
    public function set_seller_verified($userId, $verified) {
        $userId = (int)$userId;
        $verified = (int)$verified;
        
        if ($userId <= 0) {
            return false;
        }
        
        $sql = "UPDATE sellers SET verified = $verified WHERE user_id = $userId";
        
        return $this->query($sql) !== false;
    }
    
    /**
     * Get all sellers
     * 
     * @param int|null $limit Limit number of results
     * @param int|null $offset Offset for pagination
     * @return array Returns array of seller data
     */
    public function get_all_sellers($limit = null, $offset = null) {
        $sql = "SELECT s.*, u.name, u.email, bt.type_description, sec.sector_description 
                FROM sellers s
                JOIN users u ON s.user_id = u.user_id
                LEFT JOIN business_types bt ON s.type_id = bt.type_id
                LEFT JOIN sectors sec ON s.sector_id = sec.sector_id
                ORDER BY s.user_id DESC";
        
        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = $offset !== null ? (int)$offset : 0;
            $sql .= " LIMIT $offset, $limit";
        }
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Get verified sellers only
     * 
     * @param int|null $limit Limit number of results
     * @param int|null $offset Offset for pagination
     * @return array Returns array of verified seller data
     */
    public function get_verified_sellers($limit = null, $offset = null) {
        $sql = "SELECT s.*, u.name, u.email, bt.type_description, sec.sector_description 
                FROM sellers s
                JOIN users u ON s.user_id = u.user_id
                LEFT JOIN business_types bt ON s.type_id = bt.type_id
                LEFT JOIN sectors sec ON s.sector_id = sec.sector_id
                WHERE s.verified = 1
                ORDER BY s.user_id DESC";
        
        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = $offset !== null ? (int)$offset : 0;
            $sql .= " LIMIT $offset, $limit";
        }
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Check if user is a verified seller
     * 
     * @param int $userId User ID
     * @return bool Returns true if verified seller, false otherwise
     */
    public function is_seller_verified($userId) {
        $seller = $this->get_seller_by_user_id($userId);
        return $seller && $seller['verified'] == 1;
    }
    
    /**
     * Get seller count
     * 
     * @param bool|null $verifiedOnly Count only verified sellers
     * @return int Total number of sellers
     */
    public function get_seller_count($verifiedOnly = null) {
        $sql = "SELECT COUNT(*) as total FROM sellers";
        
        if ($verifiedOnly === true) {
            $sql .= " WHERE verified = 1";
        }
        
        $result = $this->fetchOne($sql);
        return $result ? (int)$result['total'] : 0;
    }
}

<?php

/**
 * Brand Class
 * ThriftHub - Brand Management Class
 * 
 * This class extends the Database class and provides methods
 * for managing brands (add, edit, delete, retrieve, etc.)
 */

require_once __DIR__ . '/../settings/db_class.php';

class Brand extends Database
{

    /**
     * Add a new brand to the database
     * 
     * @param string $brandName Brand name
     * @param int $userId User ID of the creator
     * @return array Returns ['success' => bool, 'error' => string, 'brand_id' => int]
     */
    public function addBrand($brandName, $userId)
    {
        $brandName = trim($brandName);
        $userId = (int)$userId;

        if (empty($brandName) || $userId <= 0) {
            return ['success' => false, 'error' => 'invalid_input'];
        }

        // Check if brand name exists for this user (names must be unique per user)
        // Use the original name (before escaping) for the check, as brandExists will escape it
        $exists = $this->brandExists($brandName, null, $userId);
        if ($exists) {
            error_log("Brand::addBrand - Duplicate detected: '$brandName' for user $userId");
            return ['success' => false, 'error' => 'duplicate'];
        }

        // Escape after the check
        $brandName = $this->escape($brandName);

        $sql = "INSERT INTO brands (brand_name, user_id) VALUES ('$brandName', $userId)";
        $result = $this->query($sql);

        // Check for duplicate entry error (MySQL error 1062)
        $conn = $this->getConnection();
        if (!$result && $conn && $conn->errno == 1062) {
            // Duplicate entry - brand name already exists
            error_log("Brand::addBrand - Duplicate entry error: '$brandName' for user $userId");
            return ['success' => false, 'error' => 'duplicate'];
        }

        if ($result) {
            $insertId = $this->insert_id();
            if ($insertId > 0) {
                return ['success' => true, 'brand_id' => $insertId];
            } else {
                // Fallback: get the last inserted ID by querying
                $checkSql = "SELECT brand_id FROM brands WHERE brand_name = '$brandName' AND user_id = $userId ORDER BY brand_id DESC LIMIT 1";
                $checkResult = $this->fetchOne($checkSql);
                if ($checkResult && isset($checkResult['brand_id'])) {
                    return ['success' => true, 'brand_id' => (int)$checkResult['brand_id']];
                }
                error_log("Brand::addBrand - Insert succeeded but insert_id() returned 0. Brand: $brandName, User: $userId");
                return ['success' => false, 'error' => 'insert_failed'];
            }
        } else {
            // Check if error is due to missing user_id column
            $conn = $this->getConnection();
            if ($conn && $conn->errno == 1054) { // Unknown column error
                error_log("Brand::addBrand - Database error: Column 'user_id' may not exist. Please run the migration: db/migrations/add_user_id_to_brands.sql");
                return ['success' => false, 'error' => 'missing_column'];
            } else if ($conn) {
                error_log("Brand::addBrand - Database error: " . $conn->error);
                return ['success' => false, 'error' => 'database_error', 'db_error' => $conn->error];
            }
            return ['success' => false, 'error' => 'insert_failed'];
        }
    }

    /**
     * Edit/Update brand name
     * 
     * @param int $brandId Brand ID to update
     * @param string $brandName New brand name
     * @param int $userId User ID (to verify ownership)
     * @return bool Returns true on success, false on failure
     */
    public function editBrand($brandId, $brandName, $userId = null)
    {
        // Escape inputs
        $brandId = (int)$brandId;
        $brandName = trim($brandName);
        $brandName = $this->escape($brandName);

        if ($brandId <= 0 || empty($brandName)) {
            return false;
        }

        // Check if brand exists
        $brand = $this->getBrand($brandId);
        if (!$brand) {
            return false;
        }

        // Verify ownership if userId provided
        if ($userId !== null) {
            $userId = (int)$userId;
            if ($brand['user_id'] != $userId) {
                return false; // User doesn't own this brand
            }
        }

        // Check if new name already exists for this user (excluding current brand)
        if ($this->brandExists($brandName, $brandId, $brand['user_id'])) {
            return false; // Brand name already exists
        }

        // Update brand
        $sql = "UPDATE brands SET brand_name = '$brandName' WHERE brand_id = $brandId";

        return $this->query($sql) !== false;
    }

    /**
     * Delete a brand from the database
     * 
     * @param int $brandId Brand ID to delete
     * @param int $userId User ID (to verify ownership)
     * @return bool Returns true on success, false on failure
     */
    public function deleteBrand($brandId, $userId = null)
    {
        // Escape brand ID
        $brandId = (int)$brandId;

        if ($brandId <= 0) {
            return false;
        }

        // Check if brand exists
        $brand = $this->getBrand($brandId);
        if (!$brand) {
            return false;
        }

        // Verify ownership if userId provided
        if ($userId !== null) {
            $userId = (int)$userId;
            if ($brand['user_id'] != $userId) {
                return false; // User doesn't own this brand
            }
        }

        // Delete brand
        $sql = "DELETE FROM brands WHERE brand_id = $brandId";

        return $this->query($sql) !== false;
    }

    /**
     * Get a single brand by ID
     * 
     * @param int $brandId Brand ID
     * @param int $userId Optional user ID to filter by
     * @return array|false Returns brand data as associative array, or false if not found
     */
    public function getBrand($brandId, $userId = null)
    {
        $brandId = (int)$brandId;
        $sql = "SELECT * FROM brands WHERE brand_id = $brandId";

        if ($userId !== null) {
            $userId = (int)$userId;
            $sql .= " AND user_id = $userId";
        }

        return $this->fetchOne($sql);
    }

    /**
     * Get all brands
     * 
     * @param int $userId Optional user ID to filter by
     * @param int $limit Limit number of results (optional)
     * @param int $offset Offset for pagination (optional)
     * @return array Returns array of brand data
     */
    public function getAllBrands($userId = null, $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM brands";


        $sql .= " ORDER BY brand_name ASC";

        // Add limit and offset if specified
        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = $offset !== null ? (int)$offset : 0;
            $sql .= " LIMIT $offset, $limit";
        }

        return $this->fetchAll($sql);
    }

    /**
     * Get brands owned by a specific user
     *
     * @param int $userId User ID
     * @return array Brands for user
     */
    public function getBrandsByUser($userId)
    {
        $userId = (int)$userId;
        if ($userId <= 0) {
            return [];
        }

        $sql = "SELECT * FROM brands WHERE user_id = $userId ORDER BY brand_name ASC";
        return $this->fetchAll($sql) ?: [];
    }

    /**
     * Check if brand name already exists
     * 
     * @param string $brandName Brand name to check
     * @param int $excludeBrandId Brand ID to exclude from check (for updates)
     * @param int $userId Optional user ID to filter by (for per-user uniqueness)
     * @return bool Returns true if brand exists, false otherwise
     */
    public function brandExists($brandName, $excludeBrandId = null, $userId = null)
    {
        // Trim and normalize the brand name
        $brandName = trim($brandName);
        if (empty($brandName)) {
            return false;
        }

        $brandName = $this->escape($brandName);
        // Use LOWER() for case-insensitive comparison and TRIM to handle whitespace
        $sql = "SELECT brand_id FROM brands WHERE LOWER(TRIM(brand_name)) = LOWER(TRIM('$brandName'))";

        if ($userId !== null) {
            $userId = (int)$userId;
            $sql .= " AND user_id = $userId";
        }

        if ($excludeBrandId !== null) {
            $excludeBrandId = (int)$excludeBrandId;
            $sql .= " AND brand_id != $excludeBrandId";
        }

        $result = $this->fetchOne($sql);

        // Check for database errors (like missing column)
        $conn = $this->getConnection();
        if ($conn && $conn->errno) {
            if ($conn->errno == 1054 && $userId !== null) {
                // user_id column doesn't exist - check without user filter for backward compatibility
                error_log("Brand::brandExists - user_id column missing, checking globally for: '$brandName'");
                $fallbackSql = "SELECT brand_id FROM brands WHERE LOWER(TRIM(brand_name)) = LOWER(TRIM('$brandName'))";
                if ($excludeBrandId !== null) {
                    $excludeBrandId = (int)$excludeBrandId;
                    $fallbackSql .= " AND brand_id != $excludeBrandId";
                }
                $fallbackResult = $this->fetchOne($fallbackSql);
                $exists = $fallbackResult !== false;
                if ($exists) {
                    error_log("Brand::brandExists - Found duplicate (global check): '$brandName'");
                }
                return $exists;
            } else {
                error_log("Brand::brandExists - Database error: " . $conn->error);
                // On error, assume no duplicate to avoid blocking valid inserts
                return false;
            }
        }

        $exists = $result !== false;
        if ($exists) {
            error_log("Brand::brandExists - Found duplicate: '$brandName' for user $userId");
        }

        return $exists;
    }

    /**
     * Get total count of brands
     * 
     * @return int Total number of brands
     */
    public function getBrandCount()
    {
        $sql = "SELECT COUNT(*) as total FROM brands";
        $result = $this->fetchOne($sql);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Search brands by name
     * 
     * @param string $searchTerm Search term
     * @return array Returns array of matching brands
     */
    public function searchBrands($searchTerm)
    {
        $searchTerm = $this->escape($searchTerm);
        $sql = "SELECT * FROM brands 
                WHERE brand_name LIKE '%$searchTerm%'
                ORDER BY brand_name ASC";

        return $this->fetchAll($sql);
    }
}

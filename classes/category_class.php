<?php
/**
 * Category Class
 * ThriftHub - Category Management Class
 */

require_once __DIR__ . '/../settings/db_class.php';

class Category extends Database {
    /**
     * Add a new category
     * @param string $catName
     * @param int $userId User ID of the creator
     * @return int|false
     */
    public function addCategory($catName, $userId) {
        $catName = trim($catName);
        $userId = (int)$userId;

        if (empty($catName) || $userId <= 0) {
            return ['success' => false, 'error' => 'invalid_input'];
        }
        
        // Check if category name exists for this user (names must be unique per user)
        // Use the original name (before escaping) for the check, as categoryExists will escape it
        $exists = $this->categoryExists($catName, null, $userId);
        if ($exists) {
            error_log("Category::addCategory - Duplicate detected: '$catName' for user $userId");
            return ['success' => false, 'error' => 'duplicate'];
        }
        
        // Escape after the check
        $catName = $this->escape($catName);

        $sql = "INSERT INTO categories (cat_name, user_id) VALUES ('$catName', $userId)";
        $result = $this->query($sql);
        
        // Check for duplicate entry error (MySQL error 1062)
        $conn = $this->getConnection();
        if (!$result && $conn && $conn->errno == 1062) {
            // Duplicate entry - category name already exists
            error_log("Category::addCategory - Duplicate entry error: '$catName' for user $userId");
            return ['success' => false, 'error' => 'duplicate'];
        }
        
        if ($result) {
            $insertId = $this->insert_id();
            // insert_id() can return 0 in some edge cases, but should work with AUTO_INCREMENT
            // If it's 0, check if the insert actually succeeded by querying the last row
            if ($insertId > 0) {
                return ['success' => true, 'cat_id' => $insertId];
            } else {
                // Fallback: get the last inserted ID by querying
                $checkSql = "SELECT cat_id FROM categories WHERE cat_name = '$catName' AND user_id = $userId ORDER BY cat_id DESC LIMIT 1";
                $checkResult = $this->fetchOne($checkSql);
                if ($checkResult && isset($checkResult['cat_id'])) {
                    return ['success' => true, 'cat_id' => (int)$checkResult['cat_id']];
                }
                // If still no ID, log error
                error_log("Category::addCategory - Insert succeeded but insert_id() returned 0. Category: $catName, User: $userId");
                return ['success' => false, 'error' => 'insert_failed'];
            }
        } else {
            // Check if error is due to missing user_id column
            $conn = $this->getConnection();
            if ($conn && $conn->errno == 1054) { // Unknown column error
                error_log("Category::addCategory - Database error: Column 'user_id' may not exist. Please run the migration: db/migrations/add_user_id_to_categories.sql");
                return ['success' => false, 'error' => 'missing_column'];
            } else if ($conn) {
                error_log("Category::addCategory - Database error: " . $conn->error);
                return ['success' => false, 'error' => 'database_error', 'db_error' => $conn->error];
            }
            return ['success' => false, 'error' => 'insert_failed'];
        }
    }

    /**
     * Edit category
     * @param int $catId Category ID
     * @param string $catName New category name
     * @param int $userId User ID (to verify ownership)
     * @return bool
     */
    public function editCategory($catId, $catName, $userId = null) {
        $catId = (int)$catId;
        $catName = trim($catName);
        $catName = $this->escape($catName);

        if ($catId <= 0 || empty($catName)) return false;
        
        // Verify category exists and belongs to user (if userId provided)
        $category = $this->getCategory($catId);
        if (!$category) return false;
        
        if ($userId !== null) {
            $userId = (int)$userId;
            if ($category['user_id'] != $userId) return false; // User doesn't own this category
        }
        
        // Check if new name already exists for this user (excluding current category)
        if ($this->categoryExists($catName, $catId, $category['user_id'])) return false;

        $sql = "UPDATE categories SET cat_name = '$catName' WHERE cat_id = $catId";
        return $this->query($sql) !== false;
    }

    /**
     * Delete category
     * @param int $catId Category ID
     * @param int $userId User ID (to verify ownership)
     * @return bool
     */
    public function deleteCategory($catId, $userId = null) {
        $catId = (int)$catId;
        if ($catId <= 0) return false;
        
        $category = $this->getCategory($catId);
        if (!$category) return false;
        
        // Verify ownership if userId provided
        if ($userId !== null) {
            $userId = (int)$userId;
            if ($category['user_id'] != $userId) return false; // User doesn't own this category
        }
        
        $sql = "DELETE FROM categories WHERE cat_id = $catId";
        return $this->query($sql) !== false;
    }

    public function getCategory($catId) {
        $catId = (int)$catId;
        $sql = "SELECT * FROM categories WHERE cat_id = $catId";
        return $this->fetchOne($sql);
    }

    /**
     * Get all categories (optionally filtered by user)
     * @param int|null $userId Optional user ID to filter by
     * @return array
     */
    public function getAllCategories($userId = null) {
        $sql = "SELECT * FROM categories";
        if ($userId !== null) {
            $userId = (int)$userId;
            $sql .= " WHERE user_id = $userId";
        }
        $sql .= " ORDER BY cat_name ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Get categories created by a specific user
     * @param int $userId User ID
     * @return array
     */
    public function getCategoriesByUser($userId) {
        $userId = (int)$userId;
        if ($userId <= 0) return [];
        
        $sql = "SELECT * FROM categories WHERE user_id = $userId ORDER BY cat_name ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Check if category name exists
     * @param string $catName Category name
     * @param int|null $excludeId Category ID to exclude from check
     * @param int|null $userId User ID to check within (category names must be unique per user)
     * @return bool
     */
    public function categoryExists($catName, $excludeId = null, $userId = null) {
        // Trim and normalize the category name
        $catName = trim($catName);
        if (empty($catName)) {
            return false;
        }
        
        $catName = $this->escape($catName);
        // Use LOWER() for case-insensitive comparison and TRIM to handle whitespace
        $sql = "SELECT cat_id FROM categories WHERE LOWER(TRIM(cat_name)) = LOWER(TRIM('$catName'))";
        
        if ($userId !== null) {
            $userId = (int)$userId;
            $sql .= " AND user_id = $userId";
        }
        
        if ($excludeId !== null) {
            $excludeId = (int)$excludeId;
            $sql .= " AND cat_id != $excludeId";
        }
        
        $result = $this->fetchOne($sql);
        
        // Check for database errors (like missing column)
        $conn = $this->getConnection();
        if ($conn && $conn->errno) {
            if ($conn->errno == 1054 && $userId !== null) {
                // user_id column doesn't exist - check without user filter for backward compatibility
                error_log("Category::categoryExists - user_id column missing, checking globally for: '$catName'");
                $fallbackSql = "SELECT cat_id FROM categories WHERE LOWER(TRIM(cat_name)) = LOWER(TRIM('$catName'))";
                if ($excludeId !== null) {
                    $excludeId = (int)$excludeId;
                    $fallbackSql .= " AND cat_id != $excludeId";
                }
                $fallbackResult = $this->fetchOne($fallbackSql);
                $exists = $fallbackResult !== false;
                if ($exists) {
                    error_log("Category::categoryExists - Found duplicate (global check): '$catName'");
                }
                return $exists;
            } else {
                error_log("Category::categoryExists - Database error: " . $conn->error);
                // On error, assume no duplicate to avoid blocking valid inserts
                return false;
            }
        }
        
        $exists = $result !== false;
        if ($exists) {
            error_log("Category::categoryExists - Found duplicate: '$catName' for user $userId");
        }
        
        return $exists;
    }

    /**
     * Get category count (optionally filtered by user)
     * @param int|null $userId Optional user ID to filter by
     * @return int
     */
    public function getCategoryCount($userId = null) {
        $sql = "SELECT COUNT(*) as total FROM categories";
        if ($userId !== null) {
            $userId = (int)$userId;
            $sql .= " WHERE user_id = $userId";
        }
        $result = $this->fetchOne($sql);
        return $result ? (int)$result['total'] : 0;
    }
}


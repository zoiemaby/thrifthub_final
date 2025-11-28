<?php
/**
 * Product Class
 * ThriftHub - Product Management Class
 * 
 * This class extends the Database class and provides methods
 * for managing products (add, edit, delete, retrieve, etc.)
 */

require_once __DIR__ . '/../settings/db_class.php';

class Product extends Database {
    
    /**
     * Add a new product to the database
     * 
     * @param array $data Product data (product_cat, product_brand, seller_id, product_title, product_price, product_desc, product_keywords, product_condition)
     * @return array Returns ['success' => bool, 'error' => string, 'product_id' => int]
     */
    public function addProduct($data) {
        // Validate required fields
        if (empty($data['product_title']) || empty($data['product_cat']) || 
            !isset($data['product_price']) || empty($data['seller_id'])) {
            return ['success' => false, 'error' => 'invalid_input', 'message' => 'Required fields missing.'];
        }
        
        // Sanitize and escape inputs
        $productCat = (int)$data['product_cat'];
        $productBrand = isset($data['product_brand']) && !empty($data['product_brand']) ? (int)$data['product_brand'] : null;
        $sellerId = (int)$data['seller_id'];
        $productTitle = $this->escape(trim($data['product_title']));
        $productPrice = (float)$data['product_price'];
        $productDesc = isset($data['product_desc']) ? $this->escape(trim($data['product_desc'])) : null;
        $productKeywords = isset($data['product_keywords']) ? $this->escape(trim($data['product_keywords'])) : null;
        $productCondition = isset($data['product_condition']) ? $this->escape($data['product_condition']) : 'good';
        
        // Validate price
        if ($productPrice <= 0) {
            return ['success' => false, 'error' => 'invalid_price', 'message' => 'Product price must be greater than 0.'];
        }
        
        // Validate category exists
        $catCheck = $this->fetchOne("SELECT cat_id FROM categories WHERE cat_id = $productCat");
        if (!$catCheck) {
            return ['success' => false, 'error' => 'invalid_category', 'message' => 'Selected category does not exist.'];
        }
        
        // Validate brand exists if provided
        if ($productBrand !== null) {
            $brandCheck = $this->fetchOne("SELECT brand_id FROM brands WHERE brand_id = $productBrand");
            if (!$brandCheck) {
                return ['success' => false, 'error' => 'invalid_brand', 'message' => 'Selected brand does not exist.'];
            }
        }
        
        // Build SQL query
        $brandValue = $productBrand !== null ? $productBrand : 'NULL';
        $descValue = $productDesc !== null ? "'$productDesc'" : 'NULL';
        $keywordsValue = $productKeywords !== null ? "'$productKeywords'" : 'NULL';
        
        $sql = "INSERT INTO products (product_cat, product_brand, seller_id, product_title, product_price, product_desc, product_keywords, product_condition) 
                VALUES ($productCat, $brandValue, $sellerId, '$productTitle', $productPrice, $descValue, $keywordsValue, '$productCondition')";
        
        $result = $this->query($sql);
        
        if ($result) {
            $insertId = $this->insert_id();
            if ($insertId > 0) {
                return ['success' => true, 'product_id' => $insertId];
            } else {
                // Fallback: get the last inserted ID
                $checkSql = "SELECT product_id FROM products WHERE product_title = '$productTitle' AND seller_id = $sellerId ORDER BY product_id DESC LIMIT 1";
                $checkResult = $this->fetchOne($checkSql);
                if ($checkResult && isset($checkResult['product_id'])) {
                    return ['success' => true, 'product_id' => (int)$checkResult['product_id']];
                }
                error_log("Product::addProduct - Insert succeeded but insert_id() returned 0. Product: $productTitle, Seller: $sellerId");
                return ['success' => false, 'error' => 'insert_failed', 'message' => 'Failed to retrieve product ID.'];
            }
        } else {
            $conn = $this->getConnection();
            if ($conn) {
                error_log("Product::addProduct - Database error: " . $conn->error);
                return ['success' => false, 'error' => 'database_error', 'message' => $conn->error];
            }
            return ['success' => false, 'error' => 'insert_failed', 'message' => 'Failed to insert product.'];
        }
    }
    
    /**
     * Edit/Update product
     * 
     * @param int $productId Product ID to update
     * @param array $data Data to update
     * @param int $sellerId Seller ID to verify ownership
     * @return bool Returns true on success, false on failure
     */
    public function editProduct($productId, $data, $sellerId = null) {
        $productId = (int)$productId;
        
        if ($productId <= 0) {
            return false;
        }
        
        // Verify product exists and ownership if sellerId provided
        $product = $this->getProduct($productId);
        if (!$product) {
            return false;
        }
        
        if ($sellerId !== null) {
            $sellerId = (int)$sellerId;
            if ($product['seller_id'] != $sellerId) {
                return false; // Seller doesn't own this product
            }
        }
        
        // Build update query
        $updates = [];
        
        if (isset($data['product_cat'])) {
            $productCat = (int)$data['product_cat'];
            $updates[] = "product_cat = $productCat";
        }
        
        if (isset($data['product_brand'])) {
            $productBrand = !empty($data['product_brand']) ? (int)$data['product_brand'] : null;
            $updates[] = $productBrand !== null ? "product_brand = $productBrand" : "product_brand = NULL";
        }
        
        if (isset($data['product_title'])) {
            $productTitle = $this->escape(trim($data['product_title']));
            $updates[] = "product_title = '$productTitle'";
        }
        
        if (isset($data['product_price'])) {
            $productPrice = (float)$data['product_price'];
            $updates[] = "product_price = $productPrice";
        }
        
        if (isset($data['product_desc'])) {
            $productDesc = !empty($data['product_desc']) ? $this->escape(trim($data['product_desc'])) : null;
            $updates[] = $productDesc !== null ? "product_desc = '$productDesc'" : "product_desc = NULL";
        }
        
        if (isset($data['product_keywords'])) {
            $productKeywords = !empty($data['product_keywords']) ? $this->escape(trim($data['product_keywords'])) : null;
            $updates[] = $productKeywords !== null ? "product_keywords = '$productKeywords'" : "product_keywords = NULL";
        }
        
        if (isset($data['product_condition'])) {
            $productCondition = $this->escape($data['product_condition']);
            $updates[] = "product_condition = '$productCondition'";
        }
        
        if (isset($data['product_image'])) {
            $productImage = $this->escape(trim($data['product_image']));
            $updates[] = "product_image = '$productImage'";
        }
        
        if (empty($updates)) {
            return false; // No updates provided
        }
        
        $sql = "UPDATE products SET " . implode(', ', $updates) . " WHERE product_id = $productId";
        
        return $this->query($sql) !== false;
    }
    
    /**
     * Delete a product from the database
     * 
     * @param int $productId Product ID to delete
     * @param int $sellerId Seller ID to verify ownership
     * @return bool Returns true on success, false on failure
     */
    public function deleteProduct($productId, $sellerId = null) {
        $productId = (int)$productId;
        
        if ($productId <= 0) {
            return false;
        }
        
        // Verify product exists and ownership if sellerId provided
        $product = $this->getProduct($productId);
        if (!$product) {
            return false;
        }
        
        if ($sellerId !== null) {
            $sellerId = (int)$sellerId;
            if ($product['seller_id'] != $sellerId) {
                return false; // Seller doesn't own this product
            }
        }
        
        // Delete product
        $sql = "DELETE FROM products WHERE product_id = $productId";
        
        return $this->query($sql) !== false;
    }
    
    /**
     * Get a single product by ID
     * 
     * @param int $productId Product ID
     * @return array|false Returns product data as associative array, or false if not found
     */
    public function getProduct($productId) {
        $productId = (int)$productId;
        $sql = "SELECT p.*, 
                       c.cat_name, 
                       b.brand_name,
                       u.name as seller_name
                FROM products p
                LEFT JOIN categories c ON p.product_cat = c.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                LEFT JOIN sellers s ON p.seller_id = s.user_id
                LEFT JOIN users u ON s.user_id = u.user_id
                WHERE p.product_id = $productId";
        return $this->fetchOne($sql);
    }
    
    /**
     * Get all products with composite filters
     * 
     * @param int $sellerId Optional seller ID to filter by
     * @param int $categoryId Optional category ID to filter by
     * @param int $brandId Optional brand ID to filter by
     * @param int $limit Optional limit for pagination
     * @param int $offset Optional offset for pagination
     * @param array $additionalFilters Optional additional filters (price_min, price_max, condition, status)
     * @return array Returns array of product data
     */
    public function getAllProducts($sellerId = null, $categoryId = null, $brandId = null, $limit = null, $offset = null, $additionalFilters = []) {
        $sql = "SELECT p.*, 
                       c.cat_name, 
                       b.brand_name,
                       u.name as seller_name
                FROM products p
                LEFT JOIN categories c ON p.product_cat = c.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                LEFT JOIN sellers s ON p.seller_id = s.user_id
                LEFT JOIN users u ON s.user_id = u.user_id
                WHERE 1=1";
        
        // Filter by status (indexed column) - default to active for public browsing
        // Status is an ENUM, so we validate it's one of the allowed values
        $allowedStatuses = ['active', 'inactive', 'sold', 'hidden'];
        $status = isset($additionalFilters['status']) ? $additionalFilters['status'] : 'active';
        if (!in_array($status, $allowedStatuses)) {
            $status = 'active'; // Default to active if invalid
        }
        $status = $this->escape($status);
        $sql .= " AND p.product_status = '$status'";
        
        if ($sellerId !== null) {
            $sellerId = (int)$sellerId;
            $sql .= " AND p.seller_id = $sellerId";
        }
        
        if ($categoryId !== null) {
            $categoryId = (int)$categoryId;
            $sql .= " AND p.product_cat = $categoryId";
        }
        
        if ($brandId !== null) {
            $brandId = (int)$brandId;
            $sql .= " AND p.product_brand = $brandId";
        }
        
        // Price range filters
        if (isset($additionalFilters['price_min']) && is_numeric($additionalFilters['price_min'])) {
            $priceMin = (float)$additionalFilters['price_min'];
            $sql .= " AND p.product_price >= $priceMin";
        }
        
        if (isset($additionalFilters['price_max']) && is_numeric($additionalFilters['price_max'])) {
            $priceMax = (float)$additionalFilters['price_max'];
            $sql .= " AND p.product_price <= $priceMax";
        }
        
        // Condition filter
        if (isset($additionalFilters['condition']) && !empty($additionalFilters['condition'])) {
            $condition = $this->escape($additionalFilters['condition']);
            $sql .= " AND p.product_condition = '$condition'";
        }
        
        // Apply sorting
        $sort = isset($additionalFilters['sort']) ? $additionalFilters['sort'] : 'newest';
        switch($sort) {
            case 'price-low':
                $sql .= " ORDER BY p.product_price ASC, p.created_at DESC";
                break;
            case 'price-high':
                $sql .= " ORDER BY p.product_price DESC, p.created_at DESC";
                break;
            case 'rating':
                // For now, sort by newest (rating not implemented yet)
                $sql .= " ORDER BY p.created_at DESC, p.product_title ASC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY p.created_at DESC, p.product_title ASC";
                break;
        }
        
        // Add pagination if specified
        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = $offset !== null ? (int)$offset : 0;
            $sql .= " LIMIT $offset, $limit";
        }
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Search products by keyword with composite filters
     * Efficient search using indexed columns first, then text search
     * 
     * @param string $searchTerm Search keyword
     * @param array $filters Optional filters: category_id, brand_id, price_min, price_max, condition, status, limit, offset
     * @return array Returns array of matching products
     */
    public function searchProducts($searchTerm, $filters = []) {
        $searchTerm = trim($searchTerm);
        $searchTermEscaped = $this->escape($searchTerm);
        
        // Build efficient SQL query - filter by indexed columns first for performance
        // This ensures MySQL uses indexes before applying expensive text searches
        $sql = "SELECT p.*, 
                       c.cat_name, 
                       b.brand_name,
                       u.name as seller_name
                FROM products p
                LEFT JOIN categories c ON p.product_cat = c.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                LEFT JOIN sellers s ON p.seller_id = s.user_id
                LEFT JOIN users u ON s.user_id = u.user_id
                WHERE 1=1";
        
        // CRITICAL: Apply indexed filters FIRST to reduce result set before text search
        // This order matters for query optimization
        
        // 1. Filter by status (indexed column) - reduces result set significantly
        $status = isset($filters['status']) ? $this->escape($filters['status']) : 'active';
        $sql .= " AND p.product_status = '$status'";
        
        // 2. Filter by category (indexed column) - very selective, apply early
        if (isset($filters['category_id']) && !empty($filters['category_id'])) {
            $categoryId = (int)$filters['category_id'];
            $sql .= " AND p.product_cat = $categoryId";
        }
        
        // 3. Filter by brand (indexed column) - very selective, apply early
        if (isset($filters['brand_id']) && !empty($filters['brand_id'])) {
            $brandId = (int)$filters['brand_id'];
            $sql .= " AND p.product_brand = $brandId";
        }
        
        // 4. Filter by price range (indexed column) - efficient numeric comparison
        if (isset($filters['price_min']) && is_numeric($filters['price_min'])) {
            $priceMin = (float)$filters['price_min'];
            $sql .= " AND p.product_price >= $priceMin";
        }
        
        if (isset($filters['price_max']) && is_numeric($filters['price_max'])) {
            $priceMax = (float)$filters['price_max'];
            $sql .= " AND p.product_price <= $priceMax";
        }
        
        // 5. Filter by condition (enum, efficient)
        if (isset($filters['condition']) && !empty($filters['condition'])) {
            $condition = $this->escape($filters['condition']);
            $sql .= " AND p.product_condition = '$condition'";
        }
        
        // 6. Text search (applied LAST after indexed filters reduce result set)
        // This is the most expensive operation, so we do it after filtering
        if (!empty($searchTerm)) {
            // Optimized keyword search:
            // - Search in title first (most relevant, usually shorter)
            // - Then keywords (indexed terms)
            // - Then description (longest, least efficient)
            // - Also search brand names for composite searches like "Nike footwear"
            
            // Split search term into words for better matching
            $searchWords = explode(' ', $searchTerm);
            $searchWords = array_filter(array_map('trim', $searchWords));
            
            if (count($searchWords) > 0) {
                // Build search conditions - use OR for any word match (broader search)
                $searchConditions = [];
                
                foreach ($searchWords as $word) {
                    $wordEscaped = $this->escape($word);
                    // Use LIKE with word boundaries for better performance
                    // Searching title first (most relevant)
                    $searchConditions[] = "p.product_title LIKE '%$wordEscaped%'";
                    // Keywords are usually indexed terms
                    $searchConditions[] = "p.product_keywords LIKE '%$wordEscaped%'";
                    // Brand name search for composite searches (e.g., "Nike footwear")
                    $searchConditions[] = "b.brand_name LIKE '%$wordEscaped%'";
                    // Category name search (e.g., "footwear")
                    $searchConditions[] = "c.cat_name LIKE '%$wordEscaped%'";
                }
                
                // Combine with OR - any word can match
                $sql .= " AND (" . implode(' OR ', $searchConditions) . ")";
            } else {
                // Fallback to simple search if word splitting fails
                $sql .= " AND (p.product_title LIKE '%$searchTermEscaped%' 
                               OR p.product_keywords LIKE '%$searchTermEscaped%'
                               OR b.brand_name LIKE '%$searchTermEscaped%'
                               OR c.cat_name LIKE '%$searchTermEscaped%')";
            }
        }
        
        // Order by relevance: exact matches first, then partial matches
        if (!empty($searchTerm)) {
            // Calculate relevance score for better results
            $searchTermLower = strtolower($searchTerm);
            $sql .= " ORDER BY 
                        CASE 
                            WHEN LOWER(p.product_title) = '$searchTermLower' THEN 1
                            WHEN LOWER(p.product_title) LIKE '$searchTermLower%' THEN 2
                            WHEN LOWER(p.product_title) LIKE '%$searchTermLower%' THEN 3
                            WHEN LOWER(p.product_keywords) LIKE '%$searchTermLower%' THEN 4
                            WHEN LOWER(b.brand_name) LIKE '%$searchTermLower%' THEN 5
                            WHEN LOWER(c.cat_name) LIKE '%$searchTermLower%' THEN 6
                            ELSE 7
                        END,
                        p.created_at DESC";
        } else {
            $sql .= " ORDER BY p.created_at DESC, p.product_title ASC";
        }
        
        // Add pagination if specified (LIMIT applied last for efficiency)
        if (isset($filters['limit']) && $filters['limit'] !== null) {
            $limit = (int)$filters['limit'];
            $offset = isset($filters['offset']) && $filters['offset'] !== null ? (int)$filters['offset'] : 0;
            $sql .= " LIMIT $offset, $limit";
        }
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Get total count of products matching search criteria (for pagination)
     * Efficient count query without fetching all data
     * 
     * @param string $searchTerm Search keyword
     * @param array $filters Optional filters
     * @return int Total count
     */
    public function searchProductsCount($searchTerm, $filters = []) {
        $searchTerm = trim($searchTerm);
        $searchTermEscaped = $this->escape($searchTerm);
        
        // Efficient count query - same filter order as searchProducts for consistency
        $sql = "SELECT COUNT(*) as total
                FROM products p
                LEFT JOIN categories c ON p.product_cat = c.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                WHERE 1=1";
        
        // Apply same indexed filters first (same order as searchProducts)
        $status = isset($filters['status']) ? $this->escape($filters['status']) : 'active';
        $sql .= " AND p.product_status = '$status'";
        
        if (isset($filters['category_id']) && !empty($filters['category_id'])) {
            $categoryId = (int)$filters['category_id'];
            $sql .= " AND p.product_cat = $categoryId";
        }
        
        if (isset($filters['brand_id']) && !empty($filters['brand_id'])) {
            $brandId = (int)$filters['brand_id'];
            $sql .= " AND p.product_brand = $brandId";
        }
        
        if (isset($filters['price_min']) && is_numeric($filters['price_min'])) {
            $priceMin = (float)$filters['price_min'];
            $sql .= " AND p.product_price >= $priceMin";
        }
        
        if (isset($filters['price_max']) && is_numeric($filters['price_max'])) {
            $priceMax = (float)$filters['price_max'];
            $sql .= " AND p.product_price <= $priceMax";
        }
        
        if (isset($filters['condition']) && !empty($filters['condition'])) {
            $condition = $this->escape($filters['condition']);
            $sql .= " AND p.product_condition = '$condition'";
        }
        
        // Apply same optimized text search as searchProducts
        if (!empty($searchTerm)) {
            $searchWords = explode(' ', $searchTerm);
            $searchWords = array_filter(array_map('trim', $searchWords));
            
            if (count($searchWords) > 0) {
                $searchConditions = [];
                
                foreach ($searchWords as $word) {
                    $wordEscaped = $this->escape($word);
                    $searchConditions[] = "p.product_title LIKE '%$wordEscaped%'";
                    $searchConditions[] = "p.product_keywords LIKE '%$wordEscaped%'";
                    $searchConditions[] = "b.brand_name LIKE '%$wordEscaped%'";
                    $searchConditions[] = "c.cat_name LIKE '%$wordEscaped%'";
                }
                
                $sql .= " AND (" . implode(' OR ', $searchConditions) . ")";
            } else {
                $sql .= " AND (p.product_title LIKE '%$searchTermEscaped%' 
                               OR p.product_keywords LIKE '%$searchTermEscaped%'
                               OR b.brand_name LIKE '%$searchTermEscaped%'
                               OR c.cat_name LIKE '%$searchTermEscaped%')";
            }
        }
        
        $result = $this->fetchOne($sql);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Get products by seller
     * 
     * @param int $sellerId Seller ID
     * @return array Products for seller
     */
    public function getProductsBySeller($sellerId) {
        return $this->getAllProducts($sellerId);
    }
    
    /**
     * Get products organized by category and brand
     * 
     * @param int $sellerId Optional seller ID to filter by
     * @return array Products organized by category and brand
     */
    public function getProductsOrganized($sellerId = null) {
        $products = $this->getAllProducts($sellerId);
        
        $organized = [];
        foreach ($products as $product) {
            $catId = $product['product_cat'];
            $catName = $product['cat_name'] ?? 'Uncategorized';
            $brandId = $product['product_brand'] ?? 'none';
            $brandName = $product['brand_name'] ?? 'No Brand';
            
            if (!isset($organized[$catId])) {
                $organized[$catId] = [
                    'cat_id' => $catId,
                    'cat_name' => $catName,
                    'brands' => []
                ];
            }
            
            if (!isset($organized[$catId]['brands'][$brandId])) {
                $organized[$catId]['brands'][$brandId] = [
                    'brand_id' => $brandId === 'none' ? null : $brandId,
                    'brand_name' => $brandName,
                    'products' => []
                ];
            }
            
            $organized[$catId]['brands'][$brandId]['products'][] = $product;
        }
        
        return $organized;
    }
}


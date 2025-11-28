<?php
/**
 * Product Controller
 * ThriftHub - Product Management Controller
 * 
 * This controller handles HTTP requests for product operations
 * and acts as an interface between the view/actions and the Product class.
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../classes/product_class.php';

class ProductController {
    private $product;
    
    /**
     * Constructor - Initialize Product class instance
     */
    public function __construct() {
        $this->product = new Product();
    }
    
    /**
     * Handle product creation (add_product_ctr)
     * 
     * @param array $data Product data
     * @return array Response array with success status and message/data
     */
    public function add_product_ctr($data) {
        return $this->addProduct($data);
    }
    
    /**
     * Handle product creation
     * 
     * @param array $data Product data
     * @return array Response array with success status and message/data
     */
    public function addProduct($data) {
        // Validate required fields
        if (empty($data['product_title'])) {
            return [
                'success' => false,
                'message' => 'Product title is required.'
            ];
        }
        
        if (empty($data['product_cat']) || !is_numeric($data['product_cat'])) {
            return [
                'success' => false,
                'message' => 'Product category is required.'
            ];
        }
        
        if (empty($data['product_price']) || !is_numeric($data['product_price']) || $data['product_price'] <= 0) {
            return [
                'success' => false,
                'message' => 'Valid product price is required.'
            ];
        }
        
        // Get seller_id from data or session
        $sellerId = isset($data['seller_id']) ? (int)$data['seller_id'] : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0);
        if ($sellerId <= 0) {
            return [
                'success' => false,
                'message' => 'Seller ID is required.'
            ];
        }
        
        // Validate product title length
        $productTitle = trim($data['product_title']);
        if (strlen($productTitle) < 3 || strlen($productTitle) > 200) {
            return [
                'success' => false,
                'message' => 'Product title must be between 3 and 200 characters.'
            ];
        }
        
        // Add product
        $result = $this->product->addProduct($data);
        
        if (is_array($result)) {
            if ($result['success']) {
                return [
                    'success' => true,
                    'message' => 'Product created successfully.',
                    'product_id' => $result['product_id']
                ];
            } else {
                $error = $result['error'] ?? 'unknown';
                $message = $result['message'] ?? 'Product creation failed.';
                
                return [
                    'success' => false,
                    'message' => $message
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Product creation failed. Unknown error.'
        ];
    }
    
    /**
     * Handle product update
     * 
     * @param int $productId Product ID to update
     * @param array $data Data to update
     * @return array Response array with success status and message
     */
    public function updateProduct($productId, $data) {
        // Validate product ID
        if (empty($productId) || !is_numeric($productId)) {
            return [
                'success' => false,
                'message' => 'Invalid product ID.'
            ];
        }
        
        // Get seller_id from data or session for ownership verification
        $sellerId = isset($data['seller_id']) ? (int)$data['seller_id'] : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null);
        
        // Validate product title if provided
        if (isset($data['product_title'])) {
            $productTitle = trim($data['product_title']);
            if (strlen($productTitle) < 3 || strlen($productTitle) > 200) {
                return [
                    'success' => false,
                    'message' => 'Product title must be between 3 and 200 characters.'
                ];
            }
        }
        
        // Validate price if provided
        if (isset($data['product_price'])) {
            $productPrice = (float)$data['product_price'];
            if ($productPrice <= 0) {
                return [
                    'success' => false,
                    'message' => 'Product price must be greater than 0.'
                ];
            }
        }
        
        // Update product
        $result = $this->product->editProduct($productId, $data, $sellerId);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Product updated successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Update failed. Product may not exist or you may not own it.'
            ];
        }
    }
    
    /**
     * Handle product deletion
     * 
     * @param int $productId Product ID to delete
     * @param array $data Optional data containing seller_id
     * @return array Response array with success status and message
     */
    public function deleteProduct($productId, $data = []) {
        // Validate product ID
        if (empty($productId) || !is_numeric($productId)) {
            return [
                'success' => false,
                'message' => 'Invalid product ID.'
            ];
        }
        
        $sellerId = isset($data['seller_id']) ? (int)$data['seller_id'] : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null);
        
        // Delete product
        $result = $this->product->deleteProduct($productId, $sellerId);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Product deleted successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Delete failed. Product may not exist or you may not own it.'
            ];
        }
    }
    
    /**
     * Get all products
     * 
     * @param array $filters Optional filters (category_id, brand_id, limit, offset)
     * @return array Response array with products list
     */
    public function getAllProducts($filters = []) {
        // For public browsing, sellerId should be null
        // Only set sellerId if explicitly provided in filters or if user is logged in and wants their own products
        $sellerId = null;
        if (isset($filters['seller_id'])) {
            $sellerId = (int)$filters['seller_id'];
        } elseif (isset($filters['my_products']) && $filters['my_products']) {
            // Only use session if explicitly requesting own products
            if (isset($_SESSION) && isset($_SESSION['user_id'])) {
                $sellerId = (int)$_SESSION['user_id'];
            }
        }
        $categoryId = isset($filters['category_id']) ? (int)$filters['category_id'] : null;
        $brandId = isset($filters['brand_id']) ? (int)$filters['brand_id'] : null;
        $limit = isset($filters['limit']) ? (int)$filters['limit'] : null;
        $offset = isset($filters['offset']) ? (int)$filters['offset'] : null;
        
        // Build additional filters array
        $additionalFilters = [];
        if (isset($filters['status'])) {
            $additionalFilters['status'] = $filters['status'];
        }
        if (isset($filters['price_min']) && is_numeric($filters['price_min'])) {
            $additionalFilters['price_min'] = (float)$filters['price_min'];
        }
        if (isset($filters['price_max']) && is_numeric($filters['price_max'])) {
            $additionalFilters['price_max'] = (float)$filters['price_max'];
        }
        if (isset($filters['condition']) && !empty($filters['condition'])) {
            $additionalFilters['condition'] = trim($filters['condition']);
        }
        
        if (isset($filters['sort']) && !empty($filters['sort'])) {
            $additionalFilters['sort'] = trim($filters['sort']);
        }
        
        $products = $this->product->getAllProducts($sellerId, $categoryId, $brandId, $limit, $offset, $additionalFilters);
        
        return [
            'success' => true,
            'products' => $products,
            'count' => count($products)
        ];
    }
    
    /**
     * Search products with composite filters
     * 
     * @param string $searchTerm Search keyword (can be empty for filter-only searches)
     * @param array $filters Optional filters (category_id, brand_id, price_min, price_max, condition, status, limit, offset)
     * @return array Response array with search results
     */
    public function searchProducts($searchTerm, $filters = []) {
        // Allow empty search term for filter-only searches
        $searchTerm = trim($searchTerm);
        
        // Prepare filters array
        $searchFilters = [];
        
        if (isset($filters['category_id']) && !empty($filters['category_id'])) {
            $searchFilters['category_id'] = (int)$filters['category_id'];
        }
        
        if (isset($filters['brand_id']) && !empty($filters['brand_id'])) {
            $searchFilters['brand_id'] = (int)$filters['brand_id'];
        }
        
        if (isset($filters['price_min']) && is_numeric($filters['price_min'])) {
            $searchFilters['price_min'] = (float)$filters['price_min'];
        }
        
        if (isset($filters['price_max']) && is_numeric($filters['price_max'])) {
            $searchFilters['price_max'] = (float)$filters['price_max'];
        }
        
        if (isset($filters['condition']) && !empty($filters['condition'])) {
            $searchFilters['condition'] = trim($filters['condition']);
        }
        
        if (isset($filters['status']) && !empty($filters['status'])) {
            $searchFilters['status'] = trim($filters['status']);
        }
        
        if (isset($filters['limit']) && $filters['limit'] !== null) {
            $searchFilters['limit'] = (int)$filters['limit'];
        }
        
        if (isset($filters['offset']) && $filters['offset'] !== null) {
            $searchFilters['offset'] = (int)$filters['offset'];
        }
        
        // If no search term and no filters, return error
        if (empty($searchTerm) && empty($searchFilters)) {
            return [
                'success' => false,
                'message' => 'Please provide a search term or at least one filter.'
            ];
        }
        
        // Perform search
        $products = $this->product->searchProducts($searchTerm, $searchFilters);
        
        // Ensure products is an array
        if (!is_array($products)) {
            $products = [];
        }
        
        // Get total count for pagination (if limit is set)
        $totalCount = null;
        if (isset($searchFilters['limit'])) {
            try {
                // Remove limit/offset from filters for count
                $countFilters = $searchFilters;
                unset($countFilters['limit']);
                unset($countFilters['offset']);
                $totalCount = $this->product->searchProductsCount($searchTerm, $countFilters);
            } catch (Exception $e) {
                error_log("Error getting search count in controller: " . $e->getMessage());
                $totalCount = null;
            }
        }
        
        return [
            'success' => true,
            'products' => $products,
            'count' => count($products),
            'total_count' => $totalCount,
            'search_term' => $searchTerm
        ];
    }
    
    /**
     * Get products organized by category and brand
     * 
     * @return array Response array with organized products
     */
    public function getProductsOrganized() {
        $sellerId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        
        $organized = $this->product->getProductsOrganized($sellerId);
        
        return [
            'success' => true,
            'organized' => $organized
        ];
    }
    
    /**
     * Get product by ID
     * 
     * @param int $productId Product ID
     * @return array Response array with product data
     */
    public function getProduct($productId) {
        // Validate product ID
        if (empty($productId) || !is_numeric($productId)) {
            return [
                'success' => false,
                'message' => 'Invalid product ID.'
            ];
        }
        
        $product = $this->product->getProduct($productId);
        
        if ($product) {
            return [
                'success' => true,
                'product' => $product
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Product not found.'
            ];
        }
    }
}


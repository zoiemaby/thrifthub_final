<?php
/**
 * Browse Products Action
 * ThriftHub - Public Product Browsing Handler
 * 
 * Returns all active products with pagination support
 * No login required - public access
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

header('Content-Type: application/json; charset=utf-8');

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response

try {
    $controller = new ProductController();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to initialize controller: ' . $e->getMessage()
    ]);
    exit;
}

// Get filter parameters
$categoryId = isset($_GET['category']) && !empty($_GET['category']) ? (int)$_GET['category'] : null;
$brandId = isset($_GET['brand']) && !empty($_GET['brand']) ? (int)$_GET['brand'] : null;
$priceMin = isset($_GET['price_min']) && is_numeric($_GET['price_min']) ? (float)$_GET['price_min'] : null;
$priceMax = isset($_GET['price_max']) && is_numeric($_GET['price_max']) ? (float)$_GET['price_max'] : null;
$condition = isset($_GET['condition']) && !empty($_GET['condition']) ? trim($_GET['condition']) : null;
$sort = isset($_GET['sort']) && !empty($_GET['sort']) ? trim($_GET['sort']) : 'newest';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10; // 10 products per page
$offset = ($page - 1) * $limit;

// Build filters array for controller
$filters = [
    'status' => 'active',
    'limit' => $limit,
    'offset' => $offset
];

if ($categoryId) {
    $filters['category_id'] = $categoryId;
}

if ($brandId) {
    $filters['brand_id'] = $brandId;
}

if ($priceMin !== null) {
    $filters['price_min'] = $priceMin;
}

if ($priceMax !== null) {
    $filters['price_max'] = $priceMax;
}

if ($condition) {
    $filters['condition'] = $condition;
}

if ($sort) {
    $filters['sort'] = $sort;
}

try {
    // Get products with filters (efficient SQL query with status filter at database level)
    $result = $controller->getAllProducts($filters);
    
    if (!$result || !isset($result['success'])) {
        throw new Exception('Invalid response from controller');
    }
    
    if (!$result['success']) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to load products'
        ]);
        exit;
    }

    // Get total count efficiently (without limit/offset for count)
    $countFilters = $filters;
    unset($countFilters['limit']);
    unset($countFilters['offset']);
    $countResult = $controller->getAllProducts($countFilters);
    
    if (!$countResult || !isset($countResult['count'])) {
        throw new Exception('Failed to get product count');
    }
    
    $totalProducts = $countResult['count'];
    $totalPages = ceil($totalProducts / $limit);

    $paginatedProducts = $result['products'] ?? [];

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'products' => $paginatedProducts,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_products' => $totalProducts,
            'per_page' => $limit,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1
        ]
    ]);
} catch (Exception $e) {
    error_log('Browse products error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error loading products: ' . $e->getMessage()
    ]);
}
exit;


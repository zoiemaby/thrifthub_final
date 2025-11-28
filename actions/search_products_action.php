<?php
/**
 * Search Products Action
 * ThriftHub - Product Search Handler
 * 
 * Returns search results with pagination support
 * No login required - public access
 */

// Suppress any output before JSON
ob_start();

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

// Clear any output that might have been generated
ob_clean();

header('Content-Type: application/json; charset=utf-8');

$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// Get filter parameters
$categoryId = isset($_GET['category']) && !empty($_GET['category']) ? (int)$_GET['category'] : null;
$brandId = isset($_GET['brand']) && !empty($_GET['brand']) ? (int)$_GET['brand'] : null;
$priceMin = isset($_GET['price_min']) && is_numeric($_GET['price_min']) ? (float)$_GET['price_min'] : null;
$priceMax = isset($_GET['price_max']) && is_numeric($_GET['price_max']) ? (float)$_GET['price_max'] : null;
$condition = isset($_GET['condition']) && !empty($_GET['condition']) ? trim($_GET['condition']) : null;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10; // 10 products per page
$offset = ($page - 1) * $limit;

// Allow search without search term if filters are provided (composite search)
if (empty($searchTerm) && !$categoryId && !$brandId && $priceMin === null && $priceMax === null && !$condition) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please provide a search term or at least one filter.'
    ]);
    exit;
}

$controller = new ProductController();

// Build composite filters array
$filters = [
    'status' => 'active', // Only show active products
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

try {
    // Search products with composite filters
    $result = $controller->searchProducts($searchTerm, $filters);

    if ($result['success']) {
        // Get total count for pagination
        // Use total_count from result if available, otherwise calculate
        $countFilters = $filters;
        unset($countFilters['limit']);
        unset($countFilters['offset']);
        
        $totalProducts = null;
        if (isset($result['total_count']) && $result['total_count'] !== null) {
            $totalProducts = (int)$result['total_count'];
        } else {
            // Calculate count separately
            try {
                $totalProducts = $controller->product->searchProductsCount($searchTerm, $countFilters);
            } catch (Exception $e) {
                error_log("Search count error: " . $e->getMessage());
                // Fallback: use count of current results (not accurate for pagination but better than error)
                $totalProducts = count($result['products'] ?? []);
            }
        }
        
        $totalPages = $totalProducts > 0 ? ceil($totalProducts / $limit) : 0;
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'products' => $result['products'] ?? [],
            'search_term' => $searchTerm,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_products' => $totalProducts,
                'per_page' => $limit,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ]
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $result['message'] ?? 'Search failed. Please try again.'
        ]);
    }
} catch (Exception $e) {
    error_log("Search products action error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while searching. Please try again.',
        'error' => $e->getMessage()
    ]);
}

exit;


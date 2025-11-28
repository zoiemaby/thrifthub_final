<?php
/**
 * Get Seller Dashboard Statistics
 * ThriftHub - Fetch real-time seller statistics for dashboard
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../controllers/order_controller.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Please log in.'
    ]);
    exit;
}

// Check if user is a seller
$roleNo = isset($_SESSION['user_role_no']) ? (int)$_SESSION['user_role_no'] : 0;
if ($roleNo !== ROLE_SELLER) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Seller access required.'
    ]);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$productController = new ProductController();
$orderController = new OrderController();

try {
    // Get total products for this seller
    $productsResult = $productController->getAllProducts(['seller_id' => $userId]);
    $totalProducts = $productsResult['success'] ? $productsResult['count'] : 0;
    
    // Get seller orders
    $ordersResult = $orderController->getSellerOrdersCtrl($userId);
    $orders = $ordersResult['success'] ? $ordersResult['orders'] : [];
    
    // Calculate statistics from orders
    $pendingOrders = 0;
    $completedOrders = 0;
    $monthlyEarnings = 0;
    $currentMonth = date('Y-m');
    
    foreach ($orders as $order) {
        // Count pending orders
        if (in_array(strtolower($order['order_status'] ?? ''), ['pending', 'processing'])) {
            $pendingOrders++;
        }
        
        // Count completed orders
        if (in_array(strtolower($order['order_status'] ?? ''), ['completed', 'delivered'])) {
            $completedOrders++;
        }
        
        // Calculate monthly earnings for paid, shipped, completed, and delivered orders
        if (in_array(strtolower($order['order_status'] ?? ''), ['paid', 'shipped', 'completed', 'delivered'])) {
            $orderMonth = date('Y-m', strtotime($order['order_date'] ?? 'now'));
            if ($orderMonth === $currentMonth) {
                $monthlyEarnings += (float)($order['total_amount'] ?? 0);
            }
        }
    }
    
    // Get recent orders (last 5)
    $recentOrders = array_slice($orders, 0, 5);
    
    // Format recent orders for display
    $formattedRecentOrders = array_map(function($order) {
        return [
            'order_id' => $order['order_id'] ?? '',
            'customer_name' => $order['customer_name'] ?? 'N/A',
            'status' => strtolower($order['order_status'] ?? 'pending'),
            'date' => $order['order_date'] ?? date('Y-m-d H:i:s'),
            'total' => number_format((float)($order['total_amount'] ?? 0), 2)
        ];
    }, $recentOrders);
    
    // Get top selling products
    $topProducts = [];
    if ($productsResult['success'] && !empty($productsResult['products'])) {
        // Sort products by sales count if available
        $products = $productsResult['products'];
        
        // For now, take first 5 products (can be enhanced with actual sales data)
        $topProducts = array_slice($products, 0, 5);
        $topProducts = array_map(function($product) {
            return [
                'product_id' => $product['product_id'] ?? '',
                'product_title' => $product['product_title'] ?? 'Unknown',
                'sales_count' => 0, // TODO: Add actual sales count from order_details
                'revenue' => 0 // TODO: Calculate from order_details
            ];
        }, $topProducts);
    }
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_products' => $totalProducts,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'monthly_earnings' => number_format($monthlyEarnings, 2)
        ],
        'recent_orders' => $formattedRecentOrders,
        'top_products' => $topProducts
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching dashboard statistics.',
        'error' => $e->getMessage()
    ]);
}
exit;

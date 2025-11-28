<?php
/**
 * Order Class
 * ThriftHub - Order Management Class
 * 
 * This class extends the Database class and provides methods
 * for managing orders, order details, and payments
 */

require_once __DIR__ . '/../settings/db_class.php';

class Order extends Database {
    
    /**
     * Create a new order
     * 
     * @param int $customerId Customer user ID
     * @param float $totalAmount Total order amount
     * @param string $orderStatus Order status (default: 'pending')
     * @return array Returns ['success' => bool, 'order_id' => int, 'message' => string]
     */
    public function createOrder($customerId, $totalAmount, $orderStatus = 'pending') {
        $customerId = (int)$customerId;
        $totalAmount = (float)$totalAmount;
        $orderStatus = $this->escape($orderStatus);
        
        if ($customerId <= 0) {
            return ['success' => false, 'message' => 'Invalid customer ID.'];
        }
        
        if ($totalAmount <= 0) {
            return ['success' => false, 'message' => 'Invalid order amount.'];
        }
        
        $sql = "INSERT INTO orders (customer_id, total_amount, order_status) 
                VALUES ($customerId, $totalAmount, '$orderStatus')";
        
        if ($this->query($sql)) {
            $orderId = $this->insert_id();
            return ['success' => true, 'order_id' => $orderId, 'message' => 'Order created successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to create order.'];
        }
    }
    
    /**
     * Add order details (product items)
     * 
     * @param int $orderId Order ID
     * @param int $productId Product ID
     * @param int $quantity Quantity
     * @param float $price Price per unit
     * @return array Returns ['success' => bool, 'message' => string]
     */
    public function addOrderDetails($orderId, $productId, $quantity, $price) {
        $orderId = (int)$orderId;
        $productId = (int)$productId;
        $quantity = max(1, (int)$quantity);
        $price = (float)$price;
        
        if ($orderId <= 0 || $productId <= 0) {
            return ['success' => false, 'message' => 'Invalid order or product ID.'];
        }
        
        if ($price <= 0) {
            return ['success' => false, 'message' => 'Invalid price.'];
        }
        
        $sql = "INSERT INTO orderdetails (order_id, product_id, qty, price) 
                VALUES ($orderId, $productId, $quantity, $price)";
        
        if ($this->query($sql)) {
            return ['success' => true, 'message' => 'Order detail added successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to add order detail.'];
        }
    }
    
    /**
     * Record payment
     * 
     * @param float $amount Payment amount
     * @param int $customerId Customer user ID
     * @param int $orderId Order ID
     * @param string $paymentMethod Payment method (momo, card, cash, bank_transfer)
     * @param string $paymentStatus Payment status (pending, successful, failed)
     * @param string $transactionRef Transaction reference
     * @param string $currency Currency code (default: GHS)
     * @return array Returns ['success' => bool, 'payment_id' => int, 'message' => string]
     */
    public function recordPayment($amount, $customerId, $orderId, $paymentMethod = 'momo', $paymentStatus = 'successful', $transactionRef = null, $currency = 'GHS') {
        $amount = (float)$amount;
        $customerId = (int)$customerId;
        $orderId = (int)$orderId;
        $paymentMethod = $this->escape($paymentMethod);
        $paymentStatus = $this->escape($paymentStatus);
        $currency = $this->escape($currency);
        $transactionRef = $transactionRef ? "'" . $this->escape($transactionRef) . "'" : 'NULL';
        
        if ($customerId <= 0 || $orderId <= 0) {
            return ['success' => false, 'message' => 'Invalid customer or order ID.'];
        }
        
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Invalid payment amount.'];
        }
        
        $sql = "INSERT INTO payments (amount, customer_id, order_id, currency, payment_method, payment_status, transaction_ref) 
                VALUES ($amount, $customerId, $orderId, '$currency', '$paymentMethod', '$paymentStatus', $transactionRef)";
        
        if ($this->query($sql)) {
            $paymentId = $this->insert_id();
            return ['success' => true, 'payment_id' => $paymentId, 'message' => 'Payment recorded successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to record payment.'];
        }
    }
    
    /**
     * Get past orders for a user
     * 
     * @param int $customerId Customer user ID
     * @param int $limit Optional limit
     * @param int $offset Optional offset
     * @return array Returns array of orders with details
     */
    public function getPastOrders($customerId, $limit = null, $offset = null) {
        $customerId = (int)$customerId;
        
        if ($customerId <= 0) {
            return [];
        }
        
        $sql = "SELECT o.*, 
                       COUNT(od.orderdetail_id) as item_count,
                       GROUP_CONCAT(p.product_title SEPARATOR ', ') as products
                FROM orders o
                LEFT JOIN orderdetails od ON o.order_id = od.order_id
                LEFT JOIN products p ON od.product_id = p.product_id
                WHERE o.customer_id = $customerId
                GROUP BY o.order_id
                ORDER BY o.order_date DESC";
        
        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = $offset !== null ? (int)$offset : 0;
            $sql .= " LIMIT $offset, $limit";
        }
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Get order details by order ID
     * 
     * @param int $orderId Order ID
     * @param int $customerId Customer ID for validation
     * @return array Returns order with all details
     */
    public function getOrderById($orderId, $customerId = null) {
        $orderId = (int)$orderId;
        
        if ($orderId <= 0) {
            return null;
        }
        
        $where = "o.order_id = $orderId";
        if ($customerId) {
            $where .= " AND o.customer_id = " . (int)$customerId;
        }
        
        $sql = "SELECT o.*, 
                       COUNT(od.orderdetail_id) as item_count
                FROM orders o
                LEFT JOIN orderdetails od ON o.order_id = od.order_id
                WHERE $where
                GROUP BY o.order_id";
        
        $order = $this->fetchOne($sql);
        
        if ($order) {
            // Get order details
            $detailsSql = "SELECT od.*, 
                                  p.product_title, p.product_image,
                                  cat.cat_name, b.brand_name
                           FROM orderdetails od
                           INNER JOIN products p ON od.product_id = p.product_id
                           LEFT JOIN categories cat ON p.product_cat = cat.cat_id
                           LEFT JOIN brands b ON p.product_brand = b.brand_id
                           WHERE od.order_id = $orderId";
            
            $order['items'] = $this->fetchAll($detailsSql);
            
            // Get payment info
            $paymentSql = "SELECT * FROM payments WHERE order_id = $orderId ORDER BY payment_date DESC LIMIT 1";
            $order['payment'] = $this->fetchOne($paymentSql);
        }
        
        return $order;
    }
    
    /**
     * Generate unique order reference
     * 
     * @return string Returns unique order reference
     */
    public function generateOrderReference() {
        $prefix = 'TH';
        $timestamp = time();
        $random = mt_rand(1000, 9999);
        return $prefix . $timestamp . $random;
    }
    
    /**
     * Update order status
     * 
     * @param int $orderId Order ID
     * @param string $status New status
     * @return array Returns ['success' => bool, 'message' => string]
     */
    public function updateOrderStatus($orderId, $status) {
        $orderId = (int)$orderId;
        $status = $this->escape($status);
        
        $allowedStatuses = ['pending', 'paid', 'shipped', 'completed', 'cancelled'];
        if (!in_array($status, $allowedStatuses)) {
            return ['success' => false, 'message' => 'Invalid order status.'];
        }
        
        $sql = "UPDATE orders SET order_status = '$status' WHERE order_id = $orderId";
        
        if ($this->query($sql)) {
            return ['success' => true, 'message' => 'Order status updated successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to update order status.'];
        }
    }
    
    /**
     * Get orders for a specific seller
     * 
     * @param int $sellerId Seller user ID
     * @param int $limit Optional limit
     * @param int $offset Optional offset
     * @return array Returns array of orders for seller's products
     */
    public function getSellerOrders($sellerId, $limit = null, $offset = null) {
        $sellerId = (int)$sellerId;
        
        if ($sellerId <= 0) {
            return [];
        }
        
        $sql = "SELECT DISTINCT o.*, 
                       u.name as customer_name,
                       u.email as customer_email,
                       COUNT(DISTINCT od.orderdetail_id) as item_count,
                       GROUP_CONCAT(DISTINCT p.product_title SEPARATOR ', ') as products
                FROM orders o
                INNER JOIN orderdetails od ON o.order_id = od.order_id
                INNER JOIN products p ON od.product_id = p.product_id
                LEFT JOIN users u ON o.customer_id = u.user_id
                WHERE p.seller_id = $sellerId
                GROUP BY o.order_id
                ORDER BY o.order_date DESC";
        
        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = $offset !== null ? (int)$offset : 0;
            $sql .= " LIMIT $offset, $limit";
        }
        
        return $this->fetchAll($sql);
    }
}


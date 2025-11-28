<?php
/**
 * Order Controller
 * ThriftHub - Order Management Controller
 * 
 * Creates an instance of the Order class and handles order operations
 */

require_once __DIR__ . '/../classes/order_class.php';

class OrderController {
    private $order;
    
    public function __construct() {
        $this->order = new Order();
    }
    
    /**
     * Create order controller method
     * 
     * @param array $data Order data (customer_id, total_amount, order_status)
     * @return array Response array
     */
    public function createOrderCtrl($data) {
        if (empty($data['customer_id'])) {
            return ['success' => false, 'message' => 'Customer ID is required.'];
        }
        
        if (empty($data['total_amount'])) {
            return ['success' => false, 'message' => 'Total amount is required.'];
        }
        
        $customerId = (int)$data['customer_id'];
        $totalAmount = (float)$data['total_amount'];
        $orderStatus = isset($data['order_status']) ? trim($data['order_status']) : 'pending';
        
        $result = $this->order->createOrder($customerId, $totalAmount, $orderStatus);
        return $result;
    }
    
    /**
     * Add order details controller method
     * 
     * @param array $data Order detail data (order_id, product_id, quantity, price)
     * @return array Response array
     */
    public function addOrderDetailsCtrl($data) {
        if (empty($data['order_id']) || empty($data['product_id']) || empty($data['quantity']) || empty($data['price'])) {
            return ['success' => false, 'message' => 'All order detail fields are required.'];
        }
        
        $orderId = (int)$data['order_id'];
        $productId = (int)$data['product_id'];
        $quantity = (int)$data['quantity'];
        $price = (float)$data['price'];
        
        $result = $this->order->addOrderDetails($orderId, $productId, $quantity, $price);
        return $result;
    }
    
    /**
     * Record payment controller method
     * 
     * @param array $data Payment data
     * @return array Response array
     */
    public function recordPaymentCtrl($data) {
        if (empty($data['amount']) || empty($data['customer_id']) || empty($data['order_id'])) {
            return ['success' => false, 'message' => 'Required payment fields are missing.'];
        }
        
        $amount = (float)$data['amount'];
        $customerId = (int)$data['customer_id'];
        $orderId = (int)$data['order_id'];
        $paymentMethod = isset($data['payment_method']) ? trim($data['payment_method']) : 'momo';
        $paymentStatus = isset($data['payment_status']) ? trim($data['payment_status']) : 'successful';
        $transactionRef = isset($data['transaction_ref']) ? trim($data['transaction_ref']) : null;
        $currency = isset($data['currency']) ? trim($data['currency']) : 'GHS';
        
        $result = $this->order->recordPayment($amount, $customerId, $orderId, $paymentMethod, $paymentStatus, $transactionRef, $currency);
        return $result;
    }
    
    /**
     * Get past orders controller method
     * 
     * @param int $customerId Customer ID
     * @param int $limit Optional limit
     * @param int $offset Optional offset
     * @return array Response array
     */
    public function getPastOrdersCtrl($customerId, $limit = null, $offset = null) {
        if (empty($customerId)) {
            return ['success' => false, 'message' => 'Customer ID is required.'];
        }
        
        $orders = $this->order->getPastOrders($customerId, $limit, $offset);
        
        return [
            'success' => true,
            'orders' => $orders,
            'count' => count($orders)
        ];
    }
    
    /**
     * Get order by ID controller method
     * 
     * @param int $orderId Order ID
     * @param int $customerId Optional customer ID for validation
     * @return array Response array
     */
    public function getOrderByIdCtrl($orderId, $customerId = null) {
        if (empty($orderId)) {
            return ['success' => false, 'message' => 'Order ID is required.'];
        }
        
        $order = $this->order->getOrderById($orderId, $customerId);
        
        if ($order) {
            return ['success' => true, 'order' => $order];
        } else {
            return ['success' => false, 'message' => 'Order not found.'];
        }
    }
    
    /**
     * Get seller orders controller method
     * 
     * @param int $sellerId Seller user ID
     * @param int $limit Optional limit
     * @param int $offset Optional offset
     * @return array Response array
     */
    public function getSellerOrdersCtrl($sellerId, $limit = null, $offset = null) {
        if (empty($sellerId)) {
            return ['success' => false, 'message' => 'Seller ID is required.'];
        }
        
        $orders = $this->order->getSellerOrders($sellerId, $limit, $offset);
        
        return [
            'success' => true,
            'orders' => $orders,
            'count' => count($orders)
        ];
    }
    
    /**
     * Generate order reference
     * 
     * @return string Order reference
     */
    public function generateOrderReference() {
        return $this->order->generateOrderReference();
    }
}


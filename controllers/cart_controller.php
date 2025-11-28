<?php
/**
 * Cart Controller
 * ThriftHub - Cart Management Controller
 * 
 * Creates an instance of the Cart class and wraps its methods
 */

require_once __DIR__ . '/../classes/cart_class.php';

class CartController {
    private $cart;
    
    public function __construct() {
        $this->cart = new Cart();
    }
    
    /**
     * Add to cart controller method
     * 
     * @param array $data Cart data (product_id, user_id, ip_address, quantity)
     * @return array Response array
     */
    public function addToCartCtrl($data) {
        if (empty($data['product_id'])) {
            return ['success' => false, 'message' => 'Product ID is required.'];
        }
        
        $productId = (int)$data['product_id'];
        $userId = isset($data['user_id']) ? (int)$data['user_id'] : null;
        $ipAddress = isset($data['ip_address']) ? trim($data['ip_address']) : '';
        $quantity = isset($data['quantity']) ? max(1, (int)$data['quantity']) : 1;
        
        if (empty($ipAddress) && !$userId) {
            return ['success' => false, 'message' => 'User identification required.'];
        }
        
        $result = $this->cart->addToCart($productId, $userId, $ipAddress, $quantity);
        return $result;
    }
    
    /**
     * Update cart item quantity
     * 
     * @param int $cartId Cart ID
     * @param array $data Update data (quantity, user_id, ip_address)
     * @return array Response array
     */
    public function updateCartItemCtrl($cartId, $data) {
        if (empty($cartId)) {
            return ['success' => false, 'message' => 'Cart ID is required.'];
        }
        
        $quantity = isset($data['quantity']) ? max(1, (int)$data['quantity']) : 1;
        $userId = isset($data['user_id']) ? (int)$data['user_id'] : null;
        $ipAddress = isset($data['ip_address']) ? trim($data['ip_address']) : '';
        
        $result = $this->cart->updateQuantity($cartId, $quantity, $userId, $ipAddress);
        return $result;
    }
    
    /**
     * Remove from cart controller method
     * 
     * @param int $cartId Cart ID
     * @param array $data User identification (user_id, ip_address)
     * @return array Response array
     */
    public function removeFromCartCtrl($cartId, $data) {
        if (empty($cartId)) {
            return ['success' => false, 'message' => 'Cart ID is required.'];
        }
        
        $userId = isset($data['user_id']) ? (int)$data['user_id'] : null;
        $ipAddress = isset($data['ip_address']) ? trim($data['ip_address']) : '';
        
        $result = $this->cart->removeFromCart($cartId, $userId, $ipAddress);
        return $result;
    }
    
    /**
     * Get user cart controller method
     * 
     * @param array $data User identification (user_id, ip_address)
     * @return array Response array with cart items
     */
    public function getUserCartCtrl($data) {
        $userId = isset($data['user_id']) ? (int)$data['user_id'] : null;
        $ipAddress = isset($data['ip_address']) ? trim($data['ip_address']) : '';
        
        $items = $this->cart->getUserCart($userId, $ipAddress);
        $total = $this->cart->getCartTotal($userId, $ipAddress);
        $count = $this->cart->getCartCount($userId, $ipAddress);
        
        return [
            'success' => true,
            'items' => $items,
            'total' => $total,
            'count' => $count
        ];
    }
    
    /**
     * Empty cart controller method
     * 
     * @param array $data User identification (user_id, ip_address)
     * @return array Response array
     */
    public function emptyCartCtrl($data) {
        $userId = isset($data['user_id']) ? (int)$data['user_id'] : null;
        $ipAddress = isset($data['ip_address']) ? trim($data['ip_address']) : '';
        
        $result = $this->cart->emptyCart($userId, $ipAddress);
        return $result;
    }
}


<?php
/**
 * Cart Class
 * ThriftHub - Cart Management Class
 * 
 * This class extends the Database class and provides methods
 * for managing shopping cart operations
 */

require_once __DIR__ . '/../settings/db_class.php';

class Cart extends Database {
    
    /**
     * Add a product to the cart
     * 
     * @param int $productId Product ID
     * @param int $userId User ID (null for guests)
     * @param string $ipAddress IP address for guest tracking
     * @param int $quantity Quantity to add
     * @return array Returns ['success' => bool, 'message' => string, 'cart_id' => int]
     */
    public function addToCart($productId, $userId, $ipAddress, $quantity = 1) {
        $productId = (int)$productId;
        $quantity = max(1, (int)$quantity);
        $ipAddress = $this->escape($ipAddress);
        
        if ($productId <= 0) {
            return ['success' => false, 'message' => 'Invalid product ID.'];
        }
        
        // Check if product exists and is active
        $product = $this->fetchOne("SELECT product_id, product_price, product_status FROM products WHERE product_id = $productId");
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }
        
        if ($product['product_status'] !== 'active') {
            return ['success' => false, 'message' => 'Product is not available.'];
        }
        
        // Check if product already exists in cart
        if ($userId) {
            $existing = $this->fetchOne("SELECT cart_id, qty FROM cart WHERE p_id = $productId AND c_id = $userId");
        } else {
            $existing = $this->fetchOne("SELECT cart_id, qty FROM cart WHERE p_id = $productId AND ip_add = '$ipAddress' AND c_id IS NULL");
        }
        
        if ($existing) {
            // Update quantity
            $newQty = $existing['qty'] + $quantity;
            $cartId = (int)$existing['cart_id'];
            $sql = "UPDATE cart SET qty = $newQty WHERE cart_id = $cartId";
            
            if ($this->query($sql)) {
                return ['success' => true, 'message' => 'Cart updated successfully.', 'cart_id' => $cartId];
            } else {
                return ['success' => false, 'message' => 'Failed to update cart.'];
            }
        } else {
            // Insert new cart item
            $userIdSql = $userId ? (int)$userId : 'NULL';
            $sql = "INSERT INTO cart (p_id, ip_add, c_id, qty) VALUES ($productId, '$ipAddress', $userIdSql, $quantity)";
            
            if ($this->query($sql)) {
                $cartId = $this->insert_id();
                return ['success' => true, 'message' => 'Product added to cart.', 'cart_id' => $cartId];
            } else {
                return ['success' => false, 'message' => 'Failed to add product to cart.'];
            }
        }
    }
    
    /**
     * Update quantity of a cart item
     * 
     * @param int $cartId Cart ID
     * @param int $quantity New quantity
     * @param int $userId User ID (for validation)
     * @param string $ipAddress IP address (for guest validation)
     * @return array Returns ['success' => bool, 'message' => string]
     */
    public function updateQuantity($cartId, $quantity, $userId = null, $ipAddress = null) {
        $cartId = (int)$cartId;
        $quantity = max(1, (int)$quantity);
        
        if ($cartId <= 0) {
            return ['success' => false, 'message' => 'Invalid cart ID.'];
        }
        
        // Verify ownership
        $where = "cart_id = $cartId";
        if ($userId) {
            $where .= " AND c_id = " . (int)$userId;
        } else if ($ipAddress) {
            $ipAddress = $this->escape($ipAddress);
            $where .= " AND ip_add = '$ipAddress' AND c_id IS NULL";
        } else {
            return ['success' => false, 'message' => 'User identification required.'];
        }
        
        $cartItem = $this->fetchOne("SELECT * FROM cart WHERE $where");
        if (!$cartItem) {
            return ['success' => false, 'message' => 'Cart item not found.'];
        }
        
        // Update quantity
        $sql = "UPDATE cart SET qty = $quantity WHERE cart_id = $cartId";
        
        if ($this->query($sql)) {
            return ['success' => true, 'message' => 'Quantity updated successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to update quantity.'];
        }
    }
    
    /**
     * Remove a product from the cart
     * 
     * @param int $cartId Cart ID
     * @param int $userId User ID (for validation)
     * @param string $ipAddress IP address (for guest validation)
     * @return array Returns ['success' => bool, 'message' => string]
     */
    public function removeFromCart($cartId, $userId = null, $ipAddress = null) {
        $cartId = (int)$cartId;
        
        if ($cartId <= 0) {
            return ['success' => false, 'message' => 'Invalid cart ID.'];
        }
        
        // Verify ownership
        $where = "cart_id = $cartId";
        if ($userId) {
            $where .= " AND c_id = " . (int)$userId;
        } else if ($ipAddress) {
            $ipAddress = $this->escape($ipAddress);
            $where .= " AND ip_add = '$ipAddress' AND c_id IS NULL";
        } else {
            return ['success' => false, 'message' => 'User identification required.'];
        }
        
        $sql = "DELETE FROM cart WHERE $where";
        
        if ($this->query($sql)) {
            return ['success' => true, 'message' => 'Item removed from cart.'];
        } else {
            return ['success' => false, 'message' => 'Failed to remove item from cart.'];
        }
    }
    
    /**
     * Get all cart items for a user
     * 
     * @param int $userId User ID (null for guests)
     * @param string $ipAddress IP address for guest tracking
     * @return array Returns array of cart items with product details
     */
    public function getUserCart($userId = null, $ipAddress = null) {
        if ($userId) {
            $where = "c.c_id = " . (int)$userId;
        } else if ($ipAddress) {
            $ipAddress = $this->escape($ipAddress);
            $where = "c.ip_add = '$ipAddress' AND c.c_id IS NULL";
        } else {
            return [];
        }
        
        $sql = "SELECT c.cart_id, c.p_id, c.qty, c.added_at,
                       p.product_id, p.product_title, p.product_price, p.product_image, p.product_status,
                       cat.cat_name, b.brand_name
                FROM cart c
                INNER JOIN products p ON c.p_id = p.product_id
                LEFT JOIN categories cat ON p.product_cat = cat.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                WHERE $where AND p.product_status = 'active'
                ORDER BY c.added_at DESC";
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Empty the cart for a user
     * 
     * @param int $userId User ID (null for guests)
     * @param string $ipAddress IP address for guest tracking
     * @return array Returns ['success' => bool, 'message' => string]
     */
    public function emptyCart($userId = null, $ipAddress = null) {
        if ($userId) {
            $where = "c_id = " . (int)$userId;
        } else if ($ipAddress) {
            $ipAddress = $this->escape($ipAddress);
            $where = "ip_add = '$ipAddress' AND c_id IS NULL";
        } else {
            return ['success' => false, 'message' => 'User identification required.'];
        }
        
        $sql = "DELETE FROM cart WHERE $where";
        
        if ($this->query($sql)) {
            return ['success' => true, 'message' => 'Cart emptied successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to empty cart.'];
        }
    }
    
    /**
     * Check if a product exists in cart
     * 
     * @param int $productId Product ID
     * @param int $userId User ID (null for guests)
     * @param string $ipAddress IP address for guest tracking
     * @return bool Returns true if product exists in cart
     */
    public function productExistsInCart($productId, $userId = null, $ipAddress = null) {
        $productId = (int)$productId;
        
        if ($userId) {
            $where = "p_id = $productId AND c_id = " . (int)$userId;
        } else if ($ipAddress) {
            $ipAddress = $this->escape($ipAddress);
            $where = "p_id = $productId AND ip_add = '$ipAddress' AND c_id IS NULL";
        } else {
            return false;
        }
        
        $result = $this->fetchOne("SELECT cart_id FROM cart WHERE $where");
        return $result !== false;
    }
    
    /**
     * Get cart total count
     * 
     * @param int $userId User ID (null for guests)
     * @param string $ipAddress IP address for guest tracking
     * @return int Returns total number of items in cart
     */
    public function getCartCount($userId = null, $ipAddress = null) {
        if ($userId) {
            $where = "c_id = " . (int)$userId;
        } else if ($ipAddress) {
            $ipAddress = $this->escape($ipAddress);
            $where = "ip_add = '$ipAddress' AND c_id IS NULL";
        } else {
            return 0;
        }
        
        $result = $this->fetchOne("SELECT SUM(qty) as total FROM cart WHERE $where");
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Get cart total amount
     * 
     * @param int $userId User ID (null for guests)
     * @param string $ipAddress IP address for guest tracking
     * @return float Returns total cart value
     */
    public function getCartTotal($userId = null, $ipAddress = null) {
        $items = $this->getUserCart($userId, $ipAddress);
        $total = 0;
        
        foreach ($items as $item) {
            $total += (float)$item['product_price'] * (int)$item['qty'];
        }
        
        return $total;
    }
}


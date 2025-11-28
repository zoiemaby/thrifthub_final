<?php
/**
 * Seller Controller
 * ThriftHub - Seller Management Controller
 * 
 * Interface between views/actions and the Seller class
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../classes/seller_class.php';

class SellerController {
    private $seller;
    
    public function __construct() {
        $this->seller = new Seller();
    }
    
    /**
     * Create seller profile
     */
    public function create_seller_ctrl($userId, $shopName, $typeId, $sectorId, $logo = null, $banner = null, $description = null) {
        if (empty($shopName) || empty($typeId) || empty($sectorId)) {
            return [
                'success' => false,
                'message' => 'Shop name, business type, and sector are required.'
            ];
        }
        
        $result = $this->seller->create_seller($userId, $shopName, $typeId, $sectorId, $logo, $banner, $description);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Seller profile created successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to create seller profile. Seller may already exist.'
            ];
        }
    }
    
    /**
     * Get seller by user ID
     */
    public function get_seller_by_user_id_ctrl($userId) {
        $seller = $this->seller->get_seller_by_user_id($userId);
        
        if ($seller) {
            return [
                'success' => true,
                'seller' => $seller
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Seller not found.'
            ];
        }
    }
    
    /**
     * Update seller profile
     */
    public function update_seller_profile_ctrl($userId, $shopName, $typeId, $sectorId, $logo = null, $banner = null, $description = null) {
        if (empty($shopName) || empty($typeId) || empty($sectorId)) {
            return [
                'success' => false,
                'message' => 'Shop name, business type, and sector are required.'
            ];
        }
        
        $result = $this->seller->update_seller_profile($userId, $shopName, $typeId, $sectorId, $logo, $banner, $description);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Seller profile updated successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update seller profile.'
            ];
        }
    }
    
    /**
     * Check if seller is verified
     */
    public function is_seller_verified_ctrl($userId) {
        $verified = $this->seller->is_seller_verified($userId);
        
        return [
            'success' => true,
            'verified' => $verified
        ];
    }
    
    /**
     * Get verified sellers
     */
    public function get_verified_sellers_ctrl($limit = null, $offset = null) {
        $sellers = $this->seller->get_verified_sellers($limit, $offset);
        
        return [
            'success' => true,
            'sellers' => $sellers,
            'count' => count($sellers)
        ];
    }
    
    /**
     * Get all sellers
     */
    public function get_all_sellers_ctrl($limit = null, $offset = null) {
        $sellers = $this->seller->get_all_sellers($limit, $offset);
        
        return [
            'success' => true,
            'sellers' => $sellers,
            'count' => count($sellers)
        ];
    }
    
    /**
     * Set seller verified status
     */
    public function set_seller_verified_ctrl($userId, $verified) {
        $result = $this->seller->set_seller_verified($userId, $verified);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Seller verification status updated.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update verification status.'
            ];
        }
    }
}

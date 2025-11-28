<?php
/**
 * Brand Controller
 * ThriftHub - Brand Management Controller
 * 
 * This controller handles HTTP requests for brand operations
 * and acts as an interface between the view/actions and the Brand class.
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../classes/brand_class.php';

class BrandController {
    private $brand;
    
    /**
     * Constructor - Initialize Brand class instance
     */
    public function __construct() {
        $this->brand = new Brand();
    }
    
    /**
     * Handle brand creation
     * 
     * @param array $data Brand data (brand_name, user_id)
     * @return array Response array with success status and message/data
     */
    public function addBrand($data) {
        // Validate required fields
        if (empty($data['brand_name'])) {
            return [
                'success' => false,
                'message' => 'Brand name is required.'
            ];
        }
        
        $userId = isset($data['user_id']) ? (int)$data['user_id'] : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0);
        if ($userId <= 0) {
            return [
                'success' => false,
                'message' => 'User ID is required.'
            ];
        }
        
        $brandName = trim($data['brand_name']);
        
        // Validate brand name length
        if (strlen($brandName) < 2 || strlen($brandName) > 100) {
            return [
                'success' => false,
                'message' => 'Brand name must be between 2 and 100 characters.'
            ];
        }
        
        // Add brand
        $result = $this->brand->addBrand($brandName, $userId);
        
        if (is_array($result)) {
            if ($result['success']) {
                return [
                    'success' => true,
                    'message' => 'Brand created successfully.',
                    'brand_id' => $result['brand_id']
                ];
            } else {
                $error = $result['error'] ?? 'unknown';
                
                switch ($error) {
                    case 'duplicate':
                        return [
                            'success' => false,
                            'message' => 'A brand with this name already exists. Please choose a different name.'
                        ];
                    case 'missing_column':
                        return [
                            'success' => false,
                            'message' => 'Database error: Please run the SQL migration to add user_id column. See db/migrations/add_user_id_to_brands.sql'
                        ];
                    case 'database_error':
                        return [
                            'success' => false,
                            'message' => 'Database error: ' . ($result['db_error'] ?? 'Unknown database error')
                        ];
                    case 'invalid_input':
                        return [
                            'success' => false,
                            'message' => 'Invalid brand name or user ID.'
                        ];
                    default:
                        return [
                            'success' => false,
                            'message' => 'Brand creation failed. Please try again.'
                        ];
                }
            }
        }
        return [
            'success' => false,
            'message' => 'Brand creation failed. Unknown error.'
        ];
    }
    
    /**
     * Handle brand update
     * 
     * @param int $brandId Brand ID to update
     * @param array $data Data to update
     * @return array Response array with success status and message
     */
    public function updateBrand($brandId, $data) {
        // Validate brand ID
        if (empty($brandId) || !is_numeric($brandId)) {
            return [
                'success' => false,
                'message' => 'Invalid brand ID.'
            ];
        }
        
        // Validate brand name
        if (empty($data['brand_name'])) {
            return [
                'success' => false,
                'message' => 'Brand name is required.'
            ];
        }
        
        $userId = isset($data['user_id']) ? (int)$data['user_id'] : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null);
        $brandName = trim($data['brand_name']);
        
        if (strlen($brandName) < 2 || strlen($brandName) > 100) {
            return [
                'success' => false,
                'message' => 'Brand name must be between 2 and 100 characters.'
            ];
        }
        
        // Update brand
        $result = $this->brand->editBrand($brandId, $brandName, $userId);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Brand updated successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Update failed. Brand may not exist, you may not own it, or name may already be in use.'
            ];
        }
    }
    
    /**
     * Handle brand deletion
     * 
     * @param int $brandId Brand ID to delete
     * @param array $data Optional data containing user_id
     * @return array Response array with success status and message
     */
    public function deleteBrand($brandId, $data = []) {
        // Validate brand ID
        if (empty($brandId) || !is_numeric($brandId)) {
            return [
                'success' => false,
                'message' => 'Invalid brand ID.'
            ];
        }
        
        $userId = isset($data['user_id']) ? (int)$data['user_id'] : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null);
        
        // Delete brand
        $result = $this->brand->deleteBrand($brandId, $userId);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Brand deleted successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Delete failed. Brand may not exist or you may not own it.'
            ];
        }
    }
    
    /**
     * Get all brands
     * 
     * @return array Response array with brands list
     */
    public function getAllBrands() {
        // If user is logged in, get their brands; otherwise get all brands
        // $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        
        // if ($userId) {
        //     return $this->getBrandsByUser($userId);
        // }
        
        $brands = $this->brand->getAllBrands();
        $count = count($brands);
        
        return [
            'success' => true,
            'brands' => $brands,
            'count' => $count
        ];
    }

    /**
     * Get brands created by a specific user
     *
     * @param int $userId User ID whose brands to fetch
     * @return array Response array
     */
    public function getBrandsByUser($userId) {
        $userId = (int)$userId;
        if ($userId <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid user ID.'
            ];
        }

        $brands = $this->brand->getBrandsByUser($userId);
        $count = count($brands);
        
        return [
            'success' => true,
            'brands' => $brands,
            'count' => $count
        ];
    }
    
    /**
     * Get brand by ID
     * 
     * @param int $brandId Brand ID
     * @return array Response array with brand data
     */
    public function getBrand($brandId) {
        // Validate brand ID
        if (empty($brandId) || !is_numeric($brandId)) {
            return [
                'success' => false,
                'message' => 'Invalid brand ID.'
            ];
        }
        
        $brand = $this->brand->getBrand($brandId);
        
        if ($brand) {
            return [
                'success' => true,
                'brand' => $brand
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Brand not found.'
            ];
        }
    }
}


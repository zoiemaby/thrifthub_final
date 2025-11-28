<?php
/**
 * Category Controller
 * ThriftHub - Category Management Controller
 * 
 * This controller handles HTTP requests for category operations
 * and acts as an interface between the view/actions and the Category class.
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../classes/category_class.php';

class CategoryController {
    private $category;

    public function __construct() {
        $this->category = new Category();
    }

    public function addCategory($data) {
        if (empty($data['cat_name'])) {
            return ['success' => false, 'message' => 'Category name is required.'];
        }
        
        // Get user_id from data or session
        $userId = isset($data['user_id']) ? (int)$data['user_id'] : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0);
        if ($userId <= 0) {
            return ['success' => false, 'message' => 'User ID is required.'];
        }
        
        $name = trim($data['cat_name']);
        if (strlen($name) < 2 || strlen($name) > 100) {
            return ['success' => false, 'message' => 'Category name must be between 2 and 100 characters.'];
        }
        
        $result = $this->category->addCategory($name, $userId);
        
        // Handle new return format (array with success and error keys)
        if (is_array($result)) {
            if ($result['success']) {
                return ['success' => true, 'message' => 'Category created successfully.', 'cat_id' => $result['cat_id']];
            } else {
                // Handle specific error types
                $error = $result['error'] ?? 'unknown';
                
                switch ($error) {
                    case 'duplicate':
                        return ['success' => false, 'message' => 'A category with this name already exists. Please choose a different name.'];
                    
                    case 'missing_column':
                        return [
                            'success' => false, 
                            'message' => 'Database error: Please run the SQL migration to add user_id column. See db/migrations/add_user_id_to_categories.sql'
                        ];
                    
                    case 'database_error':
                        return [
                            'success' => false,
                            'message' => 'Database error: ' . ($result['db_error'] ?? 'Unknown database error')
                        ];
                    
                    case 'invalid_input':
                        return ['success' => false, 'message' => 'Invalid category name or user ID.'];
                    
                    default:
                        return ['success' => false, 'message' => 'Category creation failed. Please try again.'];
                }
            }
        }
        
        // Fallback for old return format (backward compatibility)
        // Check for database errors first
        $conn = $this->category->getConnection();
        if ($conn && $conn->errno) {
            if ($conn->errno == 1054) {
                return [
                    'success' => false, 
                    'message' => 'Database error: Please run the SQL migration to add user_id column. See db/migrations/add_user_id_to_categories.sql'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Database error: ' . $conn->error
                ];
            }
        }
        
        if ($result && $result > 0) {
            return ['success' => true, 'message' => 'Category created successfully.', 'cat_id' => $result];
        }
        
        // Default error
        return ['success' => false, 'message' => 'A category with this name already exists. Please choose a different name.'];
    }

    public function updateCategory($catId, $data) {
        if (empty($catId) || !is_numeric($catId)) {
            return ['success' => false, 'message' => 'Invalid category ID.'];
        }
        if (empty($data['cat_name'])) {
            return ['success' => false, 'message' => 'Category name is required.'];
        }
        
        // Get user_id from data or session to verify ownership
        $userId = isset($data['user_id']) ? (int)$data['user_id'] : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null);
        
        $name = trim($data['cat_name']);
        if (strlen($name) < 2 || strlen($name) > 100) {
            return ['success' => false, 'message' => 'Category name must be between 2 and 100 characters.'];
        }
        
        $res = $this->category->editCategory($catId, $name, $userId);
        if ($res) return ['success' => true, 'message' => 'Category updated successfully.'];
        return ['success' => false, 'message' => 'Update failed. Category may not exist, name in use, or you do not have permission.'];
    }

    public function deleteCategory($catId, $userId = null) {
        if (empty($catId) || !is_numeric($catId)) {
            return ['success' => false, 'message' => 'Invalid category ID.'];
        }
        
        // Get user_id from parameter or session to verify ownership
        if ($userId === null) {
            $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        } else {
            $userId = (int)$userId;
        }
        
        $res = $this->category->deleteCategory($catId, $userId);
        if ($res) return ['success' => true, 'message' => 'Category deleted successfully.'];
        return ['success' => false, 'message' => 'Delete failed. Category may not exist or you do not have permission.'];
    }

    /**
     * Get all categories (optionally filtered by user)
     * @param int|null $userId Optional user ID to filter by
     * @return array
     */
    public function getAllCategories($userId = null) {
        $cats = $this->category->getAllCategories($userId);
        $count = $this->category->getCategoryCount($userId);
        return ['success' => true, 'categories' => $cats, 'count' => $count];
    }

    /**
     * Get categories created by a specific user
     * @param int $userId User ID
     * @return array
     */
    public function getCategoriesByUser($userId) {
        if (empty($userId) || !is_numeric($userId)) {
            return ['success' => false, 'message' => 'Invalid user ID.'];
        }
        $userId = (int)$userId;
        $cats = $this->category->getCategoriesByUser($userId);
        $count = count($cats);
        return ['success' => true, 'categories' => $cats, 'count' => $count];
    }

    public function getCategory($catId) {
        if (empty($catId) || !is_numeric($catId)) {
            return ['success' => false, 'message' => 'Invalid category ID.'];
        }
        $cat = $this->category->getCategory($catId);
        if ($cat) return ['success' => true, 'category' => $cat];
        return ['success' => false, 'message' => 'Category not found.'];
    }
}

// Optional POST dispatch (single controller instance per request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $controller = new CategoryController();
    $action = $_POST['action'];
    switch ($action) {
        case 'add':
            $resp = $controller->addCategory($_POST);
            break;
        case 'update':
            $resp = $controller->updateCategory($_POST['cat_id'] ?? null, $_POST);
            break;
        case 'delete':
            $resp = $controller->deleteCategory($_POST['cat_id'] ?? null);
            break;
        case 'getAll':
            $resp = $controller->getAllCategories();
            break;
        case 'getOne':
            $resp = $controller->getCategory($_POST['cat_id'] ?? null);
            break;
        default:
            $resp = ['success' => false, 'message' => 'Invalid action specified.'];
    }
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit;
}


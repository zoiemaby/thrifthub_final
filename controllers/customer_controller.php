<?php
/**
 * Customer Controller
 * ThriftHub - Customer Management Controller
 * 
 * This controller handles HTTP requests for customer operations
 * and acts as an interface between the view/actions and the Customer class.
 * 
 * It validates input, calls appropriate Customer class methods,
 * and returns responses (JSON or redirects).
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../classes/customer_class.php';

class CustomerController {
    private $customer;
    
    /**
     * Constructor - Initialize Customer class instance
     */
    public function __construct() {
        $this->customer = new Customer();
    }
    
    /**
     * Handle customer registration
     * 
     * @param array $data Customer registration data (name, email, password, phone, country, city, role)
     * @return array Response array with success status and message/data
     */
    public function register($data) {
        // Validate required fields
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return [
                'success' => false,
                'message' => 'Name, email, and password are required.'
            ];
        }
        
        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format.'
            ];
        }
        
        // Validate password strength (minimum 6 characters)
        if (strlen($data['password']) < 6) {
            return [
                'success' => false,
                'message' => 'Password must be at least 6 characters long.'
            ];
        }
        
        // Extract data with defaults
        $name = trim($data['name']);
        $email = trim($data['email']);
        $password = $data['password'];
        $phone = isset($data['phone']) ? trim($data['phone']) : null;
        $country = isset($data['country']) ? trim($data['country']) : null;
        $city = isset($data['city']) ? trim($data['city']) : null;
        $image = isset($data['image']) ? trim($data['image']) : null;
        $role = isset($data['role']) ? trim($data['role']) : 'customer';
        
        // Validate role
        if (!in_array($role, ['customer', 'seller', 'admin'])) {
            $role = 'customer';
        }
        
        // Add customer
        $customerId = $this->customer->addCustomer($name, $email, $password, $phone, $country, $city, $image, $role);
        
        if ($customerId) {
            return [
                'success' => true,
                'message' => 'Customer registered successfully.',
                'customer_id' => $customerId
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Registration failed. Email may already exist.'
            ];
        }
    }
    
    /**
     * Login customer controller method
     * Invokes customer_class::get() method to verify credentials
     * 
     * @param array $kwargs Associative array containing 'email' and 'password'
     * @return array Response array with success status and message/customer data
     */
    public function login_customer_ctr($kwargs) {
        // Validate input parameters
        if (empty($kwargs['email']) || empty($kwargs['password'])) {
            return [
                'success' => false,
                'message' => 'Email and password are required.'
            ];
        }
        
        // Validate email format
        if (!filter_var($kwargs['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format.'
            ];
        }
        
        // Invoke customer_class::get() method with email and password
        $result = $this->customer->get($kwargs['email'], $kwargs['password']);
        
        // Check if login was successful
        if ($result['success']) {
            $customer = $result['customer'];
            
            // Set session variables
            // Note: customer_id from query is actually user_id (mapped in getCustomerByEmail)
            $_SESSION['user_id'] = $customer['customer_id']; // user_id is the primary key
            $_SESSION['customer_id'] = $customer['customer_id'];
            $_SESSION['customer_name'] = $customer['customer_name'];
            $_SESSION['customer_email'] = $customer['customer_email'];
            $roleNo = isset($customer['user_role']) ? (int)$customer['user_role'] : null;
            $_SESSION['user_role_no'] = $roleNo;
            $_SESSION['user_role'] = getUserRole($roleNo);
            $_SESSION['user_role_str'] = getUserRole($roleNo);
            
            // Debug: Log seller_verified from query result
            error_log("Controller login_customer_ctr - Role: $roleNo, seller_verified from DB: " . (isset($customer['seller_verified']) ? var_export($customer['seller_verified'], true) : 'not set'));
            
            $_SESSION['is_seller_verified'] = isset($customer['seller_verified']) ? (bool)$customer['seller_verified'] : null;
            $_SESSION['customer_image'] = $customer['customer_image'] ?? null;
            $_SESSION['customer_phone'] = $customer['customer_phone'] ?? null;
            $_SESSION['customer_country'] = $customer['customer_country'] ?? null;
            $_SESSION['customer_city'] = $customer['customer_city'] ?? null;
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            return [
                'success' => true,
                'message' => 'Login successful.',
                'customer' => $customer
            ];
        } else {
            return [
                'success' => false,
                'message' => $result['message'] ?? 'Invalid email or password.'
            ];
        }
    }
    
    /**
     * Handle customer login
     * 
     * @param string $email Customer email
     * @param string $password Customer password
     * @return array Response array with success status and customer data
     */
    public function login($email, $password) {
        // Validate input
        if (empty($email) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Email and password are required.'
            ];
        }
        
        // Verify login credentials
        $customer = $this->customer->verifyLogin($email, $password);
        
        if ($customer) {
            // Set session data
            // Note: customer_id from query is actually user_id (mapped in getCustomerByEmail)
            $_SESSION['user_id'] = $customer['customer_id']; // user_id is the primary key
            $_SESSION['customer_id'] = $customer['customer_id'];
            $_SESSION['customer_name'] = $customer['customer_name'];
            $_SESSION['customer_email'] = $customer['customer_email'];
            $roleNo = isset($customer['user_role']) ? (int)$customer['user_role'] : null;
            $_SESSION['user_role_no'] = $roleNo;
            $_SESSION['user_role'] = getUserRole($roleNo);
            $_SESSION['user_role_str'] = getUserRole($roleNo);
            $_SESSION['is_seller_verified'] = isset($customer['seller_verified']) ? (bool)$customer['seller_verified'] : null;
            $_SESSION['customer_image'] = $customer['customer_image'] ?? null;
            $_SESSION['logged_in'] = true;
            
            return [
                'success' => true,
                'message' => 'Login successful.',
                'customer' => $customer
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Invalid email or password.'
            ];
        }
    }
    
    /**
     * Handle customer update/edit
     * 
     * @param int $customerId Customer ID to update
     * @param array $data Data to update
     * @return array Response array with success status and message
     */
    public function update($customerId, $data) {
        // Validate customer ID
        if (empty($customerId) || !is_numeric($customerId)) {
            return [
                'success' => false,
                'message' => 'Invalid customer ID.'
            ];
        }
        
        // Validate email if provided
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format.'
            ];
        }
        
        // Validate password if provided
        if (isset($data['password']) && strlen($data['password']) < 6) {
            return [
                'success' => false,
                'message' => 'Password must be at least 6 characters long.'
            ];
        }
        
        // Validate role if provided
        if (isset($data['role']) && !in_array($data['role'], ['customer', 'seller', 'admin'])) {
            return [
                'success' => false,
                'message' => 'Invalid role specified.'
            ];
        }
        
        // Update customer
        $result = $this->customer->editCustomer($customerId, $data);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Customer updated successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Update failed. Customer may not exist or email may already be in use.'
            ];
        }
    }
    
    /**
     * Handle customer deletion
     * 
     * @param int $customerId Customer ID to delete
     * @return array Response array with success status and message
     */
    public function delete($customerId) {
        // Validate customer ID
        if (empty($customerId) || !is_numeric($customerId)) {
            return [
                'success' => false,
                'message' => 'Invalid customer ID.'
            ];
        }
        
        // Delete customer
        $result = $this->customer->deleteCustomer($customerId);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Customer deleted successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Delete failed. Customer may not exist.'
            ];
        }
    }
    
    /**
     * Get customer by ID
     * 
     * @param int $customerId Customer ID
     * @return array Response array with customer data
     */
    public function getById($customerId) {
        // Validate customer ID
        if (empty($customerId) || !is_numeric($customerId)) {
            return [
                'success' => false,
                'message' => 'Invalid customer ID.'
            ];
        }
        
        $customer = $this->customer->getCustomer($customerId);
        
        if ($customer) {
            // Remove password from response
            unset($customer['customer_pass']);
            return [
                'success' => true,
                'customer' => $customer
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Customer not found.'
            ];
        }
    }
    
    /**
     * Get customer by email
     * 
     * @param string $email Customer email
     * @return array Response array with customer data
     */
    public function getByEmail($email) {
        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format.'
            ];
        }
        
        $customer = $this->customer->getCustomerByEmail($email);
        
        if ($customer) {
            // Remove password from response
            unset($customer['customer_pass']);
            return [
                'success' => true,
                'customer' => $customer
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Customer not found.'
            ];
        }
    }
    
    /**
     * Get all customers with optional filters
     * 
     * @param string $role Filter by role (optional)
     * @param int $limit Limit number of results (optional)
     * @param int $offset Offset for pagination (optional)
     * @return array Response array with customers list
     */
    public function getAll($role = null, $limit = null, $offset = null) {
        $customers = $this->customer->getAllCustomers($role, $limit, $offset);
        
        // Remove passwords from all customers
        foreach ($customers as &$customer) {
            unset($customer['customer_pass']);
        }
        
        return [
            'success' => true,
            'customers' => $customers,
            'count' => count($customers)
        ];
    }
    
    /**
     * Check if email exists
     * 
     * @param string $email Email to check
     * @param int $excludeCustomerId Customer ID to exclude (optional)
     * @return array Response array with exists status
     */
    public function checkEmail($email, $excludeCustomerId = null) {
        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format.'
            ];
        }
        
        $exists = $this->customer->emailExists($email, $excludeCustomerId);
        
        return [
            'success' => true,
            'exists' => $exists,
            'message' => $exists ? 'Email already exists.' : 'Email is available.'
        ];
    }
    
    /**
     * Search customers
     * 
     * @param string $searchTerm Search term
     * @return array Response array with matching customers
     */
    public function search($searchTerm) {
        // Validate search term
        if (empty($searchTerm) || strlen(trim($searchTerm)) < 2) {
            return [
                'success' => false,
                'message' => 'Search term must be at least 2 characters long.'
            ];
        }
        
        $customers = $this->customer->searchCustomers($searchTerm);
        
        // Remove passwords from all customers
        foreach ($customers as &$customer) {
            unset($customer['customer_pass']);
        }
        
        return [
            'success' => true,
            'customers' => $customers,
            'count' => count($customers)
        ];
    }
    
    /**
     * Get customer count
     * 
     * @param string $role Filter by role (optional)
     * @return array Response array with count
     */
    public function getCount($role = null) {
        $count = $this->customer->getCustomerCount($role);
        
        return [
            'success' => true,
            'count' => $count
        ];
    }
    
    /**
     * Update customer password
     * 
     * @param int $customerId Customer ID
     * @param string $newPassword New password
     * @return array Response array with success status
     */
    public function updatePassword($customerId, $newPassword) {
        // Validate customer ID
        if (empty($customerId) || !is_numeric($customerId)) {
            return [
                'success' => false,
                'message' => 'Invalid customer ID.'
            ];
        }
        
        // Validate password
        if (empty($newPassword) || strlen($newPassword) < 6) {
            return [
                'success' => false,
                'message' => 'Password must be at least 6 characters long.'
            ];
        }
        
        $result = $this->customer->updatePassword($customerId, $newPassword);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Password updated successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Password update failed.'
            ];
        }
    }
    
    /**
     * Update customer role
     * 
     * @param int $customerId Customer ID
     * @param string $role New role
     * @return array Response array with success status
     */
    public function updateRole($customerId, $role) {
        // Validate customer ID
        if (empty($customerId) || !is_numeric($customerId)) {
            return [
                'success' => false,
                'message' => 'Invalid customer ID.'
            ];
        }
        
        // Validate role
        if (!in_array($role, ['customer', 'seller', 'admin'])) {
            return [
                'success' => false,
                'message' => 'Invalid role specified.'
            ];
        }
        
        $result = $this->customer->updateRole($customerId, $role);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Role updated successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Role update failed.'
            ];
        }
    }
    
    /**
     * Handle logout
     * 
     * @return array Response array with success status
     */
    public function logout() {
        // Clear session
        $_SESSION = array();
        
        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Logged out successfully.'
        ];
    }
    
    /**
     * Send JSON response
     * 
     * @param array $data Data to send as JSON
     * @param int $statusCode HTTP status code
     * @return void
     */
    public function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

// Handle direct controller calls (for API endpoints)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $controller = new CustomerController();
    $action = $_POST['action'];
    $response = [];
    
    switch ($action) {
        case 'register':
            $response = $controller->register($_POST);
            break;
            
        case 'login':
            $response = $controller->login($_POST['email'] ?? '', $_POST['password'] ?? '');
            break;
            
        case 'update':
            $customerId = $_POST['customer_id'] ?? $_SESSION['customer_id'] ?? null;
            $response = $controller->update($customerId, $_POST);
            break;
            
        case 'delete':
            $customerId = $_POST['customer_id'] ?? null;
            $response = $controller->delete($customerId);
            break;
            
        case 'getById':
            $customerId = $_POST['customer_id'] ?? null;
            $response = $controller->getById($customerId);
            break;
            
        case 'getByEmail':
            $email = $_POST['email'] ?? '';
            $response = $controller->getByEmail($email);
            break;
            
        case 'getAll':
            $role = $_POST['role'] ?? null;
            $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : null;
            $offset = isset($_POST['offset']) ? (int)$_POST['offset'] : null;
            $response = $controller->getAll($role, $limit, $offset);
            break;
            
        case 'checkEmail':
            $email = $_POST['email'] ?? '';
            $excludeId = isset($_POST['exclude_id']) ? (int)$_POST['exclude_id'] : null;
            $response = $controller->checkEmail($email, $excludeId);
            break;
            
        case 'search':
            $searchTerm = $_POST['search_term'] ?? '';
            $response = $controller->search($searchTerm);
            break;
            
        case 'getCount':
            $role = $_POST['role'] ?? null;
            $response = $controller->getCount($role);
            break;
            
        case 'updatePassword':
            $customerId = $_POST['customer_id'] ?? $_SESSION['customer_id'] ?? null;
            $newPassword = $_POST['new_password'] ?? '';
            $response = $controller->updatePassword($customerId, $newPassword);
            break;
            
        case 'updateRole':
            $customerId = $_POST['customer_id'] ?? null;
            $role = $_POST['role'] ?? '';
            $response = $controller->updateRole($customerId, $role);
            break;
            
        case 'logout':
            $response = $controller->logout();
            break;
            
        default:
            $response = [
                'success' => false,
                'message' => 'Invalid action specified.'
            ];
    }
    
    $controller->jsonResponse($response);
}


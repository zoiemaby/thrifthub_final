<?php
/**
 * Customer Class
 * ThriftHub - Customer Management Class
 * 
 * This class extends the Database class and provides methods
 * for managing customers (add, edit, delete, retrieve, etc.)
 */

require_once __DIR__ . '/../settings/db_class.php';

class Customer extends Database {
    
    /**
     * Add a new customer to the database
     * 
     * @param string $name Customer name
     * @param string $email Customer email (must be unique)
     * @param string $password Customer password (will be hashed)
     * @param string $phone Customer phone (optional)
     * @param string $country Customer country (optional)
     * @param string $city Customer city (optional)
     * @param string $image Customer image path (optional)
     * @param string $role User role: 'customer', 'seller', or 'admin' (default: 'customer')
     * @return int|false Returns customer_id on success, false on failure
     */
    public function addCustomer($name, $email, $password, $phone = null, $country = null, $city = null, $image = null, $role = 'customer') {
        // Normalize + escape inputs
        $name = trim($name);
        $email = strtolower(trim($email));
        $nameEsc = $this->escape($name);
        $emailEsc = $this->escape($email);
        $phoneEsc = $phone ? "'" . $this->escape($phone) . "'" : 'NULL';
        $roleEsc = $this->escape($role);

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $hashedEsc = $this->escape($hashedPassword);

        // Unique check against users table
        if ($this->emailExists($email)) {
            return false;
        }

        // Insert into users (core identity table)
        $sqlUser = "INSERT INTO users (name, email, password, phone_number, user_role) VALUES ('$nameEsc', '$emailEsc', '$hashedEsc', $phoneEsc, 
            (SELECT role_no FROM roles WHERE role_description = '$roleEsc' LIMIT 1))";

        if ($this->query($sqlUser)) {
            $userId = $this->insert_id();
            // If registering a customer, also create row in customers table
            if ($role === 'customer') {
                $sqlCust = "INSERT INTO customers (user_id) VALUES ($userId)";
                $this->query($sqlCust);
            }
            return $userId;
        }

        return false;
    }
    
    /**
     * Edit/Update customer information
     * 
     * @param int $customerId Customer ID to update
     * @param array $data Associative array of fields to update (name, email, phone, country, city, image, role)
     * @return bool Returns true on success, false on failure
     */
    public function editCustomer($customerId, $data) {
        // Escape customer ID
        $customerId = (int)$customerId;
        
        if ($customerId <= 0) {
            return false;
        }
        
        // Check if customer exists
        if (!$this->getCustomer($customerId)) {
            return false;
        }
        
        // Build update fields
        $updateFields = [];
        
        if (isset($data['name'])) {
            $updateFields[] = "customer_name = '" . $this->escape($data['name']) . "'";
        }
        
        if (isset($data['email'])) {
            $email = $this->escape($data['email']);
            // Check if email is being changed and if new email already exists
            $currentCustomer = $this->getCustomer($customerId);
            if ($currentCustomer['customer_email'] !== $data['email'] && $this->emailExists($data['email'])) {
                return false; // Email already exists
            }
            $updateFields[] = "customer_email = '$email'";
        }
        
        if (isset($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $updateFields[] = "customer_pass = '" . $this->escape($hashedPassword) . "'";
        }
        
        if (isset($data['phone'])) {
            $phone = $data['phone'] ? "'" . $this->escape($data['phone']) . "'" : 'NULL';
            $updateFields[] = "customer_phone = $phone";
        }
        
        if (isset($data['country'])) {
            $country = $data['country'] ? "'" . $this->escape($data['country']) . "'" : 'NULL';
            $updateFields[] = "customer_country = $country";
        }
        
        if (isset($data['city'])) {
            $city = $data['city'] ? "'" . $this->escape($data['city']) . "'" : 'NULL';
            $updateFields[] = "customer_city = $city";
        }
        
        if (isset($data['image'])) {
            $image = $data['image'] ? "'" . $this->escape($data['image']) . "'" : 'NULL';
            $updateFields[] = "customer_image = $image";
        }
        
        if (isset($data['role'])) {
            $updateFields[] = "user_role = '" . $this->escape($data['role']) . "'";
        }
        
        // If no fields to update
        if (empty($updateFields)) {
            return false;
        }
        
        // Build and execute SQL query
        $sql = "UPDATE customer SET " . implode(', ', $updateFields) . " WHERE customer_id = $customerId";
        
        return $this->query($sql) !== false;
    }
    
    /**
     * Delete a customer from the database
     * 
     * @param int $customerId Customer ID to delete
     * @return bool Returns true on success, false on failure
     */
    public function deleteCustomer($customerId) {
        // Escape customer ID
        $customerId = (int)$customerId;
        
        if ($customerId <= 0) {
            return false;
        }
        
        // Check if customer exists
        if (!$this->getCustomer($customerId)) {
            return false;
        }
        
        // Delete customer
        $sql = "DELETE FROM customer WHERE customer_id = $customerId";
        
        return $this->query($sql) !== false;
    }
    
    /**
     * Get a single customer by ID
     * 
     * @param int $customerId Customer ID
     * @return array|false Returns customer data as associative array, or false if not found
     */
    public function getCustomer($customerId) {
        $customerId = (int)$customerId;
        $sql = "SELECT * FROM customer WHERE customer_id = $customerId";
        return $this->fetchOne($sql);
    }
    
    /**
     * Get a customer by email
     * 
     * @param string $email Customer email
     * @return array|false Returns customer data as associative array, or false if not found
     */
    public function getCustomerByEmail($email) {
        $email = strtolower(trim($email));
        $emailEsc = $this->escape($email);
        // Map users fields to legacy keys expected by controllers
        // Also include seller details when role is 'seller' (fields will be NULL for non-sellers)
        $sql = "SELECT 
                    u.user_id   AS customer_id,
                    u.name      AS customer_name,
                    u.email     AS customer_email,
                    u.password  AS customer_pass,
                    u.phone_number AS customer_phone,
                    r.role_description AS user_desc,
                    r.role_no   AS user_role,
                    s.shop_name       AS seller_shop_name,
                    s.type_id         AS seller_type_id,
                    s.sector_id       AS seller_sector_id,
                    s.store_logo      AS seller_store_logo,
                    s.store_banner    AS seller_store_banner,
                    s.description     AS seller_description,
                    s.verified        AS seller_verified
                FROM users u
                JOIN roles r ON r.role_no = u.user_role
                LEFT JOIN sellers s ON s.user_id = u.user_id
                WHERE u.email = '$emailEsc'";
        return $this->fetchOne($sql);
    }
    
    /**
     * Get all customers
     * 
     * @param string $role Filter by role (optional: 'customer', 'seller', 'admin')
     * @param int $limit Limit number of results (optional)
     * @param int $offset Offset for pagination (optional)
     * @return array Returns array of customer data
     */
    public function getAllCustomers($role = null, $limit = null, $offset = null) {
        $sql = "SELECT * FROM customer";
        
        // Add role filter if specified
        if ($role) {
            $role = $this->escape($role);
            $sql .= " WHERE user_role = '$role'";
        }
        
        // Order by created_at descending
        $sql .= " ORDER BY created_at DESC";
        
        // Add limit and offset if specified
        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = $offset !== null ? (int)$offset : 0;
            $sql .= " LIMIT $offset, $limit";
        }
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Check if email already exists in database
     * 
     * @param string $email Email to check
     * @param int $excludeCustomerId Customer ID to exclude from check (for updates)
     * @return bool Returns true if email exists, false otherwise
     */
    public function emailExists($email, $excludeCustomerId = null) {
        $email = $this->escape($email);
        $sql = "SELECT user_id FROM users WHERE email = '$email'";
        
        if ($excludeCustomerId !== null) {
            $excludeCustomerId = (int)$excludeCustomerId;
            $sql .= " AND customer_id != $excludeCustomerId";
        }
        
        $result = $this->fetchOne($sql);
        return $result !== false;
    }
    
    /**
     * Get customer by email and verify password
     * Checks if the password input matches the password stored in database
     * 
     * @param string $email Customer email address
     * @param string $password Customer password (plain text)
     * @return array|false Returns customer data array on success, false on failure
     *                     Returns array with 'success' => true/false and 'customer' data or 'message'
     */
    public function get($email, $password = null) {
        // Get customer by email
        $customer = $this->getCustomerByEmail($email);
        
        // If customer not found
        if (!$customer) {
            return [
                'success' => false,
                'message' => 'Invalid email address or customer not found.'
            ];
        }
        
        // If password is provided, verify it
        if ($password !== null) {
            if (!password_verify($password, $customer['customer_pass'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid password.'
                ];
            }
        }
        
        // Remove password from returned data for security
        unset($customer['customer_pass']);
        
        // Return success response with customer data
        return [
            'success' => true,
            'customer' => $customer,
            'message' => 'Customer found and password verified.'
        ];
    }
    
    /**
     * Verify customer login credentials
     * 
     * @param string $email Customer email
     * @param string $password Customer password (plain text)
     * @return array|false Returns customer data on success, false on failure
     */
    public function verifyLogin($email, $password) {
        $customer = $this->getCustomerByEmail($email);
        
        if ($customer && password_verify($password, $customer['customer_pass'])) {
            // Remove password from returned data
            unset($customer['customer_pass']);
            return $customer;
        }
        
        return false;
    }
    
    /**
     * Get total count of customers
     * 
     * @param string $role Filter by role (optional)
     * @return int Total number of customers
     */
    public function getCustomerCount($role = null) {
        $sql = "SELECT COUNT(*) as total FROM customer";
        
        if ($role) {
            $role = $this->escape($role);
            $sql .= " WHERE user_role = '$role'";
        }
        
        $result = $this->fetchOne($sql);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Search customers by name or email
     * 
     * @param string $searchTerm Search term
     * @return array Returns array of matching customers
     */
    public function searchCustomers($searchTerm) {
        $searchTerm = $this->escape($searchTerm);
        $sql = "SELECT * FROM customer 
                WHERE customer_name LIKE '%$searchTerm%' 
                OR customer_email LIKE '%$searchTerm%'
                ORDER BY customer_name ASC";
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Update customer password
     * 
     * @param int $customerId Customer ID
     * @param string $newPassword New password (plain text)
     * @return bool Returns true on success, false on failure
     */
    public function updatePassword($customerId, $newPassword) {
        $customerId = (int)$customerId;
        
        if ($customerId <= 0) {
            return false;
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $hashedPassword = $this->escape($hashedPassword);
        
        $sql = "UPDATE customer SET customer_pass = '$hashedPassword' WHERE customer_id = $customerId";
        
        return $this->query($sql) !== false;
    }
    
    /**
     * Update customer role
     * 
     * @param int $customerId Customer ID
     * @param string $role New role ('customer', 'seller', 'admin')
     * @return bool Returns true on success, false on failure
     */
    public function updateRole($customerId, $role) {
        $customerId = (int)$customerId;
        $role = $this->escape($role);
        
        if ($customerId <= 0 || !in_array($role, ['customer', 'seller', 'admin'])) {
            return false;
        }
        
        $sql = "UPDATE customer SET user_role = '$role' WHERE customer_id = $customerId";
        
        return $this->query($sql) !== false;
    }
}


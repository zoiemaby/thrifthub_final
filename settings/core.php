<?php
/**
 * Core System Initialization
 * ThriftHub - Core system setup and utility functions
 * 
 * This file initializes the system by:
 * - Starting the session
 * - Loading database credentials
 * - Loading database class
 * - Instantiating database connection
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require database credentials file
require_once __DIR__ . '/db_cred.php';

// Require database class file
require_once __DIR__ . '/db_class.php';

// Instantiate database connection
// This creates a global $db object that can be used throughout the application
$db = new Database();

/**
 * Role Constants - Using numbers for roles (aligned with DB roles)
 * 1 = admin (super admin)
 * 2 = customer (buyer)
 * 3 = seller
 */
define('ROLE_ADMIN', 1);
define('ROLE_CUSTOMER', 2);
define('ROLE_SELLER', 3);

/**
 * Get user role name based on role ID
 * 
 * Role mapping:
 * 1 = admin (super admin)
 * 3 = seller
 * 2 = buyer/customer
 * 
 * @param int $roleId The role ID (1, 2, or 3)
 * @return string|false Returns role name ('admin', 'seller', 'customer') or false if invalid
 */
function getUserRole($roleId) {
    $roles = [
        1 => 'admin',
        2 => 'customer',
        3 => 'seller'
    ];
    return isset($roles[$roleId]) ? $roles[$roleId] : false;
}

/**
 * Convert role string (from database) to role number
 * 
 * @param string $roleString Role string from database ('admin', 'seller', 'customer')
 * @return int|false Returns role number (1, 2, 3) or false if invalid
 */
function getRoleNumber($roleString) {
    $roleMap = [
        'admin' => 1,
        'customer' => 2,
        'seller' => 3
    ];
    return isset($roleMap[$roleString]) ? $roleMap[$roleString] : false;
}

/**
 * Convert role number to role string (for database)
 * 
 * @param int $roleNumber Role number (1, 2, 3)
 * @return string|false Returns role string ('admin', 'seller', 'customer') or false if invalid
 */
function getRoleString($roleNumber) {
    return getUserRole($roleNumber);
}

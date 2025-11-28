<?php
/**
 * Register Customer Action
 * ThriftHub - Customer Registration Handler
 * 
 * This script receives data from the customer registration form,
 * invokes the relevant functions from the customer controller,
 * and returns a message to the caller.
 */

// Include the customer controller
require_once __DIR__ . '/../controllers/customer_controller.php';

// Set content type for text response
header('Content-Type: text/plain; charset=utf-8');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed. Only POST requests are accepted.';
    exit;
}

// Initialize the customer controller
$controller = new CustomerController();

// Collect and sanitize form data
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';
$terms = isset($_POST['terms']) ? $_POST['terms'] : '';

// Validate required fields
if (empty($fullname)) {
    http_response_code(400);
    echo 'Full name is required.';
    exit;
}

if (empty($email)) {
    http_response_code(400);
    echo 'Email address is required.';
    exit;
}

if (empty($password)) {
    http_response_code(400);
    echo 'Password is required.';
    exit;
}

if (empty($confirmPassword)) {
    http_response_code(400);
    echo 'Please confirm your password.';
    exit;
}

// Validate password confirmation
if ($password !== $confirmPassword) {
    http_response_code(400);
    echo 'Passwords do not match.';
    exit;
}

// Validate terms acceptance
if (empty($terms)) {
    http_response_code(400);
    echo 'You must agree to the Terms of Service and Privacy Policy.';
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Invalid email format.';
    exit;
}

// Validate password strength
if (strlen($password) < 6) {
    http_response_code(400);
    echo 'Password must be at least 6 characters long.';
    exit;
}

// Validate fullname length
if (strlen($fullname) < 2) {
    http_response_code(400);
    echo 'Full name must be at least 2 characters long.';
    exit;
}

// Determine requested role (optional toggle). Accept only 'customer' or 'seller'. Default 'customer'.
$requestedRole = isset($_POST['role']) ? strtolower(trim($_POST['role'])) : 'customer';
if (!in_array($requestedRole, ['customer','seller'])) {
    $requestedRole = 'customer';
}

// Prepare data for controller (map fullname to name as expected by controller)
$registrationData = [
    'name' => $fullname,
    'email' => $email,
    'password' => $password,
    'phone' => !empty($phone) ? $phone : null,
    'country' => null,
    'city' => null,
    'image' => null,
    'role' => $requestedRole
];

// Call the controller's register method
$result = $controller->register($registrationData);

// Handle the response
if ($result['success']) {
    // Registration successful
    http_response_code(200);
    echo 'Registration successful! Redirecting to login...';
} else {
    // Registration failed
    http_response_code(400);
    echo $result['message'] ?? 'Registration failed. Please try again.';
}

exit;


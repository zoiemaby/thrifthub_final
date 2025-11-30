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

// For API consistency, return JSON
header('Content-Type: application/json; charset=utf-8');

// Helper to send JSON and exit
function send_json($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['success' => false, 'message' => 'Method not allowed. Only POST requests are accepted.'], 405);
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
    send_json(['success' => false, 'message' => 'Full name is required.'], 400);
}

if (empty($email)) {
    send_json(['success' => false, 'message' => 'Email address is required.'], 400);
}

if (empty($password)) {
    send_json(['success' => false, 'message' => 'Password is required.'], 400);
}

if (empty($confirmPassword)) {
    send_json(['success' => false, 'message' => 'Please confirm your password.'], 400);
}

// Validate password confirmation
if ($password !== $confirmPassword) {
    send_json(['success' => false, 'message' => 'Passwords do not match.'], 400);
}

// Validate terms acceptance
if (empty($terms)) {
    send_json(['success' => false, 'message' => 'You must agree to the Terms of Service and Privacy Policy.'], 400);
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_json(['success' => false, 'message' => 'Invalid email format.'], 400);
}

// Validate password strength
if (strlen($password) < 6) {
    send_json(['success' => false, 'message' => 'Password must be at least 6 characters long.'], 400);
}

// Validate fullname length
if (strlen($fullname) < 2) {
    send_json(['success' => false, 'message' => 'Full name must be at least 2 characters long.'], 400);
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
// Guard controller call to avoid 500s on live
try {
    $result = $controller->register($registrationData);
} catch (Throwable $e) {
    error_log('register_customer_action fatal: ' . $e->getMessage());
    send_json(['success' => false, 'message' => 'Server error. Please try again later.'], 500);
}

// Handle the response
if (!is_array($result)) {
    send_json(['success' => false, 'message' => 'Unexpected response from server.'], 500);
}

if ($result['success']) {
    send_json(['success' => true, 'message' => 'Registration successful.']);
} else {
    $msg = $result['message'] ?? 'Registration failed. Please try again.';
    // If email exists, make message clear
    send_json(['success' => false, 'message' => $msg], 400);
}


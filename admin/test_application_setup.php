<?php
// Simple test to check if application submission works
require_once __DIR__ . '/../settings/core.php';

echo "<h1>Seller Application Test</h1>";

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>❌ You are NOT logged in. Please <a href='../view/login.php'>login first</a>.</p>";
    exit;
}

echo "<p style='color: green;'>✅ You are logged in as User ID: " . $_SESSION['user_id'] . "</p>";
echo "<p>User Role: " . ($_SESSION['user_role'] ?? 'not set') . "</p>";

// Check database connection
require_once __DIR__ . '/../settings/db_class.php';
$db = new db_connection();
echo "<p style='color: green;'>✅ Database connection successful</p>";

// Check if seller_applications table exists
$query = "SHOW TABLES LIKE 'seller_applications'";
$result = $db->db_query($query);
if ($result && $result->num_rows > 0) {
    echo "<p style='color: green;'>✅ seller_applications table exists</p>";
} else {
    echo "<p style='color: red;'>❌ seller_applications table does NOT exist</p>";
}

// Check for existing applications
$userId = (int)$_SESSION['user_id'];
$query = "SELECT * FROM seller_applications WHERE user_id = $userId";
$result = $db->db_fetch_all($query);
if ($result && count($result) > 0) {
    echo "<p style='color: orange;'>⚠️ You already have an application:</p>";
    echo "<pre>" . print_r($result, true) . "</pre>";
} else {
    echo "<p>No existing applications found for your account.</p>";
}

// Check if uploads directory exists
$uploadsDir = __DIR__ . '/../uploads';
if (!is_dir($uploadsDir)) {
    echo "<p style='color: red;'>❌ Uploads directory does not exist. Creating it...</p>";
    mkdir($uploadsDir, 0777, true);
    echo "<p style='color: green;'>✅ Created uploads directory</p>";
} else {
    echo "<p style='color: green;'>✅ Uploads directory exists</p>";
    echo "<p>Writable: " . (is_writable($uploadsDir) ? "Yes ✅" : "No ❌") . "</p>";
}

// Check if classes are available
echo "<h2>Class Check</h2>";
require_once __DIR__ . '/../controllers/sellerApplication_controller.php';
$controller = new SellerApplicationController();
echo "<p style='color: green;'>✅ SellerApplicationController loaded successfully</p>";

echo "<hr>";
echo "<p><a href='../view/seller_apply.php'>Go to Seller Application Form</a></p>";
echo "<p><a href='test_submit_application.php'>Test Form Submission</a></p>";
?>

<?php
/**
 * Category Migration Checker
 * ThriftHub - Check if user_id column exists in categories table
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/db_class.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    die('Please log in first.');
}

$userRole = $_SESSION['user_role'] ?? '';
if ($userRole !== 'admin' && $userRole !== '1') {
    die('Only administrators can access this page.');
}

$db = new Database();
$conn = $db->getConnection();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Category Migration Check</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #0F5E4D; }
        .success { color: #2E7D32; background: #E8F5E9; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { color: #D32F2F; background: #FFEBEE; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { color: #1976D2; background: #E3F2FD; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .sql-box { background: #f5f5f5; padding: 15px; border-radius: 5px; font-family: monospace; margin: 10px 0; overflow-x: auto; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Category Migration Status Check</h1>
        
        <?php
        // Check if user_id column exists
        $checkColumn = "SHOW COLUMNS FROM categories LIKE 'user_id'";
        $result = $conn->query($checkColumn);
        
        if ($result && $result->num_rows > 0) {
            echo '<div class="success">✅ <strong>Migration Complete!</strong><br>The <code>user_id</code> column exists in the categories table.</div>';
            
            // Check if there are categories without user_id
            $checkNull = "SELECT COUNT(*) as count FROM categories WHERE user_id = 0 OR user_id IS NULL";
            $nullResult = $conn->query($checkNull);
            if ($nullResult) {
                $row = $nullResult->fetch_assoc();
                if ($row['count'] > 0) {
                    echo '<div class="info">⚠️ <strong>Warning:</strong> Found ' . $row['count'] . ' categories without a user_id assigned.<br>';
                    echo 'Run this SQL to assign them to your admin account:</div>';
                    echo '<div class="sql-box">UPDATE `categories` SET `user_id` = ' . (int)$_SESSION['user_id'] . ' WHERE `user_id` = 0 OR `user_id` IS NULL;</div>';
                }
            }
        } else {
            echo '<div class="error">❌ <strong>Migration Not Run!</strong><br>The <code>user_id</code> column does NOT exist in the categories table.</div>';
            echo '<div class="info"><strong>Action Required:</strong> Run the following SQL migration in phpMyAdmin or your MySQL client:</div>';
            echo '<div class="sql-box">';
            echo "-- Add user_id column to categories table\n";
            echo "ALTER TABLE `categories` \n";
            echo "ADD COLUMN `user_id` INT(11) NOT NULL AFTER `cat_id`;\n\n";
            echo "-- Add foreign key constraint\n";
            echo "ALTER TABLE `categories` \n";
            echo "ADD CONSTRAINT `categories_fk_user`\n";
            echo "  FOREIGN KEY (`user_id`)\n";
            echo "  REFERENCES `users` (`user_id`)\n";
            echo "  ON DELETE CASCADE\n";
            echo "  ON UPDATE CASCADE;\n\n";
            echo "-- Add index for performance\n";
            echo "ALTER TABLE `categories`\n";
            echo "ADD INDEX `idx_user_id` (`user_id`);\n\n";
            echo "-- If you have existing categories, assign them to your admin account:\n";
            echo "-- UPDATE `categories` SET `user_id` = " . (int)$_SESSION['user_id'] . " WHERE `user_id` = 0 OR `user_id` IS NULL;";
            echo '</div>';
        }
        
        // Show current categories table structure
        echo '<h2>Current Categories Table Structure</h2>';
        $structure = $conn->query("DESCRIBE categories");
        if ($structure) {
            echo '<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">';
            echo '<tr style="background: #f0f0f0;"><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>';
            while ($row = $structure->fetch_assoc()) {
                echo '<tr>';
                echo '<td><strong>' . htmlspecialchars($row['Field']) . '</strong></td>';
                echo '<td>' . htmlspecialchars($row['Type']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Null']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Key']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Default'] ?? 'NULL') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e0e0e0;">
            <a href="../seller/category.php" style="color: #0F5E4D; text-decoration: none; font-weight: bold;">← Back to Category Management</a>
        </div>
    </div>
</body>
</html>


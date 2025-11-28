<?php
// Test form submission
require_once __DIR__ . '/../settings/core.php';

if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    die("Please login first: <a href='../view/login.php'>Login</a>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Application Submission</title>
</head>
<body>
    <h1>Test Seller Application Submission</h1>
    <p>Logged in as User ID: <?php echo $_SESSION['user_id']; ?></p>
    
    <form action="../actions/apply_seller_action.php" method="POST" enctype="multipart/form-data">
        <div style="margin-bottom: 15px;">
            <label>ID Document:</label><br>
            <input type="file" name="id_path" required>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label>Address Proof:</label><br>
            <input type="file" name="address_path" required>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label>Selfie with ID:</label><br>
            <input type="file" name="selfie_path" required>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label>Mobile Money Number:</label><br>
            <input type="text" name="momo_number" value="0241234567" required>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label>Bank Name:</label><br>
            <input type="text" name="bank_name" value="GCB Bank" required>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label>Account Number:</label><br>
            <input type="text" name="account_number" value="1234567890" required>
        </div>
        
        <button type="submit" style="padding: 10px 20px; background: #0F5E4D; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Submit Test Application
        </button>
    </form>
    
    <hr style="margin: 30px 0;">
    
    <div id="result"></div>
    
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('../actions/apply_seller_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('result').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                if (data.success) {
                    document.getElementById('result').style.color = 'green';
                } else {
                    document.getElementById('result').style.color = 'red';
                }
            })
            .catch(error => {
                document.getElementById('result').innerHTML = '<pre style="color: red;">Error: ' + error.message + '</pre>';
            });
        });
    </script>
    
    <p><a href="test_application_setup.php">Back to Setup Test</a></p>
    <p><a href="../view/seller_apply.php">Go to Real Form</a></p>
</body>
</html>

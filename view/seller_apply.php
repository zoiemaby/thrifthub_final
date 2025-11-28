<?php
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/sellerApplication_controller.php';
require_once __DIR__ . '/../classes/sector_class.php';
require_once __DIR__ . '/../classes/businessType_class.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch business types and sectors from database
$sectorClass = new Sector();
$businessTypeClass = new BusinessType();

$sectors = $sectorClass->get_all_sectors();
$businessTypes = $businessTypeClass->get_all_business_types();

// Check if user already has a pending/approved application
$appController = new SellerApplicationController();
$userId = (int)$_SESSION['user_id'];
$existingApp = $appController->get_application_by_user_ctrl($userId);

$hasApplication = false;
$applicationStatus = '';
if ($existingApp['success']) {
    $hasApplication = true;
    $applicationStatus = $existingApp['application']['status'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Verification - ThriftHub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f7fa;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #0F5E4D;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }
        
        .required {
            color: #d32f2f;
        }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #0F5E4D;
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .file-input-wrapper {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .file-input-wrapper:hover {
            border-color: #0F5E4D;
            background: #f9f9f9;
        }
        
        .file-input {
            display: none;
        }
        
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #0F5E4D;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-submit:hover:not(:disabled) {
            background: #0A4538;
        }
        
        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .error-message, .success-message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        
        .error-message {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef5350;
        }
        
        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #66bb6a;
        }
        
        .alert-box {
            padding: 20px;
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #0F5E4D;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .section-title:first-of-type {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏪 Seller Verification</h1>
        <p class="subtitle">Complete this form to apply as a verified seller on ThriftHub</p>
        
        <?php if ($hasApplication && in_array($applicationStatus, ['pending', 'approved'])): ?>
            <div class="alert-box">
                <h3 style="margin-bottom: 10px;">Application Already Submitted</h3>
                <p>You have already applied to become a seller. Status: <strong><?php echo ucfirst($applicationStatus); ?></strong></p>
                <p style="margin-top: 10px;"><a href="seller_dashboard.php" style="color: #0F5E4D;">Go to Dashboard</a></p>
            </div>
        <?php else: ?>
            <div class="error-message" id="errorContainer"></div>
            <div class="success-message" id="successContainer"></div>
            
            <form id="sellerVerificationForm" method="POST" enctype="multipart/form-data">
                
                <!-- Identity Verification Section -->
                <div class="section-title">📄 Identity Verification Documents</div>
                
                <div class="form-group">
                    <label class="form-label">ID Document <span class="required">*</span></label>
                    <p style="font-size: 13px; color: #666; margin-bottom: 10px;">Upload Ghana Card, Voter ID, or Passport (PDF, PNG, JPG)</p>
                    <div class="file-input-wrapper" onclick="document.getElementById('id_path').click()">
                        <div style="font-size: 40px; margin-bottom: 10px;">🪪</div>
                        <div>Click to upload ID document</div>
                        <div style="font-size: 12px; color: #999; margin-top: 5px;">Max 5MB</div>
                    </div>
                    <input type="file" id="id_path" name="id_path" class="file-input" accept=".pdf,.png,.jpg,.jpeg" required />
                    <div id="id_preview" style="display: none; margin-top: 10px;"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Proof of Address <span class="required">*</span></label>
                    <p style="font-size: 13px; color: #666; margin-bottom: 10px;">Upload utility bill or business permit (PDF, PNG, JPG)</p>
                    <div class="file-input-wrapper" onclick="document.getElementById('address_path').click()">
                        <div style="font-size: 40px; margin-bottom: 10px;">📍</div>
                        <div>Click to upload proof of address</div>
                        <div style="font-size: 12px; color: #999; margin-top: 5px;">Max 5MB</div>
                    </div>
                    <input type="file" id="address_path" name="address_path" class="file-input" accept=".pdf,.png,.jpg,.jpeg" required />
                    <div id="address_preview" style="display: none; margin-top: 10px;"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Selfie with ID <span class="required">*</span></label>
                    <p style="font-size: 13px; color: #666; margin-bottom: 10px;">Take a selfie holding your ID for verification</p>
                    <div class="file-input-wrapper" onclick="document.getElementById('selfie_path').click()">
                        <div style="font-size: 40px; margin-bottom: 10px;">🤳</div>
                        <div>Click to upload selfie with ID</div>
                        <div style="font-size: 12px; color: #999; margin-top: 5px;">Max 5MB</div>
                    </div>
                    <input type="file" id="selfie_path" name="selfie_path" class="file-input" accept="image/*" required />
                    <div id="selfie_preview" style="display: none; margin-top: 10px;"></div>
                </div>
                
                <!-- Payment Information Section -->
                <div class="section-title">💳 Payment Information</div>
                
                <div class="form-group">
                    <label for="momo_number" class="form-label">Mobile Money Number <span class="required">*</span></label>
                    <input type="tel" id="momo_number" name="momo_number" class="form-input" placeholder="0241234567" required />
                </div>
                
                <div class="form-group">
                    <label for="bank_name" class="form-label">Bank Name <span class="required">*</span></label>
                    <input type="text" id="bank_name" name="bank_name" class="form-input" placeholder="e.g., GCB Bank" required />
                </div>
                
                <div class="form-group">
                    <label for="account_number" class="form-label">Account Number <span class="required">*</span></label>
                    <input type="text" id="account_number" name="account_number" class="form-input" placeholder="Enter account number" required />
                </div>
                
                <button type="submit" id="submitBtn" class="btn-submit">Submit Application</button>
            </form>
        <?php endif; ?>
    </div>
    
    <script src="../assets/js/verification.js"></script>
</body>
</html>

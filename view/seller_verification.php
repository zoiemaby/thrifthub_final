<?php
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/seller_controller.php';
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
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Seller Verification — ThriftHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --thrift-green: #0F5E4D;
      --thrift-green-dark: #0A4538;
      --thrift-green-light: #1A7A66;
      --beige: #F6F2EA;
      --white: #FFFFFF;
      --text-dark: #2C2C2C;
      --text-muted: #6B6B6B;
      --text-light: #9A9A9A;
      --gold: #C9A961;
      --gold-light: #E5D4A8;
      --border: #E8E3D8;
      --error: #D32F2F;
      --success: #2E7D32;
      --warning: #F57C00;
      --pending: #FFA726;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html,
    body {
      height: 100%;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    body {
      background: var(--beige);
      color: var(--text-dark);
      padding: 20px;
      min-height: 100vh;
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 280px 1fr;
      gap: 30px;
    }

    @media (max-width: 968px) {
      .container {
        grid-template-columns: 1fr;
      }
    }

    /* Header */
    .header {
      grid-column: 1 / -1;
      background: var(--white);
      padding: 20px 30px;
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(15, 94, 77, 0.12);
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo-section {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .logo {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, var(--thrift-green) 0%, var(--thrift-green-light) 100%);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--white);
      font-family: 'Playfair Display', serif;
      font-weight: 700;
      font-size: 18px;
      box-shadow: 0 2px 8px rgba(15, 94, 77, 0.15);
    }

    .header-title {
      font-family: 'Playfair Display', serif;
      font-size: 24px;
      font-weight: 600;
      color: var(--thrift-green);
      letter-spacing: -0.5px;
    }

    .help-link {
      color: var(--thrift-green);
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .help-link:hover {
      text-decoration: underline;
    }

    /* Sidebar */
    .sidebar {
      background: var(--white);
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 4px 16px rgba(15, 94, 77, 0.12);
      height: fit-content;
      position: sticky;
      top: 20px;
    }

    @media (max-width: 968px) {
      .sidebar {
        position: relative;
        top: 0;
      }
    }

    .sidebar-title {
      font-size: 18px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 20px;
      padding-bottom: 16px;
      border-bottom: 2px solid var(--beige);
    }

    .sidebar-menu {
      list-style: none;
    }

    .sidebar-item {
      margin-bottom: 12px;
    }

    .sidebar-link {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      border-radius: 10px;
      color: var(--text-muted);
      text-decoration: none;
      font-size: 14px;
      transition: all 0.3s ease;
      position: relative;
    }

    .sidebar-link:hover {
      background: var(--beige);
      color: var(--thrift-green);
    }

    .sidebar-link.active {
      background: var(--beige);
      color: var(--thrift-green);
      font-weight: 600;
    }

    .sidebar-link.active::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 4px;
      height: 60%;
      background: var(--thrift-green);
      border-radius: 0 2px 2px 0;
    }

    .sidebar-link.completed::after {
      content: '✓';
      margin-left: auto;
      color: var(--success);
      font-weight: 600;
    }

    /* Main Content */
    .main-content {
      background: var(--white);
      border-radius: 16px;
      padding: 40px;
      box-shadow: 0 4px 16px rgba(15, 94, 77, 0.12);
    }

    @media (max-width: 640px) {
      .main-content {
        padding: 24px;
      }
    }

    .section {
      display: none;
    }

    .section.active {
      display: block;
      animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .section-header {
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 2px solid var(--beige);
    }

    .section-title {
      font-size: 28px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .section-subtitle {
      font-size: 14px;
      color: var(--text-muted);
      line-height: 1.6;
    }

    .step-indicator {
      display: inline-block;
      background: var(--beige);
      color: var(--thrift-green);
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      margin-bottom: 16px;
    }

    /* Form Styles */
    .form-group {
      margin-bottom: 24px;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    @media (max-width: 640px) {
      .form-row {
        grid-template-columns: 1fr;
      }
    }

    .form-label {
      display: block;
      font-size: 14px;
      font-weight: 500;
      color: var(--text-dark);
      margin-bottom: 8px;
    }

    .form-label .required {
      color: var(--error);
      margin-left: 2px;
    }

    .form-label .optional {
      color: var(--text-light);
      font-weight: 400;
      margin-left: 4px;
    }

    .form-input,
    .form-select,
    .form-textarea {
      width: 100%;
      padding: 14px 16px;
      font-size: 15px;
      color: var(--text-dark);
      background: var(--beige);
      border: 2px solid var(--border);
      border-radius: 12px;
      transition: all 0.3s ease;
      font-family: inherit;
    }

    .form-textarea {
      resize: vertical;
      min-height: 100px;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
      outline: none;
      border-color: var(--thrift-green);
      background: var(--white);
      box-shadow: 0 0 0 4px rgba(15, 94, 77, 0.1);
    }

    .form-input:disabled {
      background: #f5f5f5;
      cursor: not-allowed;
      opacity: 0.7;
    }


    /* File Upload */
    .file-upload {
      border: 2px dashed var(--border);
      border-radius: 12px;
      padding: 30px;
      text-align: center;
      background: var(--beige);
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .file-upload:hover {
      border-color: var(--thrift-green);
      background: var(--white);
    }

    .file-upload.dragover {
      border-color: var(--thrift-green);
      background: rgba(15, 94, 77, 0.05);
    }

    .file-upload-icon {
      font-size: 48px;
      margin-bottom: 12px;
    }

    .file-upload-text {
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 8px;
    }

    .file-upload-hint {
      font-size: 12px;
      color: var(--text-light);
    }

    .file-input {
      display: none;
    }

    .file-preview {
      margin-top: 16px;
      padding: 12px;
      background: var(--white);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .file-name {
      font-size: 14px;
      color: var(--text-dark);
      flex: 1;
    }

    .file-remove {
      background: none;
      border: none;
      color: var(--error);
      cursor: pointer;
      font-size: 18px;
      padding: 4px;
    }

    /* Checkbox Group */
    .checkbox-group {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 16px;
      background: var(--beige);
      border-radius: 12px;
      margin-bottom: 16px;
    }

    .checkbox-group input[type="checkbox"] {
      width: 20px;
      height: 20px;
      accent-color: var(--thrift-green);
      cursor: pointer;
      margin-top: 2px;
      flex-shrink: 0;
    }

    .checkbox-group label {
      font-size: 14px;
      color: var(--text-dark);
      cursor: pointer;
      line-height: 1.6;
      user-select: none;
    }

    .checkbox-group a {
      color: var(--thrift-green);
      text-decoration: none;
      font-weight: 500;
    }

    .checkbox-group a:hover {
      text-decoration: underline;
    }

    .security-note {
      background: rgba(15, 94, 77, 0.05);
      border-left: 4px solid var(--thrift-green);
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 13px;
      color: var(--text-muted);
      margin-top: 12px;
    }

    /* Status Section */
    .status-card {
      background: var(--beige);
      border-radius: 16px;
      padding: 40px;
      text-align: center;
    }

    .status-icon {
      font-size: 64px;
      margin-bottom: 20px;
    }

    .status-title {
      font-size: 24px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 12px;
    }

    .status-message {
      font-size: 15px;
      color: var(--text-muted);
      line-height: 1.6;
      margin-bottom: 30px;
    }

    .progress-bar {
      width: 100%;
      height: 8px;
      background: var(--border);
      border-radius: 4px;
      overflow: hidden;
      margin-bottom: 20px;
    }

    .progress-fill {
      height: 100%;
      background: var(--thrift-green);
      transition: width 0.3s ease;
    }

    .status-badge {
      display: inline-block;
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
      margin-bottom: 20px;
    }

    .status-badge.pending {
      background: rgba(255, 167, 38, 0.1);
      color: var(--pending);
    }

    .status-badge.approved {
      background: rgba(46, 125, 50, 0.1);
      color: var(--success);
    }

    .status-badge.rejected {
      background: rgba(211, 47, 47, 0.1);
      color: var(--error);
    }

    .action-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 14px 28px;
      background: var(--thrift-green);
      color: var(--white);
      border: none;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .action-btn:hover {
      background: var(--thrift-green-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(15, 94, 77, 0.3);
    }

    /* Navigation Buttons */
    .nav-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 40px;
      padding-top: 30px;
      border-top: 2px solid var(--beige);
    }

    .btn-secondary {
      padding: 14px 28px;
      background: var(--beige);
      color: var(--thrift-green);
      border: 2px solid var(--border);
      border-radius: 12px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      background: var(--white);
      border-color: var(--thrift-green);
    }

    .btn-primary {
      padding: 14px 28px;
      background: var(--thrift-green);
      color: var(--white);
      border: none;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background: var(--thrift-green-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(15, 94, 77, 0.3);
    }

    .btn-primary:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }

    /* Error Message */
    .error-message {
      display: none;
      padding: 12px 16px;
      background: rgba(211, 47, 47, 0.1);
      border: 1px solid var(--error);
      border-radius: 12px;
      color: var(--error);
      font-size: 14px;
      margin-bottom: 20px;
    }

    .error-message.show {
      display: block;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Header -->
    <div class="header">
      <div class="logo-section">
        <div class="logo">TH</div>
        <div class="header-title">Seller Verification</div>
      </div>
      <a href="../actions/start_support_chat.php" class="help-link" title="Chat with a ThriftHub support admin" style="cursor:pointer;">
        <span>💬</span>
        <span>Need help verifying? Chat with support</span>
      </a>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar">
      <h2 class="sidebar-title">Verification Steps</h2>
      <ul class="sidebar-menu">
        <li class="sidebar-item">
          <a href="#personal" class="sidebar-link active" data-section="personal">
            <span>🧍</span>
            <span>Personal Info</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#store" class="sidebar-link" data-section="store">
            <span>🏪</span>
            <span>Store Details</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#verification" class="sidebar-link" data-section="verification">
            <span>🪪</span>
            <span>Identity Verification</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#payment" class="sidebar-link" data-section="payment">
            <span>💳</span>
            <span>Payment Setup</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#pledge" class="sidebar-link" data-section="pledge">
            <span>🌿</span>
            <span>Eco-Seller Pledge</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#review" class="sidebar-link" data-section="review">
            <span>📋</span>
            <span>Review & Status</span>
          </a>
        </li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <div class="error-message" id="errorContainer" style="display: none;"></div>
      <div style="display: none; padding: 12px 16px; background: rgba(46, 125, 50, 0.1); border: 1px solid var(--success); border-radius: 12px; color: var(--success); font-size: 14px; margin-bottom: 20px;" id="successContainer"></div>
      
      <?php if ($hasApplication && in_array($applicationStatus, ['pending', 'approved'])): ?>
        <div style="padding: 24px; background: rgba(255, 167, 38, 0.1); border: 2px solid var(--pending); border-radius: 12px; text-align: center; margin-bottom: 20px;">
          <h3 style="color: var(--text-dark); margin-bottom: 10px;">Application Already Submitted</h3>
          <p style="color: var(--text-muted);">You have already applied to become a seller. Status: <strong><?php echo ucfirst($applicationStatus); ?></strong></p>
        </div>
      <?php endif; ?>
      
      <form id="sellerVerificationForm" method="POST" enctype="multipart/form-data" <?php echo ($hasApplication && in_array($applicationStatus, ['pending', 'approved'])) ? 'style="display: none;"' : ''; ?>>

      <!-- Section 1: Personal Information -->
      <section id="personal" class="section active">
        <div class="section-header">
          <span class="step-indicator">Step 1 of 6</span>
          <h2 class="section-title">
            <span>🧍</span>
            <span>Personal Information</span>
          </h2>
          <p class="section-subtitle">Your personal details (auto-filled from your account if available)</p>
        </div>

        <div id="personalForm">
          <div class="form-row">
            <div class="form-group">
              <label for="fullname" class="form-label">Full Name <span class="required">*</span></label>
              <input type="text" id="fullname" name="fullname" class="form-input" required />
            </div>
            <div class="form-group">
              <label for="phone" class="form-label">Phone Number <span class="required">*</span></label>
              <input type="tel" id="phone" name="phone" class="form-input" placeholder="+233 54 000 0000" required />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="email" class="form-label">Email Address <span class="required">*</span></label>
              <input type="email" id="email" name="email" class="form-input" required />
            </div>
            <div class="form-group">
              <label for="dob" class="form-label">Date of Birth <span class="required">*</span></label>
              <input type="date" id="dob" name="dob" class="form-input" required />
            </div>
          </div>

          <div class="nav-buttons">
            <div></div>
            <button type="button" class="btn-primary" onclick="nextSection('store')">Continue to Store Details →</button>
          </div>
        </div>
      </section>

      <!-- Section 2: Store / Business Details -->
      <section id="store" class="section">
        <div class="section-header">
          <span class="step-indicator">Step 2 of 6</span>
          <h2 class="section-title">
            <span>🏪</span>
            <span>Store / Business Details</span>
          </h2>
          <p class="section-subtitle">Tell us about your store or business</p>
        </div>

        <div id="storeForm">
          <div class="form-group">
            <label for="storeName" class="form-label">Store Name <span class="required">*</span></label>
            <input type="text" id="storeName" name="storeName" class="form-input" placeholder="e.g., EcoFashion Hub" required />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="type_id" class="form-label">Business Type <span class="required">*</span></label>
              <select id="type_id" name="type_id" class="form-select" required>
                <option value="">Select business type</option>
                <?php if ($businessTypes): ?>
                  <?php foreach ($businessTypes as $type): ?>
                    <option value="<?php echo htmlspecialchars($type['type_id']); ?>">
                      <?php echo htmlspecialchars($type['type_description']); ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="sector_id" class="form-label">Sector <span class="required">*</span></label>
              <select id="sector_id" name="sector_id" class="form-select" required>
                <option value="">Select sector</option>
                <?php if ($sectors): ?>
                  <?php foreach ($sectors as $sector): ?>
                    <option value="<?php echo htmlspecialchars($sector['sector_id']); ?>">
                      <?php echo htmlspecialchars($sector['sector_description']); ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="storeDescription" class="form-label">Short Store Description <span class="required">*</span></label>
            <textarea id="storeDescription" name="storeDescription" class="form-textarea" placeholder="Describe your store in 100-150 characters..." maxlength="150" required></textarea>
            <div class="input-hint" style="text-align: right; margin-top: 6px; font-size: 12px; color: var(--text-light);">
              <span id="charCount">0</span>/150 characters
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="storeLogo" class="form-label">Store Logo <span class="required">*</span></label>
              <div class="file-upload" onclick="document.getElementById('storeLogo').click()">
                <div class="file-upload-icon">📷</div>
                <div class="file-upload-text">Click to upload or drag and drop</div>
                <div class="file-upload-hint">PNG, JPG up to 5MB</div>
                <input type="file" id="storeLogo" name="storeLogo" class="file-input" accept="image/*" required />
              </div>
              <div id="logoPreview" class="file-preview" style="display: none;">
                <span class="file-name"></span>
                <button type="button" class="file-remove" onclick="removeFile('storeLogo')">×</button>
              </div>
            </div>
            <div class="form-group">
              <label for="storeBanner" class="form-label">Store Banner <span class="required">*</span></label>
              <div class="file-upload" onclick="document.getElementById('storeBanner').click()">
                <div class="file-upload-icon">🖼️</div>
                <div class="file-upload-text">Click to upload or drag and drop</div>
                <div class="file-upload-hint">PNG, JPG up to 10MB</div>
                <input type="file" id="storeBanner" name="storeBanner" class="file-input" accept="image/*" required />
              </div>
              <div id="bannerPreview" class="file-preview" style="display: none;">
                <span class="file-name"></span>
                <button type="button" class="file-remove" onclick="removeFile('storeBanner')">×</button>
              </div>
            </div>
          </div>

          <div class="nav-buttons">
            <button type="button" class="btn-secondary" onclick="prevSection('personal')">← Back</button>
            <button type="button" class="btn-primary" onclick="nextSection('verification')">Continue to Verification →</button>
          </div>
        </form>
      </section>

      <!-- Section 3: Identity Verification -->
      <section id="verification" class="section">
        <div class="section-header">
          <span class="step-indicator">Step 3 of 6</span>
          <h2 class="section-title">
            <span>🪪</span>
            <span>Identity Verification</span>
          </h2>
          <p class="section-subtitle">Upload documents to verify your identity and build trust</p>
        </div>

        <div id="verificationForm">
          <div class="form-group">
            <label class="form-label">Upload ID Document <span class="required">*</span></label>
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">
              Upload any ONE of the following: Ghana Card / Voter ID / Passport
            </p>
            <div class="file-upload" onclick="document.getElementById('id_path').click()">
              <div class="file-upload-icon">🪪</div>
              <div class="file-upload-text">Click to upload ID document</div>
              <div class="file-upload-hint">PDF, PNG, JPG up to 5MB</div>
              <input type="file" id="id_path" name="id_path" class="file-input" accept=".pdf,.png,.jpg,.jpeg" required />
            </div>
            <div id="id_preview" class="file-preview" style="display: none;">
              <span class="file-name"></span>
              <button type="button" class="file-remove" onclick="removeFile('id_path')">×</button>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Proof of Address <span class="required">*</span></label>
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">
              Upload Utility Bill or Business Permit
            </p>
            <div class="file-upload" onclick="document.getElementById('address_path').click()">
              <div class="file-upload-icon">📄</div>
              <div class="file-upload-text">Click to upload proof of address</div>
              <div class="file-upload-hint">PDF, PNG, JPG up to 5MB</div>
              <input type="file" id="address_path" name="address_path" class="file-input" accept=".pdf,.png,.jpg,.jpeg" required />
            </div>
            <div id="address_preview" class="file-preview" style="display: none;">
              <span class="file-name"></span>
              <button type="button" class="file-remove" onclick="removeFile('address_path')">×</button>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Selfie with ID <span class="required">*</span></label>
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">
              For KYC verification - Take a selfie holding your ID
            </p>
            <div class="file-upload" onclick="document.getElementById('selfie_path').click()">
              <div class="file-upload-icon">📸</div>
              <div class="file-upload-text">Click to upload selfie with ID</div>
              <div class="file-upload-hint">PNG, JPG up to 5MB</div>
              <input type="file" id="selfie_path" name="selfie_path" class="file-input" accept="image/*" required />
            </div>
            <div id="selfie_preview" class="file-preview" style="display: none;">
              <span class="file-name"></span>
              <button type="button" class="file-remove" onclick="removeFile('selfie_path')">×</button>
            </div>
          </div>

          <div class="security-note">
            🔒 Your data is securely stored and never shared with third parties.
          </div>

          <div class="nav-buttons">
            <button type="button" class="btn-secondary" onclick="prevSection('store')">← Back</button>
            <button type="button" class="btn-primary" onclick="nextSection('payment')">Continue to Payment →</button>
          </div>
        </div>
      </section>

      <!-- Section 4: Payment & Bank Setup -->
      <section id="payment" class="section">
        <div class="section-header">
          <span class="step-indicator">Step 4 of 6</span>
          <h2 class="section-title">
            <span>💳</span>
            <span>Payment & Bank Setup</span>
          </h2>
          <p class="section-subtitle">Set up your payment method to receive payouts</p>
        </div>

        <div id="paymentForm">
          <div class="form-group">
            <label for="momo_number" class="form-label">Mobile Money Number <span class="required">*</span></label>
            <input type="tel" id="momo_number" name="momo_number" class="form-input" placeholder="0241234567" required />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="bank_name" class="form-label">Bank Name <span class="required">*</span></label>
              <input type="text" id="bank_name" name="bank_name" class="form-input" placeholder="e.g., GCB Bank" required />
            </div>
            <div class="form-group">
              <label for="account_number" class="form-label">Account Number / MoMo ID <span class="required">*</span></label>
              <input type="text" id="account_number" name="account_number" class="form-input" placeholder="Account number or MoMo ID" required />
            </div>
          </div>

          <div class="checkbox-group">
            <input type="checkbox" id="accountOwnership" name="accountOwnership" required />
            <label for="accountOwnership">This account belongs to me <span class="required">*</span></label>
          </div>

          <div class="nav-buttons">
            <button type="button" class="btn-secondary" onclick="prevSection('verification')">← Back</button>
            <button type="button" class="btn-primary" onclick="nextSection('pledge')">Continue to Pledge →</button>
          </div>
        </div>
      </section>

      <!-- Section 5: Eco-Seller Pledge -->
      <section id="pledge" class="section">
        <div class="section-header">
          <span class="step-indicator">Step 5 of 6</span>
          <h2 class="section-title">
            <span>🌿</span>
            <span>Eco-Seller Pledge</span>
          </h2>
          <p class="section-subtitle">Join ThriftHub's commitment to sustainable and ethical selling</p>
        </div>

        <div id="pledgeForm">
          <div style="background: rgba(15, 94, 77, 0.05); padding: 24px; border-radius: 12px; margin-bottom: 24px;">
            <p style="font-size: 15px; color: var(--text-dark); line-height: 1.8; margin-bottom: 20px;">
              At ThriftHub, we support sustainable, ethical, and community-based selling. Please confirm you agree to:
            </p>
            <ul style="list-style: none; padding: 0; margin: 0;">
              <li style="padding: 12px 0; border-bottom: 1px solid var(--border); display: flex; align-items: start; gap: 12px;">
                <span style="color: var(--thrift-green); font-size: 20px;">✓</span>
                <span style="color: var(--text-dark);">Selling genuine secondhand or locally made items</span>
              </li>
              <li style="padding: 12px 0; border-bottom: 1px solid var(--border); display: flex; align-items: start; gap: 12px;">
                <span style="color: var(--thrift-green); font-size: 20px;">✓</span>
                <span style="color: var(--text-dark);">Avoiding counterfeit or harmful products</span>
              </li>
              <li style="padding: 12px 0; display: flex; align-items: start; gap: 12px;">
                <span style="color: var(--thrift-green); font-size: 20px;">✓</span>
                <span style="color: var(--text-dark);">Following ThriftHub's community and environmental guidelines</span>
              </li>
            </ul>
          </div>

          <div class="checkbox-group">
            <input type="checkbox" id="ecoPledge" name="ecoPledge" required />
            <label for="ecoPledge">I agree to the ThriftHub Seller and Eco-Trade Policy <span class="required">*</span></label>
          </div>

          <div class="nav-buttons">
            <button type="button" class="btn-secondary" onclick="prevSection('payment')">← Back</button>
            <button type="button" class="btn-primary" onclick="submitVerification()">Submit for Review →</button>
          </div>
        </div>
      </section>

      <!-- Section 6: Status / Review -->
      <section id="review" class="section">
        <div class="section-header">
          <span class="step-indicator">Step 6 of 6</span>
          <h2 class="section-title">
            <span>📋</span>
            <span>Review & Status</span>
          </h2>
          <p class="section-subtitle">Your verification status</p>
        </div>

        <div class="status-card" id="statusCard">
          <?php if ($hasApplication): ?>
            <?php if ($applicationStatus === 'pending'): ?>
              <div class="status-icon">⏳</div>
              <div class="status-badge pending">Pending Approval</div>
              <h3 class="status-title">Your verification is under review</h3>
              <p class="status-message">
                We're reviewing your documents and information. You'll be notified within 24–48 hours via email once the review is complete.
              </p>
              <div class="progress-bar">
                <div class="progress-fill" style="width: 60%;"></div>
              </div>
              <div style="margin-top: 24px;">
                <strong>Submitted:</strong> <?php echo date('F j, Y g:i A', strtotime($existingApp['application']['submitted_at'])); ?>
              </div>
            <?php elseif ($applicationStatus === 'approved'): ?>
              <div class="status-icon">✅</div>
              <div class="status-badge approved">Approved</div>
              <h3 class="status-title">Congratulations! You're verified</h3>
              <p class="status-message">
                Your seller application has been approved. You can now start selling on ThriftHub!
              </p>
              <div class="progress-bar">
                <div class="progress-fill" style="width: 100%;"></div>
              </div>
              <div style="margin-top: 24px;">
                <a href="../seller/seller_dashboard.php" class="action-btn">
                  <span>🏪</span>
                  <span>Go to Seller Dashboard</span>
                </a>
              </div>
            <?php elseif ($applicationStatus === 'rejected'): ?>
              <div class="status-icon">❌</div>
              <div class="status-badge rejected">Rejected</div>
              <h3 class="status-title">Application Not Approved</h3>
              <p class="status-message">
                Unfortunately, your seller application was not approved. Please review our seller guidelines and reapply with correct information.
              </p>
              <div style="margin-top: 24px;">
                <strong>Reviewed:</strong> <?php echo $existingApp['application']['reviewed_at'] ? date('F j, Y g:i A', strtotime($existingApp['application']['reviewed_at'])) : 'N/A'; ?>
              </div>
            <?php endif; ?>
          <?php else: ?>
            <div class="status-icon">📝</div>
            <h3 class="status-title">Ready to Submit</h3>
            <p class="status-message">
              Complete all the steps above and submit your application for review.
            </p>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 0%;"></div>
            </div>
          <?php endif; ?>
        </div>
      </section>
      </form>
    </main>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    // jQuery-based navigation, validation and submission
    const sections = ['personal', 'store', 'verification', 'payment', 'pledge', 'review'];
    let currentSectionIndex = 0;

    const allowedImageTypes = ['image/jpeg','image/jpg','image/png','image/gif','image/webp'];
    const allowedDocTypes = ['application/pdf', ...allowedImageTypes];
    const MAX_IMG_SIZE = 10 * 1024 * 1024; // 10MB banner upper bound
    const MAX_DOC_SIZE = 5 * 1024 * 1024; // 5MB docs

    function showError(message) {
      const $err = $('#errorContainer');
      $err.text(message).show();
      $('html,body').animate({scrollTop: $err.offset().top - 20}, 300);
    }

    function showErrors(errors) {
      const $err = $('#errorContainer');
      $err.html('<ul style="margin-left:18px">' + errors.map(e => `<li>${e}</li>`).join('') + '</ul>').show();
      $('html,body').animate({scrollTop: $err.offset().top - 20}, 300);
    }

    function clearMessages() {
      $('#errorContainer').hide().empty();
      $('#successContainer').hide().empty();
    }

    function fileOk(file, types, maxSize) {
      if (!file) return false;
      if (types.indexOf(file.type) === -1) return false;
      if (file.size > maxSize) return false;
      return true;
    }

    // Make functions global for onclick handlers
    function showSection(sectionId) {
      $('.section').removeClass('active');
      $('#' + sectionId).addClass('active');
      $('.sidebar-link').removeClass('active').filter(`[data-section="${sectionId}"]`).addClass('active');
      currentSectionIndex = sections.indexOf(sectionId);
    }

    function nextSection(sectionId) {
      clearMessages();
      const $current = $('.section.active');
      let errors = [];

      // Validate required fields inside the current section
      $current.find('input[required], select[required], textarea[required]').each(function(){
        const el = this;
        if (el.type === 'checkbox') {
          if (!el.checked) errors.push($(el).closest('.form-group,.checkbox-group').find('label').first().text().trim() + ' is required');
          return;
        }
        if (el.type === 'file') {
          if (!el.files || el.files.length === 0) errors.push($(el).closest('.form-group').find('.form-label').text().trim() + ' is required');
          return;
        }
        if (!el.value || el.value.trim() === '') errors.push($(el).closest('.form-group').find('.form-label').text().trim() + ' is required');
      });

      // Extra validation per section
      const id = $current.attr('id');
      if (id === 'store') {
        const logo = $('#storeLogo')[0].files[0];
        const banner = $('#storeBanner')[0].files[0];
        if (logo && (!fileOk(logo, allowedImageTypes, MAX_DOC_SIZE))) errors.push('Store logo must be an image (<=5MB)');
        if (banner && (!fileOk(banner, allowedImageTypes, MAX_IMG_SIZE))) errors.push('Store banner must be an image (<=10MB)');
      }
      if (id === 'verification') {
        const idf = $('#id_path')[0].files[0];
        const addr = $('#address_path')[0].files[0];
        const selfie = $('#selfie_path')[0].files[0];
        if (!fileOk(idf, allowedDocTypes, MAX_DOC_SIZE)) errors.push('ID document must be PDF or image (<=5MB)');
        if (!fileOk(addr, allowedDocTypes, MAX_DOC_SIZE)) errors.push('Address proof must be PDF or image (<=5MB)');
        if (!fileOk(selfie, allowedImageTypes, MAX_DOC_SIZE)) errors.push('Selfie must be an image (<=5MB)');
      }
      if (id === 'payment') {
        const momo = $('#momo_number').val().trim();
        const acct = $('#account_number').val().trim();
        if (!/^0\d{9}$/.test(momo)) errors.push('Enter a valid 10-digit mobile money number (e.g., 0241234567)');
        if (!/^\d{10,16}$/.test(acct)) errors.push('Account number must be 10-16 digits');
      }

      if (errors.length) { showErrors(errors); return; }

      showSection(sectionId);
      window.scrollTo({top:0, behavior:'smooth'});
    }

    function prevSection(sectionId) {
      clearMessages();
      showSection(sectionId);
      window.scrollTo({top:0, behavior:'smooth'});
    }

    // File upload preview
    function setupFileUpload(inputId, previewId) {
      const $input = $('#' + inputId);
      const $preview = $('#' + previewId);
      const $name = $preview.find('.file-name');
      $input.on('change', function(){
        if (this.files && this.files.length) {
          $name.text(this.files[0].name);
          $preview.css('display','flex');
        }
      });
    }

    function removeFile(inputId) {
      const $input = $('#' + inputId);
      const previewId = inputId === 'storeLogo' ? 'logoPreview' : inputId === 'storeBanner' ? 'bannerPreview' : inputId === 'id_path' ? 'id_preview' : inputId === 'address_path' ? 'address_preview' : 'selfie_preview';
      const $preview = $('#' + previewId);
      $input.val('');
      $preview.hide();
    }

    // Character counter
    const $storeDescription = $('#storeDescription');
    const $charCount = $('#charCount');
    if ($storeDescription.length) {
      $storeDescription.on('input', function(){ $charCount.text(this.value.length); });
    }

    // Submit via jQuery AJAX - only docs + payment (relevant to endpoint)
    function submitVerification() {
      clearMessages();
      // Validate final required items (reuse nextSection validation for current pledge section dependencies)
      // Ensure previous sections valid quickly
      const errors = [];
      // Basic check of critical fields
      // Store details
      if (!$('#storeName').val().trim()) errors.push('Store name is required');
      if (!$('#type_id').val()) errors.push('Business type is required');
      if (!$('#sector_id').val()) errors.push('Sector is required');
      if (!$('#storeDescription').val().trim()) errors.push('Store description is required');
      if (!$('#storeLogo')[0].files.length) errors.push('Store logo is required');
      if (!$('#storeBanner')[0].files.length) errors.push('Store banner is required');
      if (!$('#id_path')[0].files.length) errors.push('ID document is required');
      if (!$('#address_path')[0].files.length) errors.push('Proof of address is required');
      if (!$('#selfie_path')[0].files.length) errors.push('Selfie with ID is required');
      if (!/^0\d{9}$/.test($('#momo_number').val().trim())) errors.push('Enter a valid 10-digit mobile money number');
      if (!/^\d{10,16}$/.test($('#account_number').val().trim())) errors.push('Account number must be 10-16 digits');
      if (!$('#ecoPledge').is(':checked')) errors.push('You must agree to the Eco-Seller Policy');
      if (errors.length) { showErrors(errors); return; }

      // Disable button
      const $btn = $('section#pledge .btn-primary').last();
      $btn.prop('disabled', true).text('Submitting...');

      // First, upload store details and branding to seller profile
      const fdProfile = new FormData();
      fdProfile.append('shop_name', $('#storeName').val().trim());
      fdProfile.append('type_id', $('#type_id').val());
      fdProfile.append('sector_id', $('#sector_id').val());
      fdProfile.append('description', $('#storeDescription').val().trim());
      fdProfile.append('store_logo', $('#storeLogo')[0].files[0]);
      fdProfile.append('store_banner', $('#storeBanner')[0].files[0]);

      $.ajax({
        url: '../actions/update_seller_profile_action.php',
        method: 'POST',
        data: fdProfile,
        processData: false,
        contentType: false,
        dataType: 'json'
      }).done(function(profileResp){
        if (!(profileResp && profileResp.success)) {
          const msg = (profileResp && profileResp.message) ? profileResp.message : 'Failed to save store profile. Please try again.';
          showError(msg);
          $btn.prop('disabled', false).text('Submit for Review →');
          return;
        }
        // Then, build FormData for application endpoint
        const fd = new FormData();
        fd.append('id_path', $('#id_path')[0].files[0]);
        fd.append('address_path', $('#address_path')[0].files[0]);
        fd.append('selfie_path', $('#selfie_path')[0].files[0]);
        fd.append('momo_number', $('#momo_number').val().trim());
        fd.append('bank_name', $('#bank_name').val().trim());
        fd.append('account_number', $('#account_number').val().trim());

        $.ajax({
          url: '../actions/apply_seller_action.php',
          method: 'POST',
          data: fd,
          processData: false,
          contentType: false,
          dataType: 'json'
        }).done(function(resp){
          if (resp && resp.success) {
            $('#successContainer').text(resp.message || 'Application submitted successfully!').show();
            showSection('review');
            // Do not redirect immediately; wait for admin approval
          } else {
            const msg = (resp && resp.message) ? resp.message : 'Failed to submit application. Please try again.';
            showError(msg);
            $btn.prop('disabled', false).text('Submit for Review →');
          }
        }).fail(function(jqXHR){
          let msg = 'Network/Server error while submitting application.';
          try {
            const j = JSON.parse(jqXHR.responseText);
            if (j && j.message) msg = j.message;
          } catch(e){}
          showError(msg);
          $btn.prop('disabled', false).text('Submit for Review →');
        });
      }).fail(function(jqXHR){
        let msg = 'Network/Server error while saving store profile.';
        try {
          const j = JSON.parse(jqXHR.responseText);
          if (j && j.message) msg = j.message;
        } catch(e){}
        showError(msg);
        $btn.prop('disabled', false).text('Submit for Review →');
      });
    }

    // Expose required functions globally for onclick attributes
    window.showSection = showSection;
    window.nextSection = nextSection;
    window.prevSection = prevSection;
    window.submitVerification = submitVerification;

    // Init handlers and previews
    $(function(){
      clearMessages();
      setupFileUpload('storeLogo','logoPreview');
      setupFileUpload('storeBanner','bannerPreview');
      setupFileUpload('id_path','id_preview');
      setupFileUpload('address_path','address_preview');
      setupFileUpload('selfie_path','selfie_preview');

      // Sidebar links
      $('.sidebar-link').on('click', function(e){
        e.preventDefault();
        showSection($(this).data('section'));
        window.scrollTo({top:0, behavior:'smooth'});
      });
    });
  </script>
</body>

</html>
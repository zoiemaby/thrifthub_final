<?php
/**
 * Product Management Page
 * ThriftHub - Seller Product Management
 * 
 * Requirements:
 * - Check if user is logged in
 * - Redirect to login if not authorized
 * - Display products organized by category and brand
 * - Allow CREATE/UPDATE operations using the same form
 */

require_once __DIR__ . '/../settings/core.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    header('Location: ../view/login.php');
    exit;
}

// Check if user is a seller (role 3)
$roleNo = isset($_SESSION['user_role_no']) ? (int)$_SESSION['user_role_no'] : 0;
if ($roleNo !== ROLE_SELLER) {
    header('Location: ../view/login.php');
    exit;
}

// Check if seller is verified
$isVerified = isset($_SESSION['is_seller_verified']) ? (bool)$_SESSION['is_seller_verified'] : false;
if (!$isVerified) {
    header('Location: ../view/seller_verification.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Manage Products — ThriftHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
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

    html, body {
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

    .header-actions {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .btn-logout {
      padding: 10px 20px;
      background: var(--beige);
      color: var(--thrift-green);
      border: 2px solid var(--border);
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .btn-logout:hover {
      background: var(--white);
      border-color: var(--thrift-green);
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

    .btn-primary {
      padding: 14px 28px;
      background: var(--thrift-green);
      color: var(--white);
      border: none;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background: var(--thrift-green-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(15, 94, 77, 0.3);
    }

    .btn-secondary {
      padding: 10px 20px;
      background: var(--beige);
      color: var(--thrift-green);
      border: 2px solid var(--border);
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      background: var(--white);
      border-color: var(--thrift-green);
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

    .file-upload-icon {
      font-size: 48px;
      margin-bottom: 12px;
    }

    .file-upload-text {
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 8px;
    }

    .file-input {
      display: none;
    }

    /* Products Display Styles */
    .products-organized {
      margin-top: 30px;
    }

    .category-section {
      margin-bottom: 40px;
      padding: 24px;
      background: var(--beige);
      border-radius: 12px;
    }

    .category-header {
      font-size: 20px;
      font-weight: 600;
      color: var(--thrift-green);
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 2px solid var(--border);
    }

    .brand-section {
      margin-bottom: 24px;
      padding: 20px;
      background: var(--white);
      border-radius: 10px;
      border: 1px solid var(--border);
    }

    .brand-header {
      font-size: 16px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 16px;
    }

    .products-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }

    @media (max-width: 768px) {
      .products-grid {
        grid-template-columns: 1fr;
      }
    }

    .product-card {
      background: var(--white);
      border: 2px solid var(--border);
      border-radius: 12px;
      padding: 16px;
      transition: all 0.3s ease;
    }

    .product-card:hover {
      border-color: var(--thrift-green);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(15, 94, 77, 0.15);
    }

    .product-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 12px;
      background: var(--beige);
    }

    .product-title {
      font-size: 16px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 8px;
    }

    .product-price {
      font-size: 18px;
      font-weight: 700;
      color: var(--thrift-green);
      margin-bottom: 8px;
    }

    .product-actions {
      display: flex;
      gap: 8px;
      margin-top: 12px;
    }

    .btn-edit, .btn-delete {
      flex: 1;
      padding: 8px 16px;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-edit {
      background: var(--thrift-green);
      color: var(--white);
    }

    .btn-edit:hover {
      background: var(--thrift-green-dark);
    }

    .btn-delete {
      background: var(--error);
      color: var(--white);
    }

    .btn-delete:hover {
      background: #B71C1C;
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: var(--text-muted);
    }

    .empty-state-icon {
      font-size: 48px;
      margin-bottom: 16px;
      opacity: 0.5;
    }

    .empty-state-text {
      font-size: 16px;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Header -->
    <div class="header">
      <div class="logo-section">
        <div class="logo">TH</div>
        <div class="header-title">Seller Dashboard</div>
      </div>
      <div class="header-actions">
        <a href="../index.php" class="btn-logout">← Back to Site</a>
        <a href="../actions/logout.php" class="btn-logout">Logout</a>
      </div>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar">
      <h2 class="sidebar-title">Navigation</h2>
      <ul class="sidebar-menu">
        <li class="sidebar-item">
          <a href="seller_dashboard.php" class="sidebar-link">
            <span>📊</span>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="seller_dashboard.php#products" class="sidebar-link">
            <span>📦</span>
            <span>My Products</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="product.php" class="sidebar-link active">
            <span>➕</span>
            <span>Add Product</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="brand.php" class="sidebar-link">
            <span>🏷️</span>
            <span>Manage Brands</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="category.php" class="sidebar-link">
            <span>📁</span>
            <span>Manage Categories</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="seller_dashboard.php#orders" class="sidebar-link">
            <span>🛍️</span>
            <span>Orders</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="seller_dashboard.php#sales" class="sidebar-link">
            <span>📈</span>
            <span>Sales History</span>
          </a>
        </li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <div class="section-header">
        <div>
          <h2 class="section-title">
            <span>➕</span>
            <span>Add New Product</span>
          </h2>
          <p class="section-subtitle">Create a new product listing</p>
        </div>
      </div>

      <!-- Product Form (for Add/Edit) -->
      <form id="productForm">
        <input type="hidden" id="productId" name="product_id" value="" />
        
        <div class="form-row">
          <div class="form-group">
            <label for="product_title" class="form-label">Product Title <span class="required">*</span></label>
            <input type="text" id="product_title" name="product_title" class="form-input" placeholder="e.g., Vintage Denim Jacket" required />
          </div>
          <div class="form-group">
            <label for="product_price" class="form-label">Price (₵) <span class="required">*</span></label>
            <input type="number" id="product_price" name="product_price" class="form-input" placeholder="0.00" step="0.01" min="0" required />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="product_cat" class="form-label">Category <span class="required">*</span></label>
            <select id="product_cat" name="product_cat" class="form-select" required>
              <option value="">Select category</option>
              <!-- Categories will be loaded dynamically -->
            </select>
          </div>
          <div class="form-group">
            <label for="product_brand" class="form-label">Brand</label>
            <select id="product_brand" name="product_brand" class="form-select">
              <option value="">Select brand (optional)</option>
              <!-- Brands will be loaded dynamically -->
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="product_condition" class="form-label">Condition <span class="required">*</span></label>
            <select id="product_condition" name="product_condition" class="form-select" required>
              <option value="">Select condition</option>
              <option value="new">New</option>
              <option value="like-new">Like New</option>
              <option value="good">Good</option>
              <option value="fair">Fair</option>
            </select>
          </div>
          <div class="form-group">
            <label for="product_keywords" class="form-label">Keywords</label>
            <input type="text" id="product_keywords" name="product_keywords" class="form-input" placeholder="e.g., vintage, denim, jacket" />
          </div>
        </div>

        <div class="form-group">
          <label for="product_desc" class="form-label">Description <span class="required">*</span></label>
          <textarea id="product_desc" name="product_desc" class="form-textarea" placeholder="Describe your product in detail..." required></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Product Image <span class="required">*</span></label>
          <div class="file-upload" onclick="document.getElementById('product_image').click()">
            <div class="file-upload-icon">📷</div>
            <div class="file-upload-text">Click to upload or drag and drop</div>
            <div style="font-size: 12px; color: var(--text-light);">PNG, JPG up to 5MB</div>
            <input type="file" id="product_image" name="product_image" class="file-input" accept="image/*" />
          </div>
          <div id="imagePreview" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 12px; margin-top: 16px;"></div>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 30px;">
          <button type="submit" class="btn-primary" id="submitBtn">Add Product</button>
          <button type="button" class="btn-secondary" id="cancelBtn" style="display: none;">Cancel Edit</button>
          <a href="seller_dashboard.php#products" class="btn-secondary">Back to Dashboard</a>
        </div>
      </form>

      <!-- Products Display Section -->
      <div class="section-header" style="margin-top: 60px;">
        <div>
          <h2 class="section-title">
            <span>📦</span>
            <span>My Products</span>
          </h2>
          <p class="section-subtitle">Products organized by category and brand</p>
        </div>
      </div>

      <div id="productsDisplay">
        <div class="empty-state">
          <div class="empty-state-icon">📦</div>
          <div class="empty-state-text">Loading products...</div>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assets/js/product.js"></script>
  <script>
    (function(){
      function showError(msg, url, line) {
        var main = document.querySelector('.main-content') || document.body;
        var box = document.createElement('div');
        box.style = 'position:fixed;right:20px;bottom:20px;z-index:99999;background:#111;color:#fff;padding:12px 16px;border-radius:8px;max-width:360px;font-family: monospace;box-shadow:0 6px 20px rgba(0,0,0,0.4)';
        box.textContent = 'JS Error: ' + msg + (url ? (' @ ' + url + ':' + line) : '');
        document.body.appendChild(box);
        console.error('JS Error:', msg, url, line);
      }
      window.onerror = function(msg, url, line, col, err) { showError(msg, url, line); };
      window.addEventListener('unhandledrejection', function(e){
        var reason = (e && e.reason && e.reason.message) ? e.reason.message : (e && e.reason) || 'Unknown rejection';
        showError('Unhandled promise rejection: ' + reason);
      });
    })();
  </script>
</body>
</html>


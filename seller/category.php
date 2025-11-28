<?php
/**
 * Category Management Page
 * ThriftHub - Admin Category Management
 * 
 * Requirements:
 * - Check if user is logged in
 * - Check if user is admin
 * - Redirect to login if not admin
 * - Display categories created by logged-in user
 * - Allow CREATE, UPDATE, DELETE operations
 */

require_once __DIR__ . '/../settings/core.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    header('Location: ../view/login.php');
    exit;
}

// Check if user is admin (role 1) - only admins can manage categories
$roleNo = isset($_SESSION['user_role_no']) ? (int)$_SESSION['user_role_no'] : 0;
if ($roleNo !== ROLE_SELLER) {
    header('Location: ../view/login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Manage Categories — ThriftHub</title>
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

    /* Brand and Category Management Styles */
    .add-form-section {
      background: var(--white);
      border: 2px solid var(--border);
      border-radius: 16px;
      padding: 24px;
      margin-bottom: 40px;
    }

    .add-form-title {
      font-size: 18px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 20px;
    }

    .add-form-wrapper {
      display: flex;
      gap: 12px;
      align-items: flex-start;
    }

    .add-form-input {
      flex: 1;
      padding: 14px 16px;
      border: 2px solid var(--border);
      border-radius: 12px;
      font-size: 14px;
      background: var(--beige);
      color: var(--text-dark);
      transition: all 0.3s ease;
    }

    .add-form-input:focus {
      outline: none;
      border-color: var(--thrift-green);
      background: var(--white);
    }

    .add-form-btn {
      padding: 14px 28px;
      background: var(--thrift-green);
      color: var(--white);
      border: none;
      border-radius: 12px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
      white-space: nowrap;
    }

    .add-form-btn:hover {
      background: var(--thrift-green-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(15, 94, 77, 0.3);
    }

    .add-form-btn:active {
      transform: translateY(0);
    }

    .list-section {
      margin-top: 30px;
    }

    .list-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
    }

    .list-title {
      font-size: 20px;
      font-weight: 600;
      color: var(--thrift-green);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .list-count {
      font-size: 14px;
      color: var(--text-muted);
      font-weight: 500;
    }

    .items-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 16px;
    }

    .item-card {
      background: var(--white);
      border: 2px solid var(--border);
      border-radius: 12px;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: all 0.3s ease;
    }

    .item-card:hover {
      border-color: var(--thrift-green);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(15, 94, 77, 0.15);
    }

    .item-name {
      font-size: 16px;
      font-weight: 500;
      color: var(--text-dark);
      flex: 1;
    }

    .item-actions {
      display: flex;
      gap: 8px;
    }

    .item-action-btn {
      width: 36px;
      height: 36px;
      border: 2px solid var(--border);
      border-radius: 8px;
      background: var(--white);
      color: var(--thrift-green);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      font-size: 14px;
    }

    .item-action-btn:hover {
      background: var(--thrift-green);
      color: var(--white);
      border-color: var(--thrift-green);
    }

    .item-action-btn.edit:hover {
      background: var(--thrift-green);
    }

    .item-action-btn.delete:hover {
      background: var(--error);
      border-color: var(--error);
      color: var(--white);
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

    @media (max-width: 640px) {
      .add-form-wrapper {
        flex-direction: column;
      }

      .add-form-btn {
        width: 100%;
        justify-content: center;
      }

      .items-grid {
        grid-template-columns: 1fr;
      }
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
          <a href="product.php" class="sidebar-link">
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
          <a href="category.php" class="sidebar-link active">
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
            <span>📁</span>
            <span>Manage Categories</span>
          </h2>
          <p class="section-subtitle">Add, edit, and organize product categories</p>
        </div>
      </div>

      <!-- Add New Category Form -->
      <div class="add-form-section">
        <h3 class="add-form-title">Add New Category</h3>
        <form id="addCategoryForm" class="add-form-wrapper">
          <input 
            type="text" 
            id="categoryNameInput" 
            name="cat_name" 
            class="add-form-input" 
            placeholder="Category Name" 
            required
            minlength="2"
            maxlength="100"
          />
          <button type="submit" class="add-form-btn" id="addCategoryBtn">
            <i class="fas fa-plus"></i>
            <span>Add Category</span>
          </button>
        </form>
        <div id="categoryMessage" style="margin-top: 12px; font-size: 14px;"></div>
      </div>

      <!-- All Categories List -->
      <div class="list-section">
        <div class="list-header">
          <h3 class="list-title">
            <span>All Categories</span>
            <span class="list-count" id="categoryCount">0 total</span>
          </h3>
        </div>
        <div class="items-grid" id="categoriesGrid">
          <div class="empty-state">
            <div class="empty-state-icon">📁</div>
            <div class="empty-state-text">No categories yet. Add your first category above.</div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script>
    let categories = [];

    // Load categories from server
    async function loadCategories() {
      try {
        const response = await fetch('../actions/fetch_categories_action.php');
        const result = await response.json();
        
        if (result.success) {
          categories = result.categories || [];
          renderCategories();
        }
      } catch (error) {
        console.error('Error loading categories:', error);
      }
    }

    // Render categories
    function renderCategories() {
      const grid = document.getElementById('categoriesGrid');
      const countEl = document.getElementById('categoryCount');
      
      if (!grid) return;
      
      countEl.textContent = `${categories.length} total`;
      
      if (categories.length === 0) {
        grid.innerHTML = `
          <div class="empty-state" style="grid-column: 1 / -1;">
            <div class="empty-state-icon">📁</div>
            <div class="empty-state-text">No categories yet. Add your first category above.</div>
          </div>
        `;
        return;
      }
      
      grid.innerHTML = categories.map(category => `
        <div class="item-card">
          <div class="item-name">${category.cat_name}</div>
          <div class="item-actions">
            <button class="item-action-btn edit" onclick="editCategory(${category.cat_id}, '${category.cat_name.replace(/'/g, "\\'")}')" title="Edit">
              <i class="fas fa-pencil-alt"></i>
            </button>
            <button class="item-action-btn delete" onclick="deleteCategory(${category.cat_id})" title="Delete">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
      `).join('');
    }

    // Note: Form submission is handled by category.js
    // This inline handler has been removed to avoid conflicts

    // Edit category
    function editCategory(categoryId, currentName) {
      const newName = prompt('Edit category name:', currentName);
      if (newName && newName.trim() && newName.trim() !== currentName) {
        // TODO: Implement edit functionality
        alert('Edit functionality will be implemented with update action.');
      }
    }

    // Delete category
    async function deleteCategory(categoryId) {
      if (!confirm('Are you sure you want to delete this category?')) {
        return;
      }
      
      try {
        const formData = new FormData();
        formData.append('cat_id', categoryId);
        
        const response = await fetch('../actions/delete_category_action.php', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          await loadCategories(); // Reload categories
        } else {
          alert(result.message || 'Failed to delete category.');
        }
      } catch (error) {
        alert('An error occurred. Please try again.');
        console.error('Error:', error);
      }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
      loadCategories();
    });
  </script>
  <script src="../assets/js/category.js"></script>
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


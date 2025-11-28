<?php
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
  <title>Seller Dashboard — ThriftHub</title>
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
      cursor: pointer;
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
      display: flex;
      justify-content: space-between;
      align-items: center;
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

    /* Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 40px;
    }

    .stat-card {
      background: var(--beige);
      border-radius: 16px;
      padding: 24px;
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 12px rgba(15, 94, 77, 0.15);
    }

    .stat-icon {
      font-size: 32px;
      margin-bottom: 12px;
    }

    .stat-value {
      font-size: 32px;
      font-weight: 700;
      color: var(--thrift-green);
      margin-bottom: 4px;
    }

    .stat-label {
      font-size: 14px;
      color: var(--text-muted);
      font-weight: 500;
    }

    /* Buttons */
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

    .btn-edit {
      padding: 8px 16px;
      background: var(--thrift-green);
      color: var(--white);
      border: none;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-edit:hover {
      background: var(--thrift-green-dark);
      transform: translateY(-2px);
    }

    .btn-delete {
      padding: 8px 16px;
      background: var(--error);
      color: var(--white);
      border: none;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-delete:hover {
      background: #B71C1C;
      transform: translateY(-2px);
    }

    .btn-ship {
      padding: 8px 16px;
      background: var(--success);
      color: var(--white);
      border: none;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-ship:hover {
      background: #1B5E20;
      transform: translateY(-2px);
    }

    /* Tables */
    .table-container {
      overflow-x: auto;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    thead {
      background: var(--beige);
    }

    th {
      padding: 16px;
      text-align: left;
      font-size: 14px;
      font-weight: 600;
      color: var(--text-dark);
      border-bottom: 2px solid var(--border);
    }

    td {
      padding: 16px;
      font-size: 14px;
      color: var(--text-dark);
      border-bottom: 1px solid var(--border);
    }

    tr:hover {
      background: var(--beige);
    }

    .status-badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }

    .status-badge.pending {
      background: rgba(255, 167, 38, 0.1);
      color: var(--pending);
    }

    .status-badge.shipped {
      background: rgba(46, 125, 50, 0.1);
      color: var(--success);
    }

    .status-badge.completed {
      background: rgba(46, 125, 50, 0.1);
      color: var(--success);
    }

    .status-badge.cancelled {
      background: rgba(211, 47, 47, 0.1);
      color: var(--error);
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

    /* Product Grid */
    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .product-card {
      background: var(--beige);
      border-radius: 12px;
      overflow: hidden;
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }

    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 12px rgba(15, 94, 77, 0.15);
      border-color: var(--thrift-green);
    }

    .product-image {
      width: 100%;
      height: 200px;
      background: var(--white);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 48px;
      color: var(--text-light);
    }

    .product-info {
      padding: 16px;
    }

    .product-name {
      font-size: 16px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 8px;
    }

    .product-price {
      font-size: 18px;
      font-weight: 700;
      color: var(--thrift-green);
      margin-bottom: 12px;
    }

    .product-actions {
      display: flex;
      gap: 8px;
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

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: var(--text-muted);
    }

    .empty-state-icon {
      font-size: 64px;
      margin-bottom: 20px;
      opacity: 0.5;
    }

    .empty-state-text {
      font-size: 16px;
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }

    .modal.active {
      display: flex;
    }

    .modal-content {
      background: var(--white);
      border-radius: 16px;
      padding: 40px;
      max-width: 600px;
      width: 90%;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 2px solid var(--beige);
    }

    .modal-title {
      font-size: 24px;
      font-weight: 600;
      color: var(--text-dark);
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 24px;
      color: var(--text-muted);
      cursor: pointer;
      padding: 0;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .modal-close:hover {
      background: var(--beige);
      color: var(--text-dark);
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
          <a href="#dashboard" class="sidebar-link active" data-section="dashboard">
            <span>📊</span>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#products" class="sidebar-link" data-section="products">
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
          <a href="category.php" class="sidebar-link">
            <span>📁</span>
            <span>Manage Categories</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#orders" class="sidebar-link" data-section="orders">
            <span>🛍️</span>
            <span>Orders</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="../view/chat_inbox.php" class="sidebar-link">
            <span>💬</span>
            <span>Messages</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#sales" class="sidebar-link" data-section="sales">
            <span>📈</span>
            <span>Sales History</span>
          </a>
        </li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Dashboard Overview Section -->
      <section id="dashboard" class="section active">
        <div class="section-header">
          <div>
            <h2 class="section-title">
              <span>📊</span>
              <span>Dashboard Overview</span>
            </h2>
            <p class="section-subtitle">Manage your products and track your sales</p>
          </div>
        </div>

        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-value" id="total-products">0</div>
            <div class="stat-label">Total Products Listed</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-value" id="pending-orders">0</div>
            <div class="stat-label">Pending Orders</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-value" id="earnings">₵0</div>
            <div class="stat-label">Earnings (This Month)</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-value" id="completed-orders">0</div>
            <div class="stat-label">Completed Orders</div>
          </div>
        </div>

        <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 20px; margin-top: 40px;">Recent Activity</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
          <div>
            <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-muted);">Recent Orders</h4>
            <div class="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody id="recent-orders-table">
                  <tr>
                    <td colspan="4" class="empty-state">
                      <div class="empty-state-text">No recent orders</div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div>
            <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-muted);">Top Products</h4>
            <div class="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Sales</th>
                    <th>Revenue</th>
                  </tr>
                </thead>
                <tbody id="top-products-table">
                  <tr>
                    <td colspan="3" class="empty-state">
                      <div class="empty-state-text">No products yet</div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>

      <!-- My Products Section -->
      <section id="products" class="section">
        <div class="section-header">
          <div>
            <h2 class="section-title">
              <span>📦</span>
              <span>My Products</span>
            </h2>
            <p class="section-subtitle">Manage your product listings</p>
          </div>
          <a href="#add-product" class="btn-primary" onclick="showSection('add-product'); return false;">
            <span>➕</span>
            <span>Add New Product</span>
          </a>
        </div>

        <div class="products-grid" id="products-grid"></div>
      </section>

      <!-- Manage Brands Section -->
      <section id="brands" class="section">
        <div class="section-header">
          <div>
            <h2 class="section-title">
              <span>🏷️</span>
              <span>Manage Brands</span>
            </h2>
            <p class="section-subtitle">Add, edit, and organize your brand portfolio</p>
          </div>
        </div>

        <!-- Add New Brand Form -->
        <div class="add-form-section">
          <h3 class="add-form-title">Add New Brand</h3>
          <form id="addBrandForm" class="add-form-wrapper">
            <input
              type="text"
              id="brandNameInput"
              name="brand_name"
              class="add-form-input"
              placeholder="Brand Name"
              required
              minlength="2"
              maxlength="100" />
            <button type="submit" class="add-form-btn" id="addBrandBtn">
              <i class="fas fa-plus"></i>
              <span>Add Brand</span>
            </button>
          </form>
          <div id="brandMessage" style="margin-top: 12px; font-size: 14px;"></div>
        </div>

        <!-- All Brands List -->
        <div class="list-section">
          <div class="list-header">
            <h3 class="list-title"><span>All Brands</span></h3>
            <div class="list-count" id="brandCount">0 total</div>
          </div>
          <div class="items-grid" id="brandsGrid"></div>
        </div>

              // Add product form handler
              document.getElementById('addProductForm').addEventListener('submit', (e) => {
              e.preventDefault();
              const formData = new FormData(e.target);
              const newProduct = {
              id: products.length + 1,
              name: formData.get('productName'),
              price: formData.get('productPrice'),
              category: formData.get('productCategory'),
              condition: formData.get('productCondition'),
              description: formData.get('productDescription'),
              image: '📦'
              };
              products.push(newProduct);
              renderProducts();
              updateDashboardStats();
              alert('Product added successfully! (This is a frontend-only demo)');
              e.target.reset();
              document.getElementById('imagePreview').innerHTML = '';
              showSection('products');
              });

              // Image preview
              document.getElementById('productImages').addEventListener('change', (e) => {
              const preview = document.getElementById('imagePreview');
              preview.innerHTML = '';
              Array.from(e.target.files).forEach(file => {
              const reader = new FileReader();
              reader.onload = (e) => {
              const img = document.createElement('img');
              img.src = e.target.result;
              img.style.width = '100%';
              img.style.height = '100px';
              img.style.objectFit = 'cover';
              img.style.borderRadius = '8px';
              preview.appendChild(img);
              };
              reader.readAsDataURL(file);
              });
              });

              // Product actions
              function editProduct(id) {
              const product = products.find(p => p.id === id);
              if (product) {
              alert(`Edit product: ${product.name}\n(This is a frontend-only demo. Backend integration would be implemented here.)`);
              }
              }

              function deleteProduct(id) {
              if (confirm('Are you sure you want to delete this product?')) {
              products = products.filter(p => p.id !== id);
              renderProducts();
              updateDashboardStats();
              alert('Product deleted successfully! (This is a frontend-only demo)');
              }
              }

              function shipOrder(id) {
              const order = orders.find(o => o.id === id);
              if (order) {
              order.status = 'shipped';
              renderOrders();
              renderSalesHistory();
              renderRecentOrders();
              updateDashboardStats();
              alert('Order marked as shipped! (This is a frontend-only demo)');
              }
              }

              // Brand Management Functions
              let brands = [];
              let categories = [];

              // Load brands from server
              async function loadBrands() {
              try {
              const response = await fetch('../actions/get_brands_action.php');
              const result = await response.json();

              if (result.success) {
              brands = result.brands || [];
              renderBrands();
              }
              } catch (error) {
              console.error('Error loading brands:', error);
              }
              }

              // Render brands
              function renderBrands() {
              const grid = document.getElementById('brandsGrid');
              const countEl = document.getElementById('brandCount');

              if (!grid) return;

              countEl.textContent = `${brands.length} total`;

              if (brands.length === 0) {
              grid.innerHTML = `
              <div class="empty-state" style="grid-column: 1 / -1;">
                <div class="empty-state-icon">🏷️</div>
                <div class="empty-state-text">No brands yet. Add your first brand above.</div>
              </div>
              `;
              return;
              }

              
              

              // Add brand form handler
              document.getElementById('addBrandForm')?.addEventListener('submit', async (e) => {
              e.preventDefault();

              const form = e.target;
              const brandName = document.getElementById('brandNameInput').value.trim();
              const messageEl = document.getElementById('brandMessage');
              const submitBtn = document.getElementById('addBrandBtn');

              if (!brandName) {
              messageEl.textContent = 'Please enter a brand name.';
              messageEl.style.color = 'var(--error)';
              return;
              }

              submitBtn.disabled = true;
              submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
              messageEl.textContent = '';

              try {
              const formData = new FormData();
              formData.append('brand_name', brandName);

              const response = await fetch('../actions/add_brand_action.php', {
              method: 'POST',
              body: formData
              });

              const result = await response.json();

              if (result.success) {
              messageEl.textContent = result.message || 'Brand added successfully!';
              messageEl.style.color = 'var(--success)';
              form.reset();
              await loadBrands(); // Reload brands
              } else {
              messageEl.textContent = result.message || 'Failed to add brand.';
              messageEl.style.color = 'var(--error)';
              }
              } catch (error) {
              messageEl.textContent = 'An error occurred. Please try again.';
              messageEl.style.color = 'var(--error)';
              console.error('Error:', error);
              } finally {
              submitBtn.disabled = false;
              submitBtn.innerHTML = '<i class="fas fa-plus"></i> <span>Add Brand</span>';
              }
              });

              // Edit brand
              function editBrand(brandId, currentName) {
              const newName = prompt('Edit brand name:', currentName);
              if (newName && newName.trim() && newName.trim() !== currentName) {
              // TODO: Implement edit functionality
              alert('Edit functionality will be implemented with update action.');
              }
              }

              // Delete brand
              async function deleteBrand(brandId) {
              if (!confirm('Are you sure you want to delete this brand?')) {
              return;
              }

              try {
              const formData = new FormData();
              formData.append('brand_id', brandId);

              const response = await fetch('../actions/delete_brand_action.php', {
              method: 'POST',
              body: formData
              });

              const result = await response.json();

              if (result.success) {
              await loadBrands(); // Reload brands
              } else {
              alert(result.message || 'Failed to delete brand.');
              }
              } catch (error) {
              alert('An error occurred. Please try again.');
              console.error('Error:', error);
              }
              }

              // Category Management Functions
              // Load categories from server
              async function loadCategories() {
              try {
              const response = await fetch('../actions/get_categories_action.php');
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

              
              

              // Add category form handler
              document.getElementById('addCategoryForm')?.addEventListener('submit', async (e) => {
              e.preventDefault();

              const form = e.target;
              const categoryName = document.getElementById('categoryNameInput').value.trim();
              const messageEl = document.getElementById('categoryMessage');
              const submitBtn = document.getElementById('addCategoryBtn');

              if (!categoryName) {
              messageEl.textContent = 'Please enter a category name.';
              messageEl.style.color = 'var(--error)';
              return;
              }

              submitBtn.disabled = true;
              submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
              messageEl.textContent = '';

              try {
              const formData = new FormData();
              formData.append('cat_name', categoryName);

              const response = await fetch('../actions/add_category_action.php', {
              method: 'POST',
              body: formData
              });

              const result = await response.json();

              if (result.success) {
              messageEl.textContent = result.message || 'Category added successfully!';
              messageEl.style.color = 'var(--success)';
              form.reset();
              await loadCategories(); // Reload categories
              } else {
              messageEl.textContent = result.message || 'Failed to add category.';
              messageEl.style.color = 'var(--error)';
              }
              } catch (error) {
              messageEl.textContent = 'An error occurred. Please try again.';
              messageEl.style.color = 'var(--error)';
              console.error('Error:', error);
              } finally {
              submitBtn.disabled = false;
              submitBtn.innerHTML = '<i class="fas fa-plus"></i> <span>Add Category</span>';
              }
              });

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

              // Populate brand dropdown in add product form
              async function populateBrandDropdown() {
              const brandSelect = document.getElementById('productBrand');
              if (!brandSelect) return;

              try {
              const response = await fetch('../actions/get_brands_action.php');
              const result = await response.json();

              if (result.success && result.brands) {
              // Clear existing options except the first one
              brandSelect.innerHTML = '<option value="">Select brand (optional)</option>';

              // Add brands from database
              result.brands.forEach(brand => {
              const option = document.createElement('option');
              option.value = brand.brand_id;
              option.textContent = brand.brand_name;
              brandSelect.appendChild(option);
              });
              }
              } catch (error) {
              console.error('Error loading brands for dropdown:', error);
              }
              }

              // Populate category dropdown in add product form
              async function populateCategoryDropdown() {
              const categorySelect = document.getElementById('productCategory');
              if (!categorySelect) return;

              try {
              const response = await fetch('../actions/get_categories_action.php');
              const result = await response.json();

              if (result.success && result.categories) {
              // Clear existing options except the first one
              categorySelect.innerHTML = '<option value="">Select category</option>';

              // Add categories from database
              result.categories.forEach(category => {
              const option = document.createElement('option');
              option.value = category.cat_id;
              option.textContent = category.cat_name;
              categorySelect.appendChild(option);
              });
              }
              } catch (error) {
              console.error('Error loading categories for dropdown:', error);
              }
              }

              // Load brands and categories when their sections are shown
              document.querySelectorAll('.sidebar-link[data-section="brands"]').forEach(link => {
              link.addEventListener('click', () => {
              setTimeout(() => loadBrands(), 100);
              });
              });

              document.querySelectorAll('.sidebar-link[data-section="categories"]').forEach(link => {
              link.addEventListener('click', () => {
              setTimeout(() => loadCategories(), 100);
              });
              });

              // Load brands and categories dropdowns when add-product section is shown
              document.querySelectorAll('.sidebar-link[data-section="add-product"]').forEach(link => {
              link.addEventListener('click', () => {
              setTimeout(() => {
              populateBrandDropdown();
              populateCategoryDropdown();
              }, 100);
              });
              });

              <!-- Dashboard Scripts -->
              <script src="../assets/js/seller_dashboard.js"></script>
              <script>
                (function() {
                  function showError(msg, url, line) {
                    var main = document.querySelector('.main-content') || document.body;
                    var box = document.createElement('div');
                    box.style = 'position:fixed;right:20px;bottom:20px;z-index:99999;background:#111;color:#fff;padding:12px 16px;border-radius:8px;max-width:360px;font-family: monospace;box-shadow:0 6px 20px rgba(0,0,0,0.4)';
                    box.textContent = 'JS Error: ' + msg + (url ? (' @ ' + url + ':' + line) : '');
                    document.body.appendChild(box);
                    console.error('JS Error:', msg, url, line);
                  }
                  window.onerror = function(msg, url, line, col, err) {
                    showError(msg, url, line);
                  };
                  window.addEventListener('unhandledrejection', function(e) {
                    var reason = (e && e.reason && e.reason.message) ? e.reason.message : (e && e.reason) || 'Unknown rejection';
                    showError('Unhandled promise rejection: ' + reason);
                  });
                })();
              </script>
</body>

</html>
<?php
/**
 * Browse Products Page
 * ThriftHub - Product Browsing and Filtering
 */

require_once __DIR__ . '/../settings/core.php';
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>All Thrift Items — ThriftHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet" />
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
      --border: #E8E3D8;
      --shadow-sm: 0 2px 8px rgba(15, 94, 77, 0.08);
      --shadow-md: 0 4px 16px rgba(15, 94, 77, 0.12);
      --shadow-lg: 0 8px 32px rgba(15, 94, 77, 0.16);
      --shadow-xl: 0 12px 48px rgba(15, 94, 77, 0.2);
      --gradient-green: linear-gradient(135deg, #0F5E4D 0%, #1A7A66 100%);
      --gradient-gold: linear-gradient(135deg, #C9A961 0%, #E5D4A8 100%);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: var(--text-dark);
      background: linear-gradient(to bottom, #F6F2EA 0%, #FFFFFF 100%);
      min-height: 100vh;
    }

    /* Header */
    .header {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      padding: 18px 40px;
      box-shadow: var(--shadow-sm);
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom: 1px solid var(--border);
    }

    .header-content {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 28px;
      font-weight: 700;
      color: var(--thrift-green);
      text-decoration: none;
      white-space: nowrap;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 24px;
      list-style: none;
    }

    .nav-links a {
      color: var(--text-dark);
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .nav-links a:hover {
      color: var(--thrift-green);
    }

    .search-bar {
      flex: 1;
      max-width: 500px;
      position: relative;
      display: flex;
      align-items: center;
    }

    .search-bar form {
      width: 100%;
      position: relative;
      display: flex;
      align-items: center;
    }

    .search-bar form button[type="submit"] {
      position: absolute;
      right: 8px;
      background: var(--thrift-green);
      color: var(--white);
      border: none;
      border-radius: 8px;
      padding: 10px 20px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 600;
      transition: all 0.3s ease;
      display: flex !important;
      align-items: center;
      justify-content: center;
      z-index: 1;
    }

    .search-bar form button[type="submit"]:hover {
      background: var(--thrift-green-dark);
      transform: translateY(-1px);
    }

    .search-bar form button[type="submit"]:active {
      transform: translateY(0);
    }

    .search-bar input {
      width: 100%;
      padding: 14px 100px 14px 48px;
      border: 2px solid var(--border);
      border-radius: 16px;
      font-size: 14px;
      background: var(--beige);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: var(--shadow-sm);
    }

    .search-bar input:focus {
      outline: none;
      border-color: var(--thrift-green);
      background: var(--white);
      box-shadow: var(--shadow-md);
      transform: translateY(-1px);
    }

    .search-bar i {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      pointer-events: none;
      z-index: 1;
    }

    .header-icons {
      display: flex;
      align-items: center;
      gap: 12px;
      position: relative;
      z-index: 10002;
    }

    .header-icon {
      width: 44px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 12px;
      background: var(--white);
      color: var(--thrift-green);
      text-decoration: none;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      font-size: 18px;
      border: 2px solid var(--border);
      box-shadow: var(--shadow-sm);
    }

    .header-icon:hover {
      background: var(--gradient-green);
      color: var(--white);
      border-color: var(--thrift-green);
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    .header-icon.cart-icon {
      background: var(--gradient-green);
      color: var(--white);
      border-color: var(--thrift-green);
    }

    .header-icon.cart-icon:hover {
      background: var(--thrift-green-dark);
      transform: translateY(-3px) scale(1.05);
      box-shadow: var(--shadow-lg);
    }

    .header-icon .badge {
      position: absolute;
      top: -6px;
      right: -6px;
      background: var(--gold);
      color: var(--white);
      border-radius: 50%;
      min-width: 20px;
      height: 20px;
      font-size: 11px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      border: 2px solid var(--white);
      box-shadow: var(--shadow-sm);
      padding: 0 4px;
    }

    .user-link {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--text-dark);
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
    }

    /* Profile Dropdown */
    .profile-dropdown-container {
      position: relative;
      z-index: 10001;
    }

    .profile-btn {
      cursor: pointer;
      border: none;
      padding: 0;
      background: inherit;
      width: 44px;
      height: 44px;
    }

    .profile-btn:focus {
      outline: none;
    }

    .profile-btn:hover {
      background: var(--gradient-green);
    }

    .profile-avatar,
    .profile-dropdown-avatar {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      background: var(--gradient-green);
      color: var(--white);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 16px;
    }

    .profile-avatar-img,
    .profile-dropdown-avatar-img {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      object-fit: cover;
    }

    .profile-dropdown {
      position: absolute;
      top: calc(100% + 12px);
      right: 0;
      width: 360px;
      background: #2C2C2C;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
      z-index: 10000;
      display: none;
      overflow: hidden;
    }

    .profile-dropdown.active {
      display: block;
    }

    .profile-dropdown-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .profile-dropdown-email {
      color: #FFFFFF;
      font-size: 14px;
      font-weight: 500;
    }

    .profile-dropdown-close {
      background: none;
      border: none;
      color: #FFFFFF;
      font-size: 24px;
      cursor: pointer;
      padding: 0;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 4px;
      transition: background 0.2s;
    }

    .profile-dropdown-close:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    .profile-dropdown-content {
      padding: 24px 20px 20px;
      text-align: center;
    }

    .profile-dropdown-avatar {
      width: 80px;
      height: 80px;
      margin: 0 auto 16px;
      font-size: 32px;
    }

    .profile-dropdown-avatar-img {
      width: 80px;
      height: 80px;
      margin: 0 auto 16px;
      display: block;
    }

    .profile-dropdown-name {
      color: #FFFFFF;
      font-size: 18px;
      font-weight: 500;
      margin-bottom: 20px;
    }

    .profile-dropdown-manage-btn {
      width: 100%;
      padding: 12px 20px;
      background: transparent;
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 8px;
      color: #FFFFFF;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      margin-bottom: 16px;
    }

    .profile-dropdown-manage-btn:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.5);
    }

    .profile-dropdown-actions {
      display: flex;
      gap: 12px;
      margin-bottom: 20px;
    }

    .profile-dropdown-signout-btn {
      flex: 1;
      padding: 12px 20px;
      background: transparent;
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 8px;
      color: #FFFFFF;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .profile-dropdown-signout-btn:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.5);
    }

    .profile-dropdown-footer {
      padding-top: 16px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      font-size: 12px;
    }

    .profile-dropdown-link {
      color: rgba(255, 255, 255, 0.7);
      text-decoration: none;
      transition: color 0.2s;
    }

    .profile-dropdown-link:hover {
      color: #FFFFFF;
    }

    .profile-dropdown-separator {
      color: rgba(255, 255, 255, 0.5);
    }

    @media (max-width: 640px) {
      .profile-dropdown {
        width: 320px;
        right: -20px;
      }
    }

    /* Main Container */
    .main-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 40px;
      display: grid;
      grid-template-columns: 320px 1fr;
      gap: 40px;
    }

    @media (max-width: 968px) {
      .main-container {
        grid-template-columns: 1fr;
        padding: 20px;
        gap: 30px;
      }
    }

    /* Sidebar Filters */
    .filters-sidebar {
      background: var(--white);
      border-radius: 20px;
      padding: 32px;
      box-shadow: var(--shadow-md);
      height: fit-content;
      position: sticky;
      top: 100px;
      border: 1px solid var(--border);
    }

    .filter-section {
      margin-bottom: 32px;
      padding-bottom: 24px;
      border-bottom: 1px solid var(--border);
    }

    .filter-section:last-of-type {
      border-bottom: none;
      margin-bottom: 24px;
    }

    .filter-title {
      font-size: 16px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .filter-title i {
      color: var(--thrift-green);
      font-size: 18px;
    }

    /* Price Range */
    .price-range {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .price-inputs {
      display: flex;
      gap: 8px;
      align-items: center;
    }

    .price-inputs span {
      color: var(--text-muted);
      font-size: 14px;
    }

    .price-input {
      flex: 1;
      padding: 12px 14px;
      border: 2px solid var(--border);
      border-radius: 12px;
      font-size: 14px;
      background: var(--beige);
      color: var(--text-dark);
      text-align: center;
      min-width: 0;
      transition: all 0.3s ease;
      font-weight: 500;
    }

    .price-input:focus {
      outline: none;
      border-color: var(--thrift-green);
      background: var(--white);
      box-shadow: var(--shadow-sm);
      transform: translateY(-1px);
    }

    .price-slider {
      width: 100%;
      height: 6px;
      border-radius: 3px;
      background: var(--beige);
      outline: none;
      -webkit-appearance: none;
    }

    .price-slider::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: var(--thrift-green);
      cursor: pointer;
    }

    .price-slider::-moz-range-thumb {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: var(--thrift-green);
      cursor: pointer;
      border: none;
    }

    /* Checkboxes */
    .checkbox-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .checkbox-item {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .checkbox-item input[type="checkbox"] {
      width: 18px;
      height: 18px;
      accent-color: var(--thrift-green);
      cursor: pointer;
    }

    .checkbox-item label {
      font-size: 14px;
      color: var(--text-dark);
      cursor: pointer;
    }

    /* Size Buttons */
    .size-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .size-btn {
      padding: 8px 16px;
      border: 2px solid var(--border);
      border-radius: 8px;
      background: var(--white);
      color: var(--text-dark);
      font-size: 13px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .size-btn:hover {
      border-color: var(--thrift-green);
      color: var(--thrift-green);
    }

    .size-btn.active {
      background: var(--thrift-green);
      color: var(--white);
      border-color: var(--thrift-green);
    }

    /* Form Select Styling */
    .form-select {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid var(--border);
      border-radius: 12px;
      font-size: 14px;
      background: var(--beige);
      color: var(--text-dark);
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 500;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%230F5E4D' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 16px center;
      padding-right: 40px;
    }

    .form-select:focus {
      outline: none;
      border-color: var(--thrift-green);
      background-color: var(--white);
      box-shadow: var(--shadow-sm);
      transform: translateY(-1px);
    }

    /* Clear Filters Button */
    .clear-filters {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, var(--beige) 0%, #FFFFFF 100%);
      color: var(--thrift-green);
      border: 2px solid var(--thrift-green);
      border-radius: 12px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      margin-top: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .clear-filters:hover {
      background: var(--thrift-green);
      color: var(--white);
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    .clear-filters:active {
      transform: translateY(0);
    }

    /* Main Content */
    .products-section {
      background: transparent;
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 32px;
      padding: 24px 0;
      border-bottom: 2px solid var(--border);
    }

    .section-title {
      font-size: 36px;
      font-weight: 800;
      color: var(--text-dark);
      margin-bottom: 8px;
      background: var(--gradient-green);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      letter-spacing: -0.5px;
    }

    .items-count {
      font-size: 15px;
      color: var(--text-muted);
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .items-count::before {
      content: '';
      width: 8px;
      height: 8px;
      background: var(--thrift-green);
      border-radius: 50%;
      display: inline-block;
    }

    .sort-by {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .sort-by label {
      font-size: 14px;
      color: var(--text-muted);
    }

    .sort-by select {
      padding: 10px 16px;
      border: 2px solid var(--border);
      border-radius: 12px;
      font-size: 14px;
      background: var(--beige);
      color: var(--text-dark);
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 500;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%230F5E4D' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 36px;
    }

    .sort-by select:focus {
      outline: none;
      border-color: var(--thrift-green);
      background-color: var(--white);
      box-shadow: var(--shadow-sm);
    }

    /* Product Grid */
    .products-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 28px;
      margin-bottom: 50px;
    }

    @media (max-width: 1200px) {
      .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
      }
    }

    @media (max-width: 768px) {
      .products-grid {
        grid-template-columns: 1fr;
        gap: 20px;
      }
    }

    .product-card {
      background: var(--white);
      border-radius: 20px;
      overflow: hidden;
      border: 1px solid var(--border);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
      box-shadow: var(--shadow-sm);
      position: relative;
    }

    .product-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: var(--gradient-green);
      opacity: 0;
      transition: opacity 0.4s ease;
      z-index: 0;
    }

    .product-card:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: var(--shadow-xl);
      border-color: var(--thrift-green);
    }

    .product-card:hover::before {
      opacity: 0.03;
    }

    .product-image {
      width: 100%;
      height: 300px;
      background: linear-gradient(135deg, var(--beige) 0%, #FFFFFF 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 64px;
      color: var(--text-light);
      position: relative;
      overflow: hidden;
    }

    .product-image::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(to bottom, transparent 0%, rgba(15, 94, 77, 0.05) 100%);
      opacity: 0;
      transition: opacity 0.4s ease;
    }

    .product-card:hover .product-image::after {
      opacity: 1;
    }

    .product-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .product-card:hover .product-image img {
      transform: scale(1.1);
    }

    .product-info {
      padding: 24px;
      position: relative;
      z-index: 1;
      background: var(--white);
    }

    .product-name {
      font-size: 17px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 10px;
      line-height: 1.5;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .product-price {
      font-size: 24px;
      font-weight: 800;
      background: var(--gradient-green);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 16px;
      letter-spacing: -0.5px;
    }

    .product-rating {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 16px;
    }

    .stars {
      display: flex;
      gap: 2px;
      color: var(--gold);
      font-size: 14px;
    }

    .rating-text {
      font-size: 13px;
      color: var(--text-muted);
    }

    .add-to-cart-btn {
      width: 100%;
      padding: 14px;
      background: var(--gradient-green);
      color: var(--white);
      border: none;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: var(--shadow-sm);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      position: relative;
      overflow: hidden;
    }

    .add-to-cart-btn::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.2);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }

    .add-to-cart-btn:hover::before {
      width: 300px;
      height: 300px;
    }

    .add-to-cart-btn:hover {
      transform: translateY(-3px);
      box-shadow: var(--shadow-lg);
    }

    .add-to-cart-btn:active {
      transform: translateY(-1px);
    }

    /* Pagination */
    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 12px;
      margin-top: 50px;
      padding: 20px 0;
    }

    .pagination-btn {
      padding: 12px 20px;
      border: 2px solid var(--border);
      border-radius: 12px;
      background: var(--white);
      color: var(--text-dark);
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      text-decoration: none;
      min-width: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: var(--shadow-sm);
    }

    .pagination-btn:hover:not(:disabled):not(.active) {
      border-color: var(--thrift-green);
      color: var(--thrift-green);
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    .pagination-btn.active {
      background: var(--gradient-green);
      color: var(--white);
      border-color: var(--thrift-green);
      box-shadow: var(--shadow-md);
      transform: scale(1.05);
    }

    .pagination-btn:disabled {
      opacity: 0.4;
      cursor: not-allowed;
      transform: none;
    }

    /* Loading State */
    .loading {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 60px 20px;
      color: var(--text-muted);
      font-size: 16px;
    }

    .loading::after {
      content: '';
      width: 20px;
      height: 20px;
      border: 3px solid var(--border);
      border-top-color: var(--thrift-green);
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      margin-left: 12px;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Empty State */
    .empty-state {
      grid-column: 1 / -1;
      text-align: center;
      padding: 80px 20px;
      color: var(--text-muted);
    }

    .empty-state-icon {
      font-size: 64px;
      margin-bottom: 20px;
      opacity: 0.5;
    }
  </style>
</head>

<body>
  <!-- Header -->
  <header class="header">
    <div class="header-content">
      <a href="../index.php" class="logo">ThriftHub</a>

      <ul class="nav-links">
        <li><a href="../index.php">Home</a></li>
        <li><a href="browse_products.php">Shop</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="#">Sell</a></li>
        <li><a href="#">More <i class="fas fa-chevron-down"></i></a></li>
      </ul>

      <div class="search-bar">
        <form action="product_search_result.php" method="GET" id="searchForm">
          <i class="fas fa-search"></i>
          <input type="text" name="q" placeholder="Search for thrifted treasures..." id="searchInput" required>
          <button type="submit" aria-label="Search">
            <i class="fas fa-search"></i>
          </button>
        </form>
      </div>

      <div class="header-icons">
        <a href="cart.php" class="header-icon cart-icon" title="View Cart">
          <i class="fas fa-shopping-bag"></i>
          <span class="badge" id="cart-badge" style="display: none;">0</span>
        </a>
        <a href="#" class="header-icon" title="Notifications">
          <i class="fas fa-bell"></i>
        </a>
        <?php 
        // Check if user is logged in - check multiple session variables for reliability
        $isLoggedIn = (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) || 
                      (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true);
        if ($isLoggedIn): 
        ?>
          <div class="profile-dropdown-container">
            <button type="button" class="header-icon profile-btn" id="profileBtn" title="Account">
              <?php 
              $userInitial = isset($_SESSION['customer_name']) ? strtoupper(substr($_SESSION['customer_name'], 0, 1)) : 'U';
              $hasImage = isset($_SESSION['customer_image']) && !empty($_SESSION['customer_image']);
              ?>
              <?php if ($hasImage): ?>
                <img src="../<?php echo htmlspecialchars($_SESSION['customer_image']); ?>" alt="Profile" class="profile-avatar-img">
              <?php else: ?>
                <div class="profile-avatar"><?php echo htmlspecialchars($userInitial); ?></div>
              <?php endif; ?>
            </button>
            <div class="profile-dropdown" id="profileDropdown">
              <div class="profile-dropdown-header">
                <span class="profile-dropdown-email"><?php echo htmlspecialchars($_SESSION['customer_email'] ?? ''); ?></span>
                <button class="profile-dropdown-close" onclick="toggleProfileDropdown(event)">×</button>
              </div>
              <div class="profile-dropdown-content">
                <?php if ($hasImage): ?>
                  <img src="../<?php echo htmlspecialchars($_SESSION['customer_image']); ?>" alt="Profile" class="profile-dropdown-avatar-img">
                <?php else: ?>
                  <div class="profile-dropdown-avatar"><?php echo htmlspecialchars($userInitial); ?></div>
                <?php endif; ?>
                <div class="profile-dropdown-name">Hi, <?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'User'); ?>!</div>
                <button class="profile-dropdown-manage-btn" onclick="window.location.href='<?php echo isset($_SESSION['user_role_no']) && $_SESSION['user_role_no'] == 3 ? '../seller/seller_dashboard.php' : 'view_orders.php'; ?>'">
                  Manage your Account
                </button>
                <div class="profile-dropdown-actions">
                  <button class="profile-dropdown-signout-btn" onclick="window.location.href='../actions/logout.php'">
                    <i class="fas fa-sign-out-alt"></i>
                    Sign out
                  </button>
                </div>
                <div class="profile-dropdown-footer">
                  <a href="#" class="profile-dropdown-link">Privacy Policy</a>
                  <span class="profile-dropdown-separator">•</span>
                  <a href="#" class="profile-dropdown-link">Terms of Service</a>
                </div>
              </div>
            </div>
          </div>
        <?php else: ?>
          <a href="login.php" class="header-icon" title="Account">
            <i class="fas fa-user"></i>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Main Container -->
  <div class="main-container">
      <!-- Sidebar Filters -->
    <aside class="filters-sidebar">
      <!-- Category Filter -->
      <div class="filter-section">
        <h3 class="filter-title">
          <i class="fas fa-tags"></i>
          Category
        </h3>
        <select id="categoryFilter" class="form-select">
          <option value="">All Categories</option>
          <!-- Categories will be loaded dynamically -->
        </select>
      </div>

      <!-- Brand Filter -->
      <div class="filter-section">
        <h3 class="filter-title">
          <i class="fas fa-certificate"></i>
          Brand
        </h3>
        <select id="brandFilter" class="form-select">
          <option value="">All Brands</option>
          <!-- Brands will be loaded dynamically -->
        </select>
      </div>

      <!-- Price Range Filter -->
      <div class="filter-section">
        <h3 class="filter-title">
          <i class="fas fa-dollar-sign"></i>
          Price Range (₵)
        </h3>
        <div class="price-range">
          <div class="price-inputs">
            <input type="number" id="priceMin" class="price-input" placeholder="Min" min="0" step="0.01">
            <span style="color: var(--text-muted); font-weight: 600;">-</span>
            <input type="number" id="priceMax" class="price-input" placeholder="Max" min="0" step="0.01">
          </div>
        </div>
      </div>

      <!-- Condition Filter -->
      <div class="filter-section">
        <h3 class="filter-title">
          <i class="fas fa-star"></i>
          Condition
        </h3>
        <select id="conditionFilter" class="form-select">
          <option value="">All Conditions</option>
          <option value="new">New</option>
          <option value="like-new">Like New</option>
          <option value="good">Good</option>
          <option value="fair">Fair</option>
        </select>
      </div>

      <button class="clear-filters" onclick="clearAllFilters()">
        <i class="fas fa-times"></i>
        Clear All Filters
      </button>
    </aside>

    <!-- Main Products Section -->
    <main class="products-section">
      <div class="section-header">
        <div>
          <h1 class="section-title">All Thrift Items</h1>
          <p class="items-count" id="itemsCount">8 items found</p>
        </div>
        <div class="sort-by">
          <label for="sortSelect">Sort by:</label>
          <select id="sortSelect">
            <option value="newest">Newest First</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
            <option value="rating">Highest Rated</option>
          </select>
        </div>
      </div>

      <div class="products-grid" id="productsGrid">
        <div class="loading">Loading products...</div>
      </div>

      <!-- Pagination -->
      <div class="pagination">
        <button class="pagination-btn" id="prevBtn" onclick="changePage(-1)">Previous</button>
        <button class="pagination-btn active" onclick="changePage(1)">1</button>
        <button class="pagination-btn" onclick="changePage(2)">2</button>
        <button class="pagination-btn" onclick="changePage(3)">3</button>
        <button class="pagination-btn" id="nextBtn" onclick="changePage(1)">Next</button>
      </div>

      <!-- Recommendation Sections -->
      <section style="margin-top:60px;">
        <h2 style="font-size:28px;font-weight:800;margin-bottom:20px;">Trending Right Now</h2>
        <div id="trendingGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:24px;">
          <div class="loading">Loading trending products...</div>
        </div>
      </section>

      <section style="margin-top:60px;">
        <h2 style="font-size:28px;font-weight:800;margin-bottom:20px;">Recommended For You</h2>
        <div id="personalGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:24px;">
          <div class="loading">Loading your recommendations...</div>
        </div>
      </section>
    </main>
  </div>

  <script>
    let currentPage = 1;
    let itemsPerPage = 10;
    let allProducts = [];
    let filteredProducts = [];
    let selectedCategory = '';
    let selectedBrand = '';
    let priceMin = null;
    let priceMax = null;
    let selectedCondition = '';

    // Load products from server
    async function loadProducts(page = 1) {
      try {
        const params = new URLSearchParams();
        if (selectedCategory) params.append('category', selectedCategory);
        if (selectedBrand) params.append('brand', selectedBrand);
        if (priceMin !== null && priceMin !== '') params.append('price_min', priceMin);
        if (priceMax !== null && priceMax !== '') params.append('price_max', priceMax);
        if (selectedCondition) params.append('condition', selectedCondition);
        if (currentSort) params.append('sort', currentSort);
        params.append('page', page);

        const response = await fetch(`../actions/browse_products_action.php?${params.toString()}`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const text = await response.text();
        let result;
        
        try {
          result = JSON.parse(text);
        } catch (parseError) {
          console.error('JSON parse error:', parseError);
          console.error('Response text:', text);
          throw new Error('Invalid JSON response from server');
        }

        if (result.success) {
          allProducts = result.products || [];
          filteredProducts = result.products || [];
          currentPage = result.pagination?.current_page || page;
          
          renderProducts();
          updatePagination(result.pagination || {});
          updateItemsCount();
        } else {
          console.error('Error loading products:', result.message);
          document.getElementById('productsGrid').innerHTML = '<div class="empty-state"><div class="empty-state-icon">📦</div><div>No products found.</div></div>';
          updateItemsCount(0);
        }
      } catch (error) {
        console.error('Error loading products:', error);
        document.getElementById('productsGrid').innerHTML = '<div class="empty-state"><div class="empty-state-icon">⚠️</div><div>Error loading products. Please try again.</div></div>';
        updateItemsCount(0);
      }
    }

    // Render products
    function renderProducts() {
      const grid = document.getElementById('productsGrid');
      
      if (filteredProducts.length === 0) {
        grid.innerHTML = '<div class="empty-state"><div class="empty-state-icon">🔍</div><div>No products found matching your filters.</div></div>';
        updateItemsCount();
        return;
      }

      grid.innerHTML = filteredProducts.map(product => {
        const imageUrl = product.product_image ? `../${product.product_image}` : '../assets/images/landback.jpg';
        const condition = product.product_condition ? product.product_condition.charAt(0).toUpperCase() + product.product_condition.slice(1).replace('-', ' ') : '';
        return `
          <div class="product-card" onclick="viewProduct(${product.product_id})">
            <div class="product-image">
              <img src="${imageUrl}" alt="${escapeHtml(product.product_title)}" onerror="this.src='../assets/images/landback.jpg'" />
            </div>
            <div class="product-info">
              <div class="product-name">${escapeHtml(product.product_title)}</div>
              <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px; display: flex; flex-wrap: wrap; gap: 8px; align-items: center;">
                ${product.cat_name ? `<span style="background: var(--beige); padding: 4px 10px; border-radius: 8px; font-weight: 500;">${escapeHtml(product.cat_name)}</span>` : ''}
                ${product.brand_name ? `<span style="background: var(--beige); padding: 4px 10px; border-radius: 8px; font-weight: 500;">${escapeHtml(product.brand_name)}</span>` : ''}
                ${condition ? `<span style="background: linear-gradient(135deg, #C9A961 0%, #E5D4A8 100%); color: white; padding: 4px 10px; border-radius: 8px; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">${condition}</span>` : ''}
              </div>
              <div class="product-price">₵${parseFloat(product.product_price).toFixed(2)}</div>
              <button class="add-to-cart-btn" onclick="event.stopPropagation(); addToCart(${product.product_id})">
                <i class="fas fa-shopping-cart" style="margin-right: 8px;"></i>
                Add to Cart
              </button>
            </div>
          </div>
        `;
      }).join('');

      updateItemsCount();
    }
    
    // Escape HTML
    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }

    // Load categories for filter
    async function loadCategories() {
      try {
        const response = await fetch('../actions/get_public_categories_action.php');
        const result = await response.json();
        
        if (result.success && result.categories) {
          const select = document.getElementById('categoryFilter');
          result.categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.cat_id;
            option.textContent = category.cat_name;
            select.appendChild(option);
          });
        }
      } catch (error) {
        console.error('Error loading categories:', error);
      }
    }

    // Load brands for filter
    async function loadBrands() {
      try {
        const response = await fetch('../actions/get_public_brands_action.php');
        const result = await response.json();
        
        if (result.success && result.brands) {
          const select = document.getElementById('brandFilter');
          result.brands.forEach(brand => {
            const option = document.createElement('option');
            option.value = brand.brand_id;
            option.textContent = brand.brand_name;
            select.appendChild(option);
          });
        }
      } catch (error) {
        console.error('Error loading brands:', error);
      }
    }

    // Update items count
    function updateItemsCount(count = null) {
      const displayCount = count !== null ? count : filteredProducts.length;
      document.getElementById('itemsCount').textContent = `${displayCount} item${displayCount !== 1 ? 's' : ''} found`;
    }

    // Update pagination
    function updatePagination(pagination) {
      if (!pagination) return;
      
      const paginationDiv = document.querySelector('.pagination');
      const totalPages = pagination.total_pages;
      
      let html = '';
      
      // Previous button
      html += `<button class="pagination-btn" ${!pagination.has_prev ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">Previous</button>`;
      
      // Page numbers
      for (let i = 1; i <= totalPages; i++) {
        html += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
      }
      
      // Next button
      html += `<button class="pagination-btn" ${!pagination.has_next ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">Next</button>`;
      
      paginationDiv.innerHTML = html;
    }

    // Change page
    function changePage(page) {
      if (page < 1) return;
      currentPage = page;
      loadProducts(page);
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }

    // Filter by category
    document.getElementById('categoryFilter').addEventListener('change', (e) => {
      selectedCategory = e.target.value;
      currentPage = 1;
      loadProducts(1);
    });

    // Filter by brand
    document.getElementById('brandFilter').addEventListener('change', (e) => {
      selectedBrand = e.target.value;
      currentPage = 1;
      loadProducts(1);
    });

    // Filter by price range
    document.getElementById('priceMin').addEventListener('change', (e) => {
      priceMin = e.target.value ? parseFloat(e.target.value) : null;
      currentPage = 1;
      loadProducts(1);
    });

    document.getElementById('priceMax').addEventListener('change', (e) => {
      priceMax = e.target.value ? parseFloat(e.target.value) : null;
      currentPage = 1;
      loadProducts(1);
    });

    // Filter by condition
    document.getElementById('conditionFilter').addEventListener('change', (e) => {
      selectedCondition = e.target.value;
      currentPage = 1;
      loadProducts(1);
    });

    // Clear all filters
    function clearAllFilters() {
      selectedCategory = '';
      selectedBrand = '';
      priceMin = null;
      priceMax = null;
      selectedCondition = '';
      document.getElementById('categoryFilter').value = '';
      document.getElementById('brandFilter').value = '';
      document.getElementById('priceMin').value = '';
      document.getElementById('priceMax').value = '';
      document.getElementById('conditionFilter').value = '';
      currentPage = 1;
      loadProducts(1);
    }

    // Search
    document.getElementById('searchInput').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        const searchTerm = e.target.value.trim();
        if (searchTerm) {
          window.location.href = `product_search_result.php?q=${encodeURIComponent(searchTerm)}`;
        }
      }
    });

    // Add to cart
    async function addToCart(productId) {
      try {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', 1);
        
        const response = await fetch('../actions/add_to_cart_action.php', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          // Show success message (you can use SweetAlert if available)
          console.log('Product added to cart:', result.message);
          // Update cart badge
          updateCartBadge();
        } else {
          alert(result.message || 'Failed to add product to cart.');
        }
      } catch (error) {
        console.error('Error adding to cart:', error);
        alert('An error occurred. Please try again.');
      }
    }
    
    // Update cart badge
    async function updateCartBadge() {
      try {
        const response = await fetch('../actions/get_cart_action.php');
        const result = await response.json();
        
        if (result.success) {
          const badge = document.getElementById('cart-badge');
          if (badge) {
            const count = result.count || 0;
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
          }
        }
      } catch (error) {
        console.error('Error updating cart badge:', error);
      }
    }
    
    // Initialize cart badge on load
    document.addEventListener('DOMContentLoaded', () => {
      updateCartBadge();
    });

    // View product
    function viewProduct(productId) {
      window.location.href = `single_product.php?id=${productId}`;
    }

    // Sort functionality
    let currentSort = 'newest';
    
    document.getElementById('sortSelect').addEventListener('change', (e) => {
      currentSort = e.target.value;
      currentPage = 1; // Reset to first page when sorting changes
      loadProducts(1); // Reload products with new sort
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
      loadCategories();
      loadBrands();
      loadProducts(1);
      loadTrending();
      loadPersonalized();
    });

    // Profile Dropdown Toggle - Make it globally accessible
    window.toggleProfileDropdown = function(event) {
      console.log('toggleProfileDropdown called');
      if (event) {
        event.preventDefault();
        event.stopPropagation();
      }
      const dropdown = document.getElementById('profileDropdown');
      console.log('Dropdown element:', dropdown);
      if (dropdown) {
        const isActive = dropdown.classList.contains('active');
        console.log('Current state - active:', isActive);
        dropdown.classList.toggle('active');
        console.log('New state - active:', dropdown.classList.contains('active'));
      } else {
        console.error('Profile dropdown element not found!');
      }
      return false;
    };

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('profileDropdown');
      const profileBtn = document.getElementById('profileBtn');
      const profileContainer = document.querySelector('.profile-dropdown-container');
      
      if (dropdown && profileBtn && profileContainer) {
        // Close if clicking outside the profile container
        if (!profileContainer.contains(event.target)) {
          dropdown.classList.remove('active');
        }
      }
    });

    // Also attach event listener to profile button as backup
    document.addEventListener('DOMContentLoaded', function() {
      console.log('DOM loaded, setting up profile dropdown');
      const profileBtn = document.getElementById('profileBtn');
      console.log('Profile button:', profileBtn);
      if (profileBtn) {
        // Remove existing onclick and use event listener
        profileBtn.onclick = null;
        profileBtn.addEventListener('click', function(e) {
          console.log('Profile button clicked via event listener');
          e.preventDefault();
          e.stopPropagation();
          window.toggleProfileDropdown(e);
          return false;
        });
      }
    });

    // Build product card (shared)
    function buildRecCard(p){
      const imageUrl = p.product_image ? `../${p.product_image}` : '../assets/images/landback.jpg';
      return `<div class="product-card" style="min-height:100%;" onclick="viewProduct(${p.product_id})">
        <div class="product-image" style="height:200px;">
          <img src="${imageUrl}" alt="${escapeHtml(p.product_title)}" onerror="this.src='../assets/images/landback.jpg'" />
        </div>
        <div class="product-info" style="padding:16px;">
          <div class="product-name" style="font-size:15px;">${escapeHtml(p.product_title)}</div>
          <div class="product-price" style="font-size:20px;margin:10px 0;">₵${parseFloat(p.product_price).toFixed(2)}</div>
        </div>
      </div>`;
    }

    // Load trending products
    async function loadTrending(){
      try {
        const res = await fetch('../actions/get_trending_products_action.php?limit=8');
        const json = await res.json();
        const grid = document.getElementById('trendingGrid');
        if(json.success && json.data.length){
          grid.innerHTML = json.data.map(buildRecCard).join('');
        } else {
          grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1;">No trending data.</div>';
        }
      } catch (e){
        console.error(e);
        document.getElementById('trendingGrid').innerHTML = '<div class="empty-state" style="grid-column:1/-1;">Failed to load trending.</div>';
      }
    }

    // Load personalized recommendations (requires user session)
    async function loadPersonalized(){
      const customerId = <?php echo isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0); ?>;
      if(!customerId){
        document.getElementById('personalGrid').innerHTML = '<div class="empty-state" style="grid-column:1/-1;">Login to see personalized recommendations.</div>';
        return;
      }
      try {
        const res = await fetch(`../actions/get_user_recommendations_action.php?customer_id=${customerId}&limit=8`);
        const json = await res.json();
        const grid = document.getElementById('personalGrid');
        if(json.success && json.data.length){
            grid.innerHTML = json.data.map(buildRecCard).join('');
        } else {
            grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1;">No personalized data yet.</div>';
        }
      } catch (e){
        console.error(e);
        document.getElementById('personalGrid').innerHTML = '<div class="empty-state" style="grid-column:1/-1;">Failed to load personalized.</div>';
      }
    }
  </script>
</body>

</html>
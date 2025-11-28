<?php
/**
 * Single Product View
 * ThriftHub - Individual Product Detail Page
 * 
 * Displays full details of a single product
 */

require_once __DIR__ . '/../settings/core.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Product Details — ThriftHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    .header-icons {
      display: flex;
      align-items: center;
      gap: 12px;
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

    /* Main Container */
    .main-container {
      max-width: 800px;
      margin: 0 auto;
      padding: 30px;
    }

    /* Back Button */
    .back-button {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 12px 24px;
      background: var(--white);
      color: var(--thrift-green);
      border: 2px solid var(--thrift-green);
      border-radius: 12px;
      font-size: 15px;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: var(--shadow-sm);
      margin-bottom: 24px;
    }

    .back-button:hover {
      background: var(--gradient-green);
      color: var(--white);
      transform: translateX(-4px);
      box-shadow: var(--shadow-md);
    }

    .back-button i {
      transition: transform 0.3s ease;
    }

    .back-button:hover i {
      transform: translateX(-4px);
    }

    .breadcrumb {
      margin-bottom: 24px;
      font-size: 14px;
      color: var(--text-muted);
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .breadcrumb a {
      color: var(--thrift-green);
      text-decoration: none;
      transition: color 0.3s ease;
      font-weight: 500;
    }

    .breadcrumb a:hover {
      color: var(--thrift-green-dark);
      text-decoration: underline;
    }

    .breadcrumb span {
      color: var(--text-dark);
      font-weight: 600;
    }

    .product-detail {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
      background: var(--white);
      padding: 30px;
      border-radius: 20px;
      box-shadow: var(--shadow-lg);
      border: 1px solid var(--border);
      position: relative;
      overflow: hidden;
    }

    .product-detail::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--gradient-green);
    }

    @media (max-width: 968px) {
      .product-detail {
        grid-template-columns: 1fr;
        gap: 30px;
        padding: 30px;
      }
    }

    .product-image-section {
      position: relative;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: var(--shadow-md);
    }

    .product-main-image {
      width: 100%;
      height: 380px;
      object-fit: cover;
      border-radius: 16px;
      background: linear-gradient(135deg, var(--beige) 0%, #FFFFFF 100%);
      transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .product-image-section:hover .product-main-image {
      transform: scale(1.05);
    }

    .product-info-section {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .product-id {
      font-size: 12px;
      color: var(--text-light);
      font-family: monospace;
      background: var(--beige);
      padding: 6px 12px;
      border-radius: 8px;
      display: inline-block;
      width: fit-content;
      font-weight: 500;
    }

    .product-category {
      font-size: 13px;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 1.5px;
      font-weight: 700;
      background: var(--beige);
      padding: 8px 16px;
      border-radius: 20px;
      display: inline-block;
      width: fit-content;
    }

    .product-brand {
      font-size: 18px;
      color: var(--thrift-green);
      font-weight: 700;
      background: linear-gradient(135deg, var(--beige) 0%, #FFFFFF 100%);
      padding: 10px 18px;
      border-radius: 12px;
      display: inline-block;
      width: fit-content;
      border: 2px solid var(--border);
    }

    .product-title {
      font-size: 32px;
      font-weight: 800;
      background: var(--gradient-green);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      line-height: 1.2;
      letter-spacing: -0.5px;
    }

    .product-price {
      font-size: 36px;
      font-weight: 800;
      background: var(--gradient-green);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      letter-spacing: -1px;
    }

    .product-description {
      font-size: 15px;
      line-height: 1.7;
      color: var(--text-dark);
      padding: 20px;
      background: linear-gradient(135deg, var(--beige) 0%, #FFFFFF 100%);
      border-radius: 12px;
      border: 1px solid var(--border);
      box-shadow: var(--shadow-sm);
    }

    .product-description strong {
      color: var(--thrift-green);
      font-size: 18px;
      display: block;
      margin-bottom: 12px;
      font-weight: 700;
    }

    .product-keywords {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .keyword-tag {
      padding: 10px 18px;
      background: var(--white);
      border: 2px solid var(--border);
      border-radius: 20px;
      font-size: 14px;
      color: var(--text-dark);
      font-weight: 500;
      transition: all 0.3s ease;
      box-shadow: var(--shadow-sm);
    }

    .keyword-tag:hover {
      border-color: var(--thrift-green);
      background: var(--beige);
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    .add-to-cart-btn {
      padding: 18px 32px;
      background: var(--gradient-green);
      color: var(--white);
      border: none;
      border-radius: 16px;
      font-size: 17px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      width: 100%;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: var(--shadow-md);
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
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
      width: 400px;
      height: 400px;
    }

    .add-to-cart-btn:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-xl);
    }

    .add-to-cart-btn:active {
      transform: translateY(-2px);
    }

    .add-to-cart-btn i {
      font-size: 18px;
    }

    .loading {
      text-align: center;
      padding: 80px 20px;
      color: var(--text-muted);
      font-size: 18px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 20px;
    }

    .loading::after {
      content: '';
      width: 40px;
      height: 40px;
      border: 4px solid var(--border);
      border-top-color: var(--thrift-green);
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .error {
      text-align: center;
      padding: 80px 20px;
      color: #D32F2F;
      font-size: 18px;
      background: var(--white);
      border-radius: 20px;
      box-shadow: var(--shadow-md);
      border: 2px solid #D32F2F;
    }

    .error::before {
      content: '⚠️';
      font-size: 48px;
      display: block;
      margin-bottom: 16px;
    }

    /* Condition Badge */
    .product-condition {
      display: inline-block;
      padding: 10px 20px;
      background: var(--gradient-gold);
      color: white;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: var(--shadow-sm);
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
      </ul>
      <div class="header-icons">
        <a href="cart.php" class="header-icon cart-icon" title="View Cart">
          <i class="fas fa-shopping-bag"></i>
          <span class="badge" id="cart-badge" style="display: none;">0</span>
        </a>
      </div>
    </div>
  </header>

  <!-- Main Container -->
  <div class="main-container">
    <a href="browse_products.php" class="back-button">
      <i class="fas fa-arrow-left"></i>
      Back to Products
    </a>
    
    <div class="breadcrumb">
      <a href="../index.php">Home</a> / 
      <a href="browse_products.php">Products</a> / 
      <span id="breadcrumbProduct">Product</span>
    </div>

    <div id="productContainer">
      <div class="loading">Loading product details...</div>
    </div>
  </div>

  <script>
    const productId = <?php echo $productId; ?>;

    async function loadProduct() {
      if (productId <= 0) {
        document.getElementById('productContainer').innerHTML = '<div class="error">Invalid product ID.</div>';
        return;
      }

      try {
        const response = await fetch(`../actions/get_public_product_action.php?id=${productId}`);
        const result = await response.json();

        if (result.success && result.product) {
          const product = result.product;
          renderProduct(product);
        } else {
          document.getElementById('productContainer').innerHTML = '<div class="error">Product not found.</div>';
        }
      } catch (error) {
        console.error('Error loading product:', error);
        document.getElementById('productContainer').innerHTML = '<div class="error">Error loading product. Please try again.</div>';
      }
    }

    function renderProduct(product) {
      const imageUrl = product.product_image ? `../${product.product_image}` : '../assets/images/landback.jpg';
      const keywords = product.product_keywords ? product.product_keywords.split(',').map(k => k.trim()) : [];
      const condition = product.product_condition ? product.product_condition.charAt(0).toUpperCase() + product.product_condition.slice(1).replace('-', ' ') : '';
      
      document.getElementById('breadcrumbProduct').textContent = escapeHtml(product.product_title);
      
      document.getElementById('productContainer').innerHTML = `
        <div class="product-detail">
          <div class="product-image-section">
            <img src="${imageUrl}" alt="${escapeHtml(product.product_title)}" class="product-main-image" onerror="this.src='../assets/images/landback.jpg'" />
          </div>
          <div class="product-info-section">
            <div class="product-category">${escapeHtml(product.cat_name || 'Uncategorized')}</div>
            ${product.brand_name ? `<div class="product-brand">${escapeHtml(product.brand_name)}</div>` : ''}
            ${condition ? `<div class="product-condition">${condition}</div>` : ''}
            <h1 class="product-title">${escapeHtml(product.product_title)}</h1>
            <div class="product-price">₵${parseFloat(product.product_price).toFixed(2)}</div>
            <div class="product-description">
              <strong>Description</strong>
              ${escapeHtml(product.product_desc || 'No description available.')}
            </div>
            ${keywords.length > 0 ? `
              <div>
                <strong style="display: block; margin-bottom: 12px; color: var(--thrift-green); font-size: 18px; font-weight: 700;">Keywords</strong>
                <div class="product-keywords">
                  ${keywords.map(k => `<span class="keyword-tag">${escapeHtml(k)}</span>`).join('')}
                </div>
              </div>
            ` : ''}
            <button class="add-to-cart-btn" onclick="addToCart(${product.product_id})">
              <i class="fas fa-shopping-cart"></i>
              Add to Cart
            </button>
            ${product.seller_id ? `
            <form action="../actions/start_conversation.php" method="POST" style="margin-top: 12px;">
              <input type="hidden" name="seller_id" value="${product.seller_id}" />
              <input type="hidden" name="product_id" value="${product.product_id}" />
              <button type="submit" class="add-to-cart-btn" style="background: linear-gradient(135deg, #C9A961 0%, #E5D4A8 100%);">
                <i class="fas fa-comments"></i>
                Message Seller
              </button>
            </form>
            ` : ''}
          </div>
        </div>
      `;
    }

    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }

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
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'Added to Cart!',
              text: result.message || 'Product added to cart successfully.',
              timer: 2000,
              showConfirmButton: false
            });
          } else {
            alert(result.message || 'Product added to cart!');
          }
          // Update cart badge if function exists
          if (typeof updateCartBadge === 'function') {
            updateCartBadge();
          }
        } else {
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: result.message || 'Failed to add product to cart.'
            });
          } else {
            alert(result.message || 'Failed to add product to cart.');
          }
        }
      } catch (error) {
        console.error('Error adding to cart:', error);
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred. Please try again.'
          });
        } else {
          alert('An error occurred. Please try again.');
        }
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

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
      loadProduct();
      updateCartBadge();
    });
  </script>
</body>
</html>


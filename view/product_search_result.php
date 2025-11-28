<?php
/**
 * Product Search Results
 * ThriftHub - Search Results Page
 * 
 * Displays search results with optional pagination and filters
 */

require_once __DIR__ . '/../settings/core.php';

$searchTerm = isset($_GET['q']) ? htmlspecialchars(trim($_GET['q'])) : '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Search Results — ThriftHub</title>
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
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: var(--text-dark);
      background: var(--beige);
    }

    /* Header */
    .header {
      background: var(--white);
      padding: 16px 40px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      position: sticky;
      top: 0;
      z-index: 1000;
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

    /* Main Container */
    .main-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 40px;
      display: grid;
      grid-template-columns: 280px 1fr;
      gap: 40px;
    }

    @media (max-width: 968px) {
      .main-container {
        grid-template-columns: 1fr;
        padding: 20px;
      }
    }

    /* Sidebar Filters */
    .filters-sidebar {
      background: var(--white);
      padding: 24px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      height: fit-content;
      position: sticky;
      top: 100px;
    }

    .filter-section {
      margin-bottom: 24px;
    }

    .filter-title {
      font-size: 16px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 12px;
    }

    .filter-select {
      width: 100%;
      padding: 10px;
      border: 2px solid var(--border);
      border-radius: 8px;
      background: var(--beige);
      font-size: 14px;
      color: var(--text-dark);
    }

    .filter-select:focus {
      outline: none;
      border-color: var(--thrift-green);
      background: var(--white);
    }

    .clear-filters {
      width: 100%;
      padding: 12px;
      background: var(--beige);
      color: var(--thrift-green);
      border: 2px solid var(--border);
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 8px;
    }

    .clear-filters:hover {
      background: var(--white);
      border-color: var(--thrift-green);
    }

    /* Products Section */
    .products-section {
      background: var(--white);
      padding: 32px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .section-header {
      margin-bottom: 24px;
      padding-bottom: 20px;
      border-bottom: 2px solid var(--beige);
    }

    .section-title {
      font-size: 28px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 8px;
    }

    .items-count {
      font-size: 14px;
      color: var(--text-muted);
    }

    .search-term {
      color: var(--thrift-green);
      font-weight: 600;
    }

    /* Product Grid */
    .products-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 24px;
      margin-bottom: 40px;
    }

    @media (max-width: 768px) {
      .products-grid {
        grid-template-columns: 1fr;
      }
    }

    .product-card {
      background: var(--white);
      border-radius: 12px;
      overflow: hidden;
      border: 2px solid var(--border);
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      border-color: var(--thrift-green);
    }

    .product-image {
      width: 100%;
      height: 280px;
      background: var(--beige);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    .product-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .product-info {
      padding: 20px;
    }

    .product-name {
      font-size: 16px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 8px;
      line-height: 1.4;
    }

    .product-meta {
      font-size: 12px;
      color: var(--text-muted);
      margin-bottom: 8px;
    }

    .product-price {
      font-size: 20px;
      font-weight: 700;
      color: var(--thrift-green);
      margin-bottom: 12px;
    }

    .add-to-cart-btn {
      width: 100%;
      padding: 12px;
      background: var(--thrift-green);
      color: var(--white);
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .add-to-cart-btn:hover {
      background: var(--thrift-green-dark);
      transform: translateY(-2px);
    }

    /* Pagination */
    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
      margin-top: 40px;
    }

    .pagination-btn {
      padding: 10px 16px;
      border: 2px solid var(--border);
      border-radius: 8px;
      background: var(--white);
      color: var(--text-dark);
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
    }

    .pagination-btn:hover:not(:disabled) {
      border-color: var(--thrift-green);
      color: var(--thrift-green);
    }

    .pagination-btn.active {
      background: var(--thrift-green);
      color: var(--white);
      border-color: var(--thrift-green);
    }

    .pagination-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
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
    </div>
  </header>

  <!-- Main Container -->
  <div class="main-container">
    <!-- Sidebar Filters -->
    <aside class="filters-sidebar">
      <div class="filter-section">
        <h3 class="filter-title">Narrow Results</h3>
        <label style="display: block; margin-bottom: 8px; font-size: 14px; color: var(--text-dark);">Category:</label>
        <select id="categoryFilter" class="filter-select">
          <option value="">All Categories</option>
          <!-- Categories will be loaded dynamically -->
        </select>
      </div>

      <div class="filter-section">
        <label style="display: block; margin-bottom: 8px; font-size: 14px; color: var(--text-dark);">Brand:</label>
        <select id="brandFilter" class="filter-select">
          <option value="">All Brands</option>
          <!-- Brands will be loaded dynamically -->
        </select>
      </div>

      <div class="filter-section">
        <h3 class="filter-title">Price Range (₵)</h3>
        <div style="display: flex; flex-direction: column; gap: 12px;">
          <div style="display: flex; gap: 8px; align-items: center;">
            <input type="number" id="priceMin" class="filter-select" placeholder="Min" min="0" step="0.01" style="flex: 1;">
            <span style="color: var(--text-muted);">-</span>
            <input type="number" id="priceMax" class="filter-select" placeholder="Max" min="0" step="0.01" style="flex: 1;">
          </div>
        </div>
      </div>

      <div class="filter-section">
        <h3 class="filter-title">Condition</h3>
        <select id="conditionFilter" class="filter-select">
          <option value="">All Conditions</option>
          <option value="new">New</option>
          <option value="like-new">Like New</option>
          <option value="good">Good</option>
          <option value="fair">Fair</option>
        </select>
      </div>

      <button class="clear-filters" onclick="clearFilters()">Clear Filters</button>
    </aside>

    <!-- Products Section -->
    <main class="products-section">
      <div class="section-header">
        <h1 class="section-title">Search Results</h1>
        <p class="items-count">
          Search results for: <span class="search-term" id="searchTermDisplay"><?php echo $searchTerm; ?></span>
          <span id="itemsCount"></span>
        </p>
      </div>

      <div class="products-grid" id="productsGrid">
        <div class="empty-state">
          <div class="empty-state-icon">🔍</div>
          <div>Loading search results...</div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="pagination" id="pagination">
        <!-- Pagination will be rendered here -->
      </div>
    </main>
  </div>

  <script>
    const searchTerm = <?php echo json_encode($searchTerm); ?>;
    let currentPage = 1;
    let selectedCategory = '';
    let selectedBrand = '';
    let priceMin = null;
    let priceMax = null;
    let selectedCondition = '';

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

    // Load search results
    async function loadSearchResults(page = 1) {
      try {
        const params = new URLSearchParams();
        if (searchTerm) params.append('q', searchTerm);
        if (selectedCategory) params.append('category', selectedCategory);
        if (selectedBrand) params.append('brand', selectedBrand);
        if (priceMin !== null && priceMin !== '') params.append('price_min', priceMin);
        if (priceMax !== null && priceMax !== '') params.append('price_max', priceMax);
        if (selectedCondition) params.append('condition', selectedCondition);
        params.append('page', page);

        const url = `../actions/search_products_action.php?${params.toString()}`;
        console.log('Fetching search results from:', url);
        
        const response = await fetch(url);
        
        // Check if response is OK
        if (!response.ok) {
          const text = await response.text();
          console.error('Search response error:', response.status, text);
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Get response text first to check if it's valid JSON
        const responseText = await response.text();
        console.log('Search response:', responseText);
        
        let result;
        try {
          result = JSON.parse(responseText);
        } catch (parseError) {
          console.error('JSON parse error:', parseError);
          console.error('Response text:', responseText);
          throw new Error('Invalid JSON response from server');
        }

        if (result.success) {
          renderProducts(result.products || []);
          if (result.pagination) {
            updatePagination(result.pagination);
            updateItemsCount(result.pagination.total_products || 0);
          }
        } else {
          document.getElementById('productsGrid').innerHTML = `<div class="empty-state" style="grid-column: 1 / -1;"><div class="empty-state-icon">🔍</div><div>${result.message || 'No products found for your search.'}</div></div>`;
        }
      } catch (error) {
        console.error('Error loading search results:', error);
        document.getElementById('productsGrid').innerHTML = `<div class="empty-state" style="grid-column: 1 / -1;"><div class="empty-state-icon">⚠️</div><div>Error loading search results: ${error.message || 'Please try again.'}</div></div>`;
      }
    }

    // Render products
    function renderProducts(products) {
      const grid = document.getElementById('productsGrid');
      
      if (products.length === 0) {
        grid.innerHTML = '<div class="empty-state" style="grid-column: 1 / -1;"><div class="empty-state-icon">🔍</div><div>No products found matching your search criteria.</div></div>';
        return;
      }

      grid.innerHTML = products.map(product => {
        const imageUrl = product.product_image ? `../${product.product_image}` : '../assets/images/landback.jpg';
        return `
          <div class="product-card" onclick="viewProduct(${product.product_id})">
            <div class="product-image">
              <img src="${imageUrl}" alt="${escapeHtml(product.product_title)}" onerror="this.src='../assets/images/landback.jpg'" />
            </div>
            <div class="product-info">
              <div class="product-name">${escapeHtml(product.product_title)}</div>
              <div class="product-meta">
                ${product.cat_name ? 'Category: ' + escapeHtml(product.cat_name) : ''}
                ${product.brand_name ? ' | Brand: ' + escapeHtml(product.brand_name) : ''}
              </div>
              <div class="product-price">₵${parseFloat(product.product_price).toFixed(2)}</div>
              <button class="add-to-cart-btn" onclick="event.stopPropagation(); addToCart(${product.product_id})">Add to Cart</button>
            </div>
          </div>
        `;
      }).join('');
    }

    // Update pagination
    function updatePagination(pagination) {
      if (!pagination || pagination.total_pages <= 1) {
        document.getElementById('pagination').innerHTML = '';
        return;
      }

      const paginationDiv = document.getElementById('pagination');
      let html = '';
      
      // Previous button
      html += `<button class="pagination-btn" ${!pagination.has_prev ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">Previous</button>`;
      
      // Page numbers
      for (let i = 1; i <= pagination.total_pages; i++) {
        html += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
      }
      
      // Next button
      html += `<button class="pagination-btn" ${!pagination.has_next ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">Next</button>`;
      
      paginationDiv.innerHTML = html;
    }

    // Update items count
    function updateItemsCount(total) {
      document.getElementById('itemsCount').textContent = ` - ${total} result${total !== 1 ? 's' : ''} found`;
    }

    // Change page
    function changePage(page) {
      if (page < 1) return;
      currentPage = page;
      loadSearchResults(page);
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }

    // Filter by category
    document.getElementById('categoryFilter').addEventListener('change', (e) => {
      selectedCategory = e.target.value;
      currentPage = 1;
      loadSearchResults(1);
    });

    // Filter by brand
    document.getElementById('brandFilter').addEventListener('change', (e) => {
      selectedBrand = e.target.value;
      currentPage = 1;
      loadSearchResults(1);
    });

    // Filter by price range
    document.getElementById('priceMin').addEventListener('change', (e) => {
      priceMin = e.target.value ? parseFloat(e.target.value) : null;
      currentPage = 1;
      loadSearchResults(1);
    });

    document.getElementById('priceMax').addEventListener('change', (e) => {
      priceMax = e.target.value ? parseFloat(e.target.value) : null;
      currentPage = 1;
      loadSearchResults(1);
    });

    // Filter by condition
    document.getElementById('conditionFilter').addEventListener('change', (e) => {
      selectedCondition = e.target.value;
      currentPage = 1;
      loadSearchResults(1);
    });

    // Clear filters
    function clearFilters() {
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
      loadSearchResults(1);
    }

    // Escape HTML
    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }

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
          console.log('Product added to cart:', result.message);
          // You can add SweetAlert here if needed
        } else {
          alert(result.message || 'Failed to add product to cart.');
        }
      } catch (error) {
        console.error('Error adding to cart:', error);
        alert('An error occurred. Please try again.');
      }
    }

    // View product
    function viewProduct(productId) {
      window.location.href = `single_product.php?id=${productId}`;
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
      loadCategories();
      loadBrands();
      if (searchTerm) {
        loadSearchResults(1);
      } else {
        document.getElementById('productsGrid').innerHTML = '<div class="empty-state" style="grid-column: 1 / -1;"><div class="empty-state-icon">🔍</div><div>Please enter a search term.</div></div>';
      }
    });
  </script>
</body>
</html>


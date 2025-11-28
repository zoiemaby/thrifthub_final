// Seller Dashboard JavaScript
// ThriftHub - Dynamic data loading for seller dashboard

// Navigation between sections
function showSection(sectionId) {
  document.querySelectorAll('.section').forEach(section => {
    section.classList.remove('active');
  });
  
  document.querySelectorAll('.sidebar-link').forEach(link => {
    link.classList.remove('active');
  });
  
  const section = document.getElementById(sectionId);
  if (section) {
    section.classList.add('active');
  }
  
  const link = document.querySelector(`.sidebar-link[data-section="${sectionId}"]`);
  if (link) {
    link.classList.add('active');
  }
  
  // Load section-specific data
  if (sectionId === 'dashboard') {
    loadDashboardStats();
  } else if (sectionId === 'products') {
    loadProducts();
  } else if (sectionId === 'orders') {
    loadOrders();
  } else if (sectionId === 'sales') {
    loadSalesHistory();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.sidebar-link[data-section]').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const sectionId = link.getAttribute('data-section');
      showSection(sectionId);
    });
  });

  // Load initial dashboard data
  loadDashboardStats();
});

// Dashboard data loading functions
async function loadDashboardStats() {
  try {
    const response = await fetch('../actions/get_seller_dashboard_stats.php');
    const result = await response.json();
    
    if (result.success) {
      // Update stats
      document.getElementById('total-products').textContent = result.stats.total_products || 0;
      document.getElementById('pending-orders').textContent = result.stats.pending_orders || 0;
      document.getElementById('completed-orders').textContent = result.stats.completed_orders || 0;
      document.getElementById('earnings').textContent = '₵' + (result.stats.monthly_earnings || '0.00');
      
      // Update recent orders table
      renderRecentOrders(result.recent_orders || []);
      
      // Update top products table
      renderTopProducts(result.top_products || []);
    }
  } catch (error) {
    console.error('Error loading dashboard stats:', error);
  }
}

function renderRecentOrders(orders) {
  const tableBody = document.getElementById('recent-orders-table');
  
  if (orders.length === 0) {
    tableBody.innerHTML = `
      <tr>
        <td colspan="4" class="empty-state">
          <div class="empty-state-text">No recent orders</div>
        </td>
      </tr>
    `;
    return;
  }

  tableBody.innerHTML = orders.map(order => `
    <tr>
      <td>#${order.order_id}</td>
      <td>${order.customer_name}</td>
      <td><span class="status-badge ${order.status}">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span></td>
      <td>${new Date(order.date).toLocaleDateString()}</td>
    </tr>
  `).join('');
}

function renderTopProducts(products) {
  const tableBody = document.getElementById('top-products-table');
  
  if (products.length === 0) {
    tableBody.innerHTML = `
      <tr>
        <td colspan="3" class="empty-state">
          <div class="empty-state-text">No products yet</div>
        </td>
      </tr>
    `;
    return;
  }

  tableBody.innerHTML = products.map(product => `
    <tr>
      <td>${product.product_title}</td>
      <td>${product.sales_count}</td>
      <td>₵${parseFloat(product.revenue).toFixed(2)}</td>
    </tr>
  `).join('');
}

// Load products
async function loadProducts() {
  try {
    const response = await fetch('../actions/get_seller_products.php');
    const result = await response.json();
    
    if (result.success) {
      renderProducts(result.products || []);
    }
  } catch (error) {
    console.error('Error loading products:', error);
  }
}

function renderProducts(products) {
  const grid = document.getElementById('products-grid');
  
  if (products.length === 0) {
    grid.innerHTML = `
      <div class="empty-state" style="grid-column: 1 / -1;">
        <div class="empty-state-icon">📦</div>
        <div class="empty-state-text">No products listed yet. Add your first product!</div>
      </div>
    `;
    return;
  }

  grid.innerHTML = products.map(product => `
    <div class="product-card">
      <div class="product-image">
        ${product.product_image ? `<img src="../${product.product_image}" alt="${product.product_title}" style="width:100%;height:100%;object-fit:cover;">` : '📦'}
      </div>
      <div class="product-info">
        <div class="product-name">${product.product_title}</div>
        <div class="product-price">₵${parseFloat(product.product_price).toFixed(2)}</div>
        <div class="product-actions">
          <button class="btn-edit" onclick="editProduct(${product.product_id})">Edit</button>
          <button class="btn-delete" onclick="deleteProduct(${product.product_id}, '${product.product_title.replace(/'/g, "\\'")}')">Delete</button>
        </div>
      </div>
    </div>
  `).join('');
}

// Load orders
async function loadOrders() {
  try {
    const response = await fetch('../actions/get_seller_orders.php');
    const result = await response.json();
    
    if (result.success) {
      renderOrders(result.orders || []);
    }
  } catch (error) {
    console.error('Error loading orders:', error);
  }
}

function renderOrders(orders) {
  const tableBody = document.getElementById('orders-table');
  
  if (orders.length === 0) {
    tableBody.innerHTML = `
      <tr>
        <td colspan="7" class="empty-state">
          <div class="empty-state-icon">🛍️</div>
          <div class="empty-state-text">No orders yet</div>
        </td>
      </tr>
    `;
    return;
  }

  tableBody.innerHTML = orders.map(order => `
    <tr>
      <td>#${order.order_id}</td>
      <td>${order.products || 'N/A'}</td>
      <td>${order.customer_name || 'N/A'}</td>
      <td>₵${parseFloat(order.total_amount).toFixed(2)}</td>
      <td><span class="status-badge ${order.order_status}">${order.order_status.charAt(0).toUpperCase() + order.order_status.slice(1)}</span></td>
      <td>${new Date(order.order_date).toLocaleDateString()}</td>
      <td>
        ${order.order_status === 'pending' || order.order_status === 'paid' ? `<button class="btn-ship" onclick="shipOrder(${order.order_id})">Mark as Shipped</button>` : ''}
      </td>
    </tr>
  `).join('');
}

// Load sales history
async function loadSalesHistory() {
  try {
    const response = await fetch('../actions/get_seller_orders.php');
    const result = await response.json();
    
    if (result.success) {
      const completedOrders = (result.orders || []).filter(o => 
        o.order_status === 'completed' || o.order_status === 'shipped' || o.order_status === 'delivered'
      );
      renderSalesHistory(completedOrders);
    }
  } catch (error) {
    console.error('Error loading sales history:', error);
  }
}

function renderSalesHistory(orders) {
  const tableBody = document.getElementById('sales-table');
  
  if (orders.length === 0) {
    tableBody.innerHTML = `
      <tr>
        <td colspan="6" class="empty-state">
          <div class="empty-state-icon">📈</div>
          <div class="empty-state-text">No sales history yet</div>
        </td>
      </tr>
    `;
    return;
  }

  tableBody.innerHTML = orders.map(order => `
    <tr>
      <td>#${order.order_id}</td>
      <td>${order.products || 'N/A'}</td>
      <td>${order.customer_name || 'N/A'}</td>
      <td>₵${parseFloat(order.total_amount).toFixed(2)}</td>
      <td><span class="status-badge ${order.order_status}">${order.order_status.charAt(0).toUpperCase() + order.order_status.slice(1)}</span></td>
      <td>${new Date(order.order_date).toLocaleDateString()}</td>
    </tr>
  `).join('');
}

// Product actions
function editProduct(id) {
  window.location.href = `product.php?edit=${id}`;
}

async function deleteProduct(id, name) {
  if (!confirm(`Are you sure you want to delete "${name}"?`)) {
    return;
  }
  
  try {
    const formData = new FormData();
    formData.append('product_id', id);
    
    const response = await fetch('../actions/delete_product_action.php', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
      alert('Product deleted successfully!');
      loadProducts();
      loadDashboardStats(); // Refresh stats
    } else {
      alert(result.message || 'Failed to delete product.');
    }
  } catch (error) {
    alert('An error occurred. Please try again.');
    console.error('Error:', error);
  }
}

async function shipOrder(orderId) {
  if (!confirm('Mark this order as shipped?')) {
    return;
  }
  
  try {
    const formData = new FormData();
    formData.append('order_id', orderId);
    formData.append('status', 'shipped');
    
    const response = await fetch('../actions/update_order_status.php', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
      alert('Order marked as shipped!');
      loadOrders();
      loadDashboardStats(); // Refresh stats
    } else {
      alert(result.message || 'Failed to update order status.');
    }
  } catch (error) {
    alert('An error occurred. Please try again.');
    console.error('Error:', error);
  }
}

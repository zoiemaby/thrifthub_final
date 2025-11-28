<?php
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/sellerApplication_controller.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    header('Location: ../view/login.php');
    exit;
}

$roleNo = isset($_SESSION['user_role_no']) ? (int)$_SESSION['user_role_no'] : 0;
if ($roleNo !== ROLE_ADMIN) {
    header('Location: ../index.php');
    exit;
}

// Fetch pending seller applications
$appController = new SellerApplicationController();
$result = $appController->get_pending_applications_ctrl();
$pendingSellers = $result['success'] ? $result['applications'] : [];
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Admin Dashboard — ThriftHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
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

    .status-badge.approved {
      background: rgba(46, 125, 50, 0.1);
      color: var(--success);
    }

    .status-badge.rejected {
      background: rgba(211, 47, 47, 0.1);
      color: var(--error);
    }

    /* Buttons */
    .btn-group {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }

    .btn-approve {
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

    .btn-approve:hover {
      background: #1B5E20;
      transform: translateY(-2px);
    }

    .btn-decline {
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

    .btn-decline:hover {
      background: #B71C1C;
      transform: translateY(-2px);
    }

    .btn-view {
      padding: 8px 16px;
      background: var(--thrift-green);
      color: var(--white);
      border: none;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s ease;
    }

    .btn-view:hover {
      background: var(--thrift-green-dark);
      transform: translateY(-2px);
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

    /* Seller Details */
    .seller-details {
      background: var(--beige);
      border-radius: 12px;
      padding: 20px;
      margin-top: 12px;
    }

    .detail-row {
      display: grid;
      grid-template-columns: 150px 1fr;
      gap: 12px;
      padding: 12px 0;
      border-bottom: 1px solid var(--border);
    }

    .detail-row:last-child {
      border-bottom: none;
    }

    .detail-label {
      font-weight: 600;
      color: var(--text-muted);
      font-size: 13px;
    }

    .detail-value {
      color: var(--text-dark);
      font-size: 14px;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Header -->
    <div class="header">
      <div class="logo-section">
        <div class="logo">TH</div>
        <div class="header-title">Admin Dashboard</div>
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
          <a href="#sellers" class="sidebar-link" data-section="sellers">
            <span>👥</span>
            <span>Seller Management</span>
            <span id="pending-count" style="margin-left: auto; background: var(--pending); color: white; padding: 2px 8px; border-radius: 12px; font-size: 11px; display: none;">0</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#buyers" class="sidebar-link" data-section="buyers">
            <span>🛒</span>
            <span>Buyers</span>
          </a>
        </li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Dashboard Section -->
      <section id="dashboard" class="section active">
        <div class="section-header">
          <h2 class="section-title">
            <span>📊</span>
            <span>Dashboard Overview</span>
          </h2>
          <p class="section-subtitle">Platform statistics and activity summary</p>
        </div>

        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-value" id="active-sellers">0</div>
            <div class="stat-label">Active Sellers</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">🛒</div>
            <div class="stat-value" id="active-buyers">0</div>
            <div class="stat-label">Active Buyers</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-value" id="pending-sellers">0</div>
            <div class="stat-label">Pending Verifications</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-value" id="total-items">0</div>
            <div class="stat-label">Total Items</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">🛍️</div>
            <div class="stat-value" id="total-orders">0</div>
            <div class="stat-label">Total Orders</div>
          </div>
        </div>

        <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 20px; margin-top: 40px;">New User Registrations</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
          <div>
            <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-muted);">Recent Sellers</h4>
            <div class="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody id="recent-sellers-table">
                  <tr>
                    <td colspan="4" class="empty-state">
                      <div class="empty-state-text">No sellers yet</div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div>
            <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-muted);">Recent Buyers</h4>
            <div class="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody id="recent-buyers-table">
                  <tr>
                    <td colspan="3" class="empty-state">
                      <div class="empty-state-text">No buyers yet</div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>

      <!-- Seller Management Section -->
      <section id="sellers" class="section">
        <div class="section-header">
          <h2 class="section-title">
            <span>👥</span>
            <span>Seller Management</span>
          </h2>
          <p class="section-subtitle">Review and verify seller applications</p>
        </div>

        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Seller Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Submitted</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="pending-sellers-table">
              <?php if (empty($pendingSellers)): ?>
                <tr>
                  <td colspan="6" class="empty-state">
                    <div class="empty-state-icon">✅</div>
                    <div class="empty-state-text">No pending seller verifications</div>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($pendingSellers as $seller): ?>
                  <tr id="seller-row-<?php echo $seller['application_id']; ?>">
                    <td>
                      <strong><?php echo htmlspecialchars($seller['name'] ?? 'Unknown'); ?></strong>
                    </td>
                    <td><?php echo htmlspecialchars($seller['email'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($seller['phone_number'] ?? 'N/A'); ?></td>
                    <td><?php echo date('M d, Y', strtotime($seller['submitted_at'])); ?></td>
                    <td><span class="status-badge <?php echo $seller['status']; ?>"><?php echo ucfirst($seller['status']); ?></span></td>
                    <td>
                      <div class="btn-group">
                        <button class="btn-approve" onclick="verifySeller(<?php echo $seller['application_id']; ?>, 'approved')">
                          ✓ Approve
                        </button>
                        <button class="btn-decline" onclick="verifySeller(<?php echo $seller['application_id']; ?>, 'rejected')">
                          ✗ Decline
                        </button>
                        <a href="pending_seller_applications.php" class="btn-view">
                          👁️ View Details
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Buyers Section -->
      <section id="buyers" class="section">
        <div class="section-header">
          <h2 class="section-title">
            <span>🛒</span>
            <span>Buyers</span>
          </h2>
          <p class="section-subtitle">View all registered buyers on the platform</p>
        </div>

        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Registered</th>
              </tr>
            </thead>
            <tbody id="buyers-table">
              <tr>
                <td colspan="4" class="empty-state">
                  <div class="empty-state-icon">🛒</div>
                  <div class="empty-state-text">No buyers registered yet</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

  <script>
    // Section navigation
    document.querySelectorAll('.sidebar-link').forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const sectionId = link.dataset.section;
        showSection(sectionId);
      });
    });

    function showSection(sectionId) {
      // Hide all sections
      document.querySelectorAll('.section').forEach(section => {
        section.classList.remove('active');
      });

      // Show selected section
      document.getElementById(sectionId).classList.add('active');

      // Update sidebar
      document.querySelectorAll('.sidebar-link').forEach(link => {
        link.classList.remove('active');
        if (link.dataset.section === sectionId) {
          link.classList.add('active');
        }
      });

      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }

    function toggleSellerDetails(userId) {
      const details = document.getElementById('details-' + userId);
      if (details) {
        if (details.style.display === 'none' || !details.style.display) {
          details.style.display = 'block';
        } else {
          details.style.display = 'none';
        }
      }
    }

    function verifySeller(applicationId, status) {
      const action = status === 'approved' ? 'approve' : 'reject';
      const actionTitle = status === 'approved' ? 'Approve Seller?' : 'Reject Application?';
      const actionText = status === 'approved' 
        ? 'This seller will be granted access to sell on ThriftHub.' 
        : 'This application will be rejected and the seller will be notified.';
      
      Swal.fire({
        title: actionTitle,
        text: actionText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: status === 'approved' ? '#2E7D32' : '#D32F2F',
        cancelButtonColor: '#6B6B6B',
        confirmButtonText: status === 'approved' ? 'Yes, Approve' : 'Yes, Reject',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (!result.isConfirmed) return;

        // Show loading
        Swal.fire({
          title: 'Processing...',
          text: 'Please wait',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        const url = status === 'approved' ? '../actions/approve_seller.php' : '../actions/reject_seller.php';
        const formData = new FormData();
        formData.append('application_id', applicationId);

        fetch(url, {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: data.message || `Seller ${action}d successfully!`,
              confirmButtonColor: '#0F5E4D'
            }).then(() => {
              // Remove row from table
              const row = document.getElementById('seller-row-' + applicationId);
              if (row) {
                row.remove();
              }
              // Update pending count
              updatePendingCount();
              // Reload if no more pending sellers
              const tbody = document.getElementById('pending-sellers-table');
              if (tbody.querySelectorAll('tr[id^="seller-row-"]').length === 0) {
                location.reload();
              }
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: data.message || `Failed to ${action} seller. Please try again.`,
              confirmButtonColor: '#D32F2F'
            });
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: `An error occurred while ${action}ing the seller.`,
            confirmButtonColor: '#D32F2F'
          });
        });
      });
    }

    function updatePendingCount() {
      const tbody = document.getElementById('pending-sellers-table');
      const count = tbody.querySelectorAll('tr[id^="seller-row-"]').length;
      const badge = document.getElementById('pending-count');
      const statValue = document.getElementById('pending-sellers');
      
      if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline-block';
      } else {
        badge.style.display = 'none';
      }
      
      if (statValue) {
        statValue.textContent = count;
      }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
      updatePendingCount();
      const pendingCount = <?php echo count($pendingSellers); ?>;
      document.getElementById('pending-sellers').textContent = pendingCount;

      // Fetch dynamic admin stats
      fetch('../actions/get_admin_stats.php')
        .then(r => r.json())
        .then(data => {
          if (!data.success) return;
          const c = data.counts || {};
          document.getElementById('active-sellers').textContent = c.active_sellers ?? 0;
          document.getElementById('active-buyers').textContent = c.active_buyers ?? 0;
          document.getElementById('total-items').textContent = c.total_items ?? 0;
          document.getElementById('total-orders').textContent = c.total_orders ?? 0;

          // Recent sellers
          const rs = data.recent?.sellers || [];
          const rsTable = document.getElementById('recent-sellers-table');
          rsTable.innerHTML = rs.length ? rs.map(s => `
            <tr>
              <td>${s.name ? s.name : 'Unknown'}</td>
              <td>${s.email ? s.email : ''}</td>
              <td><span class="status-badge ${s.verified==1?'approved':'pending'}">${s.verified==1?'Approved':'Pending'}</span></td>
              <td>${s.date ? new Date(s.date).toLocaleDateString() : ''}</td>
            </tr>
          `).join('') : `
            <tr><td colspan="4" class="empty-state"><div class="empty-state-text">No sellers yet</div></td></tr>
          `;

          // Recent buyers
          const rb = data.recent?.buyers || [];
          const rbTable = document.getElementById('recent-buyers-table');
          rbTable.innerHTML = rb.length ? rb.map(b => `
            <tr>
              <td>${b.name ? b.name : 'Unknown'}</td>
              <td>${b.email ? b.email : ''}</td>
              <td>${b.date ? new Date(b.date).toLocaleDateString() : ''}</td>
            </tr>
          `).join('') : `
            <tr><td colspan="3" class="empty-state"><div class="empty-state-text">No buyers yet</div></td></tr>
          `;
        })
        .catch(err => console.error('Admin stats error:', err));
    });
  </script>
</body>

</html>
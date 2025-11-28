<?php
/**
 * Pending Seller Applications View
 * ThriftHub - Admin Dashboard for Seller Verification
 * 
 * Displays pending seller applications with documentation CSV rendering
 */

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

$controller = new SellerApplicationController();
$result = $controller->get_pending_applications_ctrl();

$applications = $result['success'] ? $result['applications'] : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Verification - ThriftHub Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            --border: #E8E3D8;
            --success: #2E7D32;
            --error: #D32F2F;
            --warning: #F57C00;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #F6F2EA 0%, #E8E3D8 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .page-header {
            background: var(--white);
            padding: 24px 32px;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(15, 94, 77, 0.08);
            margin-bottom: 32px;
            border: 1px solid var(--border);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--thrift-green);
            text-decoration: none;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .back-link:hover {
            background: var(--beige);
        }

        .stats-row {
            display: flex;
            gap: 16px;
            margin-top: 16px;
        }

        .stat-card {
            flex: 1;
            background: var(--beige);
            padding: 16px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            background: var(--thrift-green);
            color: var(--white);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-content h4 {
            font-size: 24px;
            font-weight: 700;
            color: var(--thrift-green);
        }

        .stat-content p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 2px;
        }
        
        h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-dark);
        }
        
        .subtitle {
            color: var(--text-muted);
            font-size: 15px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .no-applications {
            background: var(--white);
            padding: 80px 40px;
            text-align: center;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(15, 94, 77, 0.08);
        }

        .no-applications i {
            font-size: 64px;
            color: var(--border);
            margin-bottom: 16px;
        }

        .no-applications h3 {
            font-size: 20px;
            color: var(--text-muted);
            font-weight: 600;
        }
        
        .application-card {
            background: var(--white);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: 0 2px 12px rgba(15, 94, 77, 0.08);
            border: 1px solid var(--border);
            transition: all 0.3s;
        }

        .application-card:hover {
            box-shadow: 0 4px 20px rgba(15, 94, 77, 0.12);
            transform: translateY(-2px);
        }
        
        .app-header {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 24px;
            align-items: start;
            margin-bottom: 28px;
            padding-bottom: 24px;
            border-bottom: 2px solid var(--beige);
        }

        .app-avatar {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, var(--thrift-green), var(--thrift-green-light));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: var(--white);
            font-weight: 700;
        }
        
        .app-info h3 {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .app-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 8px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .meta-item i {
            color: var(--thrift-green);
            font-size: 16px;
        }

        .app-badge {
            background: var(--warning);
            color: var(--white);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .docs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .doc-card {
            background: var(--beige);
            padding: 20px;
            border-radius: 12px;
            border: 2px solid var(--border);
            transition: all 0.3s;
            cursor: pointer;
        }

        .doc-card:hover {
            border-color: var(--thrift-green);
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(15, 94, 77, 0.15);
        }

        .doc-icon {
            width: 56px;
            height: 56px;
            background: var(--white);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 12px;
        }

        .doc-title {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 4px;
            font-size: 15px;
        }

        .doc-filename {
            font-size: 12px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .doc-view-btn {
            margin-top: 12px;
            padding: 8px 16px;
            background: var(--thrift-green);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .doc-view-btn:hover {
            background: var(--thrift-green-dark);
        }

        .payment-section {
            background: var(--beige);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .payment-title {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .payment-item {
            background: var(--white);
            padding: 16px;
            border-radius: 8px;
        }

        .payment-label {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .payment-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
        
        .btn {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-approve {
            background: var(--success);
            color: var(--white);
        }
        
        .btn-approve:hover {
            background: #1B5E20;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
        }
        
        .btn-reject {
            background: var(--error);
            color: var(--white);
        }
        
        .btn-reject:hover {
            background: #B71C1C;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(211, 47, 47, 0.3);
        }
        
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .message {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: none;
            align-items: center;
            gap: 12px;
            font-weight: 600;
        }
        
        .message.success {
            background: #E8F5E9;
            color: var(--success);
            border: 2px solid var(--success);
        }
        
        .message.error {
            background: #FFEBEE;
            color: var(--error);
            border: 2px solid var(--error);
        }

        .message i {
            font-size: 20px;
        }

        /* Document Preview Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: var(--white);
            margin: 40px auto;
            padding: 0;
            border-radius: 16px;
            max-width: 90%;
            max-height: 90vh;
            position: relative;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 2px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .modal-close {
            width: 36px;
            height: 36px;
            background: var(--beige);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--text-dark);
            transition: all 0.3s;
        }

        .modal-close:hover {
            background: var(--error);
            color: var(--white);
        }

        .modal-body {
            padding: 24px;
            max-height: calc(90vh - 100px);
            overflow: auto;
        }

        .modal-body img {
            max-width: 100%;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        }

        .modal-body iframe {
            width: 100%;
            height: 70vh;
            border: none;
            border-radius: 12px;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .application-card {
            animation: slideIn 0.4s ease-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div class="header-top">
                <div>
                    <h1>Seller Verification Center</h1>
                    <p class="subtitle">Review and approve seller applications</p>
                </div>
                <a href="admin_dashboard.php" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
            
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h4 id="pending-count">0</h4>
                        <p>Pending Applications</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h4 id="docs-count">0</h4>
                        <p>Documents to Review</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="message" class="message"></div>
        
        <?php if (empty($applications)): ?>
            <div class="no-applications">
                <i class="fas fa-check-circle"></i>
                <h3>No Pending Applications</h3>
                <p>All seller applications have been reviewed</p>
            </div>
        <?php else: ?>
            <?php foreach ($applications as $app): ?>
                <div class="application-card" id="app-<?php echo $app['application_id']; ?>">
                    <div class="app-header">
                        <div class="app-avatar">
                            <?php echo strtoupper(substr($app['name'] ?? 'U', 0, 2)); ?>
                        </div>
                        
                        <div class="app-info">
                            <h3><?php echo htmlspecialchars($app['name'] ?? 'Unknown'); ?></h3>
                            <div class="app-meta">
                                <span class="meta-item">
                                    <i class="fas fa-envelope"></i>
                                    <?php echo htmlspecialchars($app['email'] ?? 'No email'); ?>
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-phone"></i>
                                    <?php echo htmlspecialchars($app['phone_number'] ?? 'No phone'); ?>
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('M d, Y g:i A', strtotime($app['submitted_at'])); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="app-badge">
                            <i class="fas fa-hourglass-half"></i>
                            Pending Review
                        </div>
                    </div>
                    
                    <?php
                    // Read and parse CSV file
                    $csvPath = __DIR__ . '/../' . $app['documentation_path'];
                    $csvRow = null;
                    if (file_exists($csvPath)):
                        if (($handle = fopen($csvPath, 'r')) !== false):
                            $headers = fgetcsv($handle);
                            if (($row = fgetcsv($handle)) !== false):
                                $csvRow = array_combine($headers, $row);
                            endif;
                            fclose($handle);
                        endif;
                    endif;
                    ?>
                    
                    <?php if ($csvRow): ?>
                        <div class="docs-grid">
                            <?php if (!empty($csvRow['id_path'])): ?>
                                <div class="doc-card" onclick="viewDocument('../<?php echo htmlspecialchars($csvRow['id_path']); ?>', 'ID Document')">
                                    <div class="doc-icon">🪪</div>
                                    <div class="doc-title">ID Document</div>
                                    <div class="doc-filename"><?php echo basename($csvRow['id_path']); ?></div>
                                    <button class="doc-view-btn" type="button">
                                        <i class="fas fa-eye"></i>
                                        View Document
                                    </button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($csvRow['address_path'])): ?>
                                <div class="doc-card" onclick="viewDocument('../<?php echo htmlspecialchars($csvRow['address_path']); ?>', 'Address Proof')">
                                    <div class="doc-icon">📍</div>
                                    <div class="doc-title">Address Proof</div>
                                    <div class="doc-filename"><?php echo basename($csvRow['address_path']); ?></div>
                                    <button class="doc-view-btn" type="button">
                                        <i class="fas fa-eye"></i>
                                        View Document
                                    </button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($csvRow['selfie_path'])): ?>
                                <div class="doc-card" onclick="viewDocument('../<?php echo htmlspecialchars($csvRow['selfie_path']); ?>', 'Selfie Verification')">
                                    <div class="doc-icon">🤳</div>
                                    <div class="doc-title">Selfie Verification</div>
                                    <div class="doc-filename"><?php echo basename($csvRow['selfie_path']); ?></div>
                                    <button class="doc-view-btn" type="button">
                                        <i class="fas fa-eye"></i>
                                        View Document
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="payment-section">
                            <div class="payment-title">
                                <i class="fas fa-credit-card"></i>
                                Payment Information
                            </div>
                            <div class="payment-grid">
                                <div class="payment-item">
                                    <div class="payment-label">Mobile Money Number</div>
                                    <div class="payment-value"><?php echo htmlspecialchars($csvRow['momo_number'] ?? 'N/A'); ?></div>
                                </div>
                                <div class="payment-item">
                                    <div class="payment-label">Bank Name</div>
                                    <div class="payment-value"><?php echo htmlspecialchars($csvRow['bank_name'] ?? 'N/A'); ?></div>
                                </div>
                                <div class="payment-item">
                                    <div class="payment-label">Account Number</div>
                                    <div class="payment-value"><?php echo htmlspecialchars($csvRow['account_number'] ?? 'N/A'); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="actions">
                        <button class="btn btn-approve" onclick="processApplication(<?php echo $app['application_id']; ?>, 'approve')">
                            <i class="fas fa-check-circle"></i>
                            Approve Seller
                        </button>
                        <button class="btn btn-reject" onclick="processApplication(<?php echo $app['application_id']; ?>, 'reject')">
                            <i class="fas fa-times-circle"></i>
                            Reject Application
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Document Preview Modal -->
    <div id="docModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Document Preview</h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Document content will be loaded here -->
            </div>
        </div>
    </div>
    
    <script>
        // Update stats on page load
        document.addEventListener('DOMContentLoaded', function() {
            const appCards = document.querySelectorAll('.application-card');
            const pendingCount = appCards.length;
            const docsCount = pendingCount * 3; // Each application has 3 documents
            
            document.getElementById('pending-count').textContent = pendingCount;
            document.getElementById('docs-count').textContent = docsCount;
            
            console.log('Loaded ' + pendingCount + ' pending applications');
        });

        // Document preview modal
        function viewDocument(path, title) {
            const modal = document.getElementById('docModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');
            
            modalTitle.textContent = title;
            
            // Check file extension
            const ext = path.split('.').pop().toLowerCase();
            
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                modalBody.innerHTML = `<img src="${path}" alt="${title}">`;
            } else if (ext === 'pdf') {
                modalBody.innerHTML = `<iframe src="${path}"></iframe>`;
            } else {
                modalBody.innerHTML = `
                    <div style="text-align: center; padding: 40px;">
                        <i class="fas fa-file" style="font-size: 64px; color: #ccc; margin-bottom: 16px;"></i>
                        <p>Preview not available for this file type</p>
                        <a href="${path}" target="_blank" class="btn btn-approve" style="display: inline-flex; margin-top: 16px;">
                            <i class="fas fa-external-link-alt"></i>
                            Open in New Tab
                        </a>
                    </div>
                `;
            }
            
            modal.style.display = 'block';
        }

        function closeModal() {
            document.getElementById('docModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('docModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
        
        function processApplication(applicationId, action) {
            const actionTitle = action === 'approve' ? 'Approve Seller?' : 'Reject Application?';
            const actionText = action === 'approve' 
                ? 'This seller will be granted access to sell on ThriftHub.' 
                : 'This application will be rejected and the seller will be notified.';
            
            Swal.fire({
                title: actionTitle,
                text: actionText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'approve' ? '#2E7D32' : '#D32F2F',
                cancelButtonColor: '#6B6B6B',
                confirmButtonText: action === 'approve' ? 'Yes, Approve' : 'Yes, Reject',
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
                
                const url = action === 'approve' ? '../actions/approve_seller.php' : '../actions/reject_seller.php';
                const formData = new FormData();
                formData.append('application_id', applicationId);
                
                // Disable buttons
                const card = document.getElementById(`app-${applicationId}`);
                const buttons = card.querySelectorAll('.btn');
                buttons.forEach(btn => btn.disabled = true);
                
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
                            text: data.message || `Application ${action}d successfully!`,
                            confirmButtonColor: '#0F5E4D'
                        }).then(() => {
                            // Remove the card after successful processing
                            card.style.opacity = '0';
                            setTimeout(() => card.remove(), 300);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || `Failed to ${action} application.`,
                            confirmButtonColor: '#D32F2F'
                        });
                        // Re-enable buttons on error
                        buttons.forEach(btn => btn.disabled = false);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: `An error occurred while ${action}ing the application.`,
                        confirmButtonColor: '#D32F2F'
                    });
                    // Re-enable buttons
                    buttons.forEach(btn => btn.disabled = false);
                });
            });
        }
    </script>
</body>
</html>

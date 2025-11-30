<?php
require_once __DIR__ . '/../settings/db_class.php';

class AdminController extends Database {
    public function getCounts() {
        // Active sellers: verified sellers in sellers table or users with role seller and verified flag
        $activeSellers = 0;
        $row = $this->fetchOne("SELECT COUNT(*) AS c FROM sellers WHERE verified = 1");
        if ($row) { $activeSellers = (int)$row['c']; }

        // Active buyers: all customers registered on platform
        $buyers = 0;
        $row = $this->fetchOne("SELECT COUNT(*) AS c FROM customers");
        if ($row) { $buyers = (int)$row['c']; }

        // Total items: products table, status active
        $totalItems = 0;
        $row = $this->fetchOne("SELECT COUNT(*) AS c FROM products WHERE product_status='active'");
        if ($row) { $totalItems = (int)$row['c']; }

        // Total orders: all orders
        $totalOrders = 0;
        $row = $this->fetchOne("SELECT COUNT(*) AS c FROM orders");
        if ($row) { $totalOrders = (int)$row['c']; }

        // Pending verifications: seller applications pending
        $pending = 0;
        $row = $this->fetchOne("SELECT COUNT(*) AS c FROM seller_applications WHERE status='pending'");
        if ($row) { $pending = (int)$row['c']; }

        return [
            'success' => true,
            'data' => [
                'active_sellers' => $activeSellers,
                'active_buyers' => $buyers,
                'total_items' => $totalItems,
                'total_orders' => $totalOrders,
                'pending_verifications' => $pending,
            ]
        ];
    }

    public function getRecentUsers($limit = 5) {
        $limit = (int)$limit;
        // Recent sellers: get from seller_applications to include status
        $recentSellers = $this->fetchAll("SELECT u.name, u.email, sa.status, sa.submitted_at AS date
                                          FROM seller_applications sa
                                          JOIN users u ON sa.user_id = u.user_id
                                          ORDER BY sa.submitted_at DESC
                                          LIMIT $limit");
        // Recent buyers/customers: join customers to users for complete info
        $recentBuyers = $this->fetchAll("SELECT u.name, u.email, u.created_at AS date
                                         FROM customers c
                                         JOIN users u ON c.user_id = u.user_id
                                         ORDER BY u.created_at DESC
                                         LIMIT $limit");
        return [
            'success' => true,
            'sellers' => $recentSellers,
            'buyers' => $recentBuyers
        ];
    }

    public function listActiveBuyers($limit = 50, $offset = 0) {
        $limit = (int)$limit; $offset = (int)$offset;
        $rows = $this->fetchAll("SELECT name, email, phone_number, created_at
                                 FROM users
                                 WHERE user_role='customer'
                                 ORDER BY created_at DESC
                                 LIMIT $offset, $limit");
        return ['success' => true, 'data' => $rows];
    }
}

?>

<?php
/**
 * Recommendation Class
 * Provides product recommendation methods (content-based, collaborative, popularity, user-personalized)
 */

require_once __DIR__ . '/../settings/db_class.php';

class Recommendation extends Database {
    // Cached TF-IDF vectors for products
    private static $productVectors = null; // [product_id => [term => weight]]
    private static $vocabulary = [];       // [term => document_frequency]
    private static $lastBuildTime = 0;     // timestamp of last build
    private static $cacheTTL = 300;        // seconds to keep vectors (5 min)

    /**
     * Build / refresh TF-IDF vectors lazily
     */
    private function buildVectorsIfNeeded() {
        // Rebuild if cache empty or expired
        if (self::$productVectors !== null && (time() - self::$lastBuildTime) < self::$cacheTTL) {
            return; // still fresh
        }

        self::$productVectors = [];
        self::$vocabulary = [];

        // Fetch minimal product data (active only)
        $products = $this->fetchAll("SELECT product_id, product_title, product_keywords, product_desc FROM products WHERE product_status='active'");
        $docCount = count($products);
        if ($docCount === 0) { return; }

        $tokenizedProducts = []; // temporary storage of term counts per product

        foreach ($products as $p) {
            $pid = (int)$p['product_id'];
            $textParts = [];
            if (!empty($p['product_title'])) { $textParts[] = $p['product_title']; }
            if (!empty($p['product_keywords'])) { $textParts[] = $p['product_keywords']; }
            if (!empty($p['product_desc'])) { $textParts[] = $p['product_desc']; }
            $rawText = strtolower(implode(' ', $textParts));

            // Basic tokenization: split on non-alphanumeric, remove short tokens, simple stopwords
            $tokens = preg_split('/[^a-z0-9]+/i', $rawText);
            $stop = ['the','and','for','with','from','this','that','you','your','our','are','was','were','will','have','has','had','but','not','all','any','can','new','used','good'];
            $counts = [];
            foreach ($tokens as $t) {
                $t = trim($t);
                if ($t === '' || strlen($t) < 3) { continue; }
                if (in_array($t, $stop)) { continue; }
                $counts[$t] = isset($counts[$t]) ? $counts[$t] + 1 : 1;
            }
            $tokenizedProducts[$pid] = $counts;
            // Update vocabulary document frequency
            foreach (array_keys($counts) as $term) {
                self::$vocabulary[$term] = isset(self::$vocabulary[$term]) ? self::$vocabulary[$term] + 1 : 1;
            }
        }

        // Compute TF-IDF vectors
        foreach ($tokenizedProducts as $pid => $counts) {
            $vector = [];
            $maxTf = 0;
            foreach ($counts as $tf) { if ($tf > $maxTf) { $maxTf = $tf; } }
            if ($maxTf === 0) { continue; }
            foreach ($counts as $term => $tf) {
                $df = self::$vocabulary[$term];
                // log smoothing: idf = log((N+1)/(df+1)) + 1
                $idf = log(($docCount + 1) / ($df + 1)) + 1;
                // normalized term frequency (0.5 + 0.5 * tf/maxTf) improves discrimination
                $ntf = 0.5 + (0.5 * $tf / $maxTf);
                $vector[$term] = $ntf * $idf;
            }
            self::$productVectors[$pid] = $vector;
        }

        self::$lastBuildTime = time();
    }

    /**
     * Cosine similarity between two sparse vectors
     */
    private function cosineSimilarity($v1, $v2) {
        if (empty($v1) || empty($v2)) { return 0.0; }
        $dot = 0.0; $norm1 = 0.0; $norm2 = 0.0;
        foreach ($v1 as $t => $w) { $norm1 += $w * $w; }
        foreach ($v2 as $t => $w) { $norm2 += $w * $w; }
        // Iterate over smaller vector for efficiency
        if (count($v1) < count($v2)) {
            foreach ($v1 as $t => $w) {
                if (isset($v2[$t])) { $dot += $w * $v2[$t]; }
            }
        } else {
            foreach ($v2 as $t => $w) {
                if (isset($v1[$t])) { $dot += $w * $v1[$t]; }
            }
        }
        $den = (sqrt($norm1) * sqrt($norm2));
        return $den > 0 ? $dot / $den : 0.0;
    }

    /**
     * Get similar products (content-based)
     * @param int $productId
     * @param int $limit
     * @return array
     */
    public function getSimilarProducts($productId, $limit = 10) {
        $productId = (int)$productId;
        if ($productId <= 0) { return []; }
        $this->buildVectorsIfNeeded();
        if (!isset(self::$productVectors[$productId])) { return []; }
        $targetVec = self::$productVectors[$productId];
        $scores = [];
        foreach (self::$productVectors as $pid => $vec) {
            if ($pid === $productId) { continue; }
            $scores[$pid] = $this->cosineSimilarity($targetVec, $vec);
        }
        arsort($scores); // highest similarity first
        $topIds = array_slice(array_keys($scores), 0, $limit);
        if (empty($topIds)) { return []; }
        $idList = implode(',', array_map('intval', $topIds));
        $sql = "SELECT product_id, product_title, product_price, product_image, product_cat, product_brand FROM products WHERE product_id IN ($idList)";
        $rows = $this->fetchAll($sql);
        // Attach similarity
        foreach ($rows as &$r) { $pid = (int)$r['product_id']; $r['similarity'] = isset($scores[$pid]) ? round($scores[$pid], 4) : 0; }
        // Sort rows by similarity (in case DB reorders)
        usort($rows, function($a,$b){ return $b['similarity'] <=> $a['similarity']; });
        return $rows;
    }

    /**
     * Get trending / popular products (recent sales volume)
     */
    public function getTrendingProducts($limit = 10, $days = 30) {
        $days = (int)$days; $limit = (int)$limit;
        $sql = "SELECT p.product_id, p.product_title, p.product_price, p.product_image, SUM(od.qty) as total_qty
                FROM orderdetails od
                JOIN orders o ON od.order_id = o.order_id
                JOIN products p ON od.product_id = p.product_id
                WHERE p.product_status='active'
                  AND o.order_status IN ('paid','shipped','completed','delivered')
                  AND o.order_date >= DATE_SUB(NOW(), INTERVAL $days DAY)
                GROUP BY p.product_id
                ORDER BY total_qty DESC, p.product_id DESC
                LIMIT $limit";
        return $this->fetchAll($sql);
    }

    /**
     * Get products frequently bought together with given product (also-bought)
     */
    public function getAlsoBought($productId, $limit = 10) {
        $productId = (int)$productId; $limit = (int)$limit;
        if ($productId <= 0) { return []; }
        $sql = "SELECT od2.product_id, SUM(od2.qty) as total_qty
                FROM orderdetails od
                JOIN orders o ON od.order_id = o.order_id
                JOIN orderdetails od2 ON od.order_id = od2.order_id AND od2.product_id <> od.product_id
                WHERE od.product_id = $productId
                  AND o.order_status IN ('paid','shipped','completed','delivered')
                GROUP BY od2.product_id
                ORDER BY total_qty DESC
                LIMIT $limit";
        $rows = $this->fetchAll($sql);
        if (empty($rows)) { return []; }
        $ids = implode(',', array_map(function($r){ return (int)$r['product_id']; }, $rows));
        $details = $this->fetchAll("SELECT product_id, product_title, product_price, product_image FROM products WHERE product_id IN ($ids) AND product_status='active'");
        // Map qty
        $qtyMap = [];
        foreach ($rows as $r) { $qtyMap[(int)$r['product_id']] = (int)$r['total_qty']; }
        foreach ($details as &$d) { $pid = (int)$d['product_id']; $d['co_purchase_qty'] = $qtyMap[$pid] ?? 0; }
        // Sort by co_purchase_qty
        usort($details, function($a,$b){ return $b['co_purchase_qty'] <=> $a['co_purchase_qty']; });
        return $details;
    }

    /**
     * Personalized user recommendations based on category affinity
     */
    public function getUserRecommendations($customerId, $limit = 10) {
        $customerId = (int)$customerId; $limit = (int)$limit;
        if ($customerId <= 0) { return []; }
        // Category affinity (past purchases)
        $affSql = "SELECT p.product_cat, SUM(od.qty) as qty
                   FROM orders o
                   JOIN orderdetails od ON o.order_id = od.order_id
                   JOIN products p ON od.product_id = p.product_id
                   WHERE o.customer_id = $customerId
                                         AND o.order_status IN ('paid','shipped','completed','delivered')
                   GROUP BY p.product_cat
                   ORDER BY qty DESC";
        $affRows = $this->fetchAll($affSql);
        if (empty($affRows)) {
            // Fallback: return trending if no history
            return $this->getTrendingProducts($limit, 30);
        }
        $catIds = array_map(function($r){ return (int)$r['product_cat']; }, $affRows);
        $catList = implode(',', $catIds);
        // Already purchased products to exclude
        $purchasedRows = $this->fetchAll("SELECT DISTINCT od.product_id FROM orders o JOIN orderdetails od ON o.order_id=od.order_id WHERE o.customer_id=$customerId AND o.order_status IN ('paid','shipped','completed','delivered')");
        $excludeIds = array_map(function($r){ return (int)$r['product_id']; }, $purchasedRows);
        $excludeClause = '';
        if (!empty($excludeIds)) { $excludeClause = 'AND p.product_id NOT IN (' . implode(',', $excludeIds) . ')'; }
        // Select new products in favorite categories (recent first, price ascending secondary)
        $sql = "SELECT p.product_id, p.product_title, p.product_price, p.product_image, p.product_cat
                FROM products p
                WHERE p.product_status='active'
                  AND p.product_cat IN ($catList)
                  $excludeClause
                ORDER BY FIELD(p.product_cat, $catList), p.created_at DESC, p.product_price ASC
                LIMIT $limit";
                $rows = $this->fetchAll($sql);
                // If no candidates left after exclusion, fallback to trending
                if (empty($rows)) {
                        return $this->getTrendingProducts($limit, 30);
                }
                return $rows;
    }

    /**
     * (Optional) Log browsing event stub - requires browsing_events table
     */
    public function logBrowseEvent($customerId, $productId) {
        $customerId = (int)$customerId; $productId = (int)$productId;
        if ($productId <= 0) { return false; }
        // If customerId not logged in, skip
        if ($customerId <= 0) { return false; }
        // Table must exist; silent fail if not
        $sql = "INSERT INTO browsing_events (customer_id, product_id) VALUES ($customerId, $productId)";
        $this->query($sql); // ignore result
        return true;
    }
}

?>

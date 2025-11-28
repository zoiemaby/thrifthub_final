<?php
/**
 * Recommendation Controller
 * Wraps Recommendation class methods and prepares JSON-friendly responses
 */

require_once __DIR__ . '/../classes/recommendation_class.php';

class RecommendationController {
    private $rec;

    public function __construct() {
        $this->rec = new Recommendation();
    }

    private function success($data) {
        return [
            'success' => true,
            'count' => count($data),
            'data' => $data
        ];
    }

    private function error($code, $message) {
        return [
            'success' => false,
            'error' => $code,
            'message' => $message
        ];
    }

    public function similar($productId, $limit = 10) {
        $productId = (int)$productId;
        $limit = (int)$limit;
        if ($productId <= 0) { return $this->error('invalid_input','Invalid product id'); }
        $data = $this->rec->getSimilarProducts($productId, $limit);
        return $this->success($data);
    }

    public function trending($limit = 10, $days = 30) {
        $limit = (int)$limit; $days = (int)$days;
        $data = $this->rec->getTrendingProducts($limit, $days);
        return $this->success($data);
    }

    public function alsoBought($productId, $limit = 10) {
        $productId = (int)$productId; $limit = (int)$limit;
        if ($productId <= 0) { return $this->error('invalid_input','Invalid product id'); }
        $data = $this->rec->getAlsoBought($productId, $limit);
        return $this->success($data);
    }

    public function userRecommendations($customerId, $limit = 10) {
        $customerId = (int)$customerId; $limit = (int)$limit;
        if ($customerId <= 0) { return $this->error('invalid_input','Invalid customer id'); }
        $data = $this->rec->getUserRecommendations($customerId, $limit);
        return $this->success($data);
    }
}

?>

<?php
require_once __DIR__ . '/../controllers/recommendation_controller.php';

header('Content-Type: application/json');

$customerId = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

$controller = new RecommendationController();
$result = $controller->userRecommendations($customerId, $limit);
echo json_encode($result);
?>

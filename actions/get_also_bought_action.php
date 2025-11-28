<?php
require_once __DIR__ . '/../controllers/recommendation_controller.php';

header('Content-Type: application/json');

$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

$controller = new RecommendationController();
$result = $controller->alsoBought($productId, $limit);
echo json_encode($result);
?>

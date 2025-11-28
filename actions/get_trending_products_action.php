<?php
require_once __DIR__ . '/../controllers/recommendation_controller.php';

header('Content-Type: application/json');

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$days = isset($_GET['days']) ? (int)$_GET['days'] : 30;

$controller = new RecommendationController();
$result = $controller->trending($limit, $days);
echo json_encode($result);
?>

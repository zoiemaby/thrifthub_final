<?php
require_once __DIR__ . '/../controllers/admin_controller.php';

header('Content-Type: application/json');

$controller = new AdminController();
$counts = $controller->getCounts();
$recent = $controller->getRecentUsers(5);

echo json_encode([
    'success' => true,
    'counts' => $counts['data'],
    'recent' => [
        'sellers' => $recent['sellers'],
        'buyers' => $recent['buyers']
    ]
]);
?>

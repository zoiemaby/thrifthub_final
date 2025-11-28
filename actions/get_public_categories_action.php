<?php
/**
 * Get Public Categories Action
 * ThriftHub - Get All Categories for Public Use
 * 
 * Returns all categories for public browsing (no login required)
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/category_controller.php';

header('Content-Type: application/json; charset=utf-8');

$controller = new CategoryController();

// Get all categories (public access)
$result = $controller->getAllCategories();

http_response_code(200);
echo json_encode($result);
exit;


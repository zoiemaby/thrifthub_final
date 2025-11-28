<?php
/**
 * Get Public Brands Action
 * ThriftHub - Get All Brands for Public Use
 * 
 * Returns all brands for public browsing (no login required)
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

header('Content-Type: application/json; charset=utf-8');

$controller = new BrandController();

// Get all brands (public access)
$result = $controller->getAllBrands();

http_response_code(200);
echo json_encode($result);
exit;


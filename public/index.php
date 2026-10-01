<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Controllers/VehicleController.php';

// Global CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

$db = (new Database())->getConnection();
$controller = new VehicleController($db);

// Parsing URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriSegments = explode('/', trim($uri, '/'));

$requestMethod = $_SERVER['REQUEST_METHOD'];

// Route check: /vehicles or /vehicles/{id}
$resource = $uriSegments[0] ?? null;
$id = isset($uriSegments[1]) ? (int)$uriSegments[1] : null;

switch ($requestMethod) {
  case 'GET':
    $controller->index($_GET);
    break;

  case 'POST':
    $controller->store();
    break;
  
  case 'PUT':
    if ($id) {
      $controller->update($id);
    } else {
      http_response_code(400);
      echo json_encode(["message" => "Missing Vehicle ID for update."]);
    }
    break;

  case 'DELETE':
    if ($id) {
      $controller->destroy($id);
    } else {
      http_response_code(400);
      echo json_encode(["message" => "Missing Vehicle ID for deletion."]);
    }

    break;

  default: 
    http_response_code(405);
    echo json_encode(["message" => "Method Not Allowed"]);
    break;
}
<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-HTTP-Method-Override");
header("Content-Type: application/json");

require_once 'db.php';

// Allow method override via header for PATCH support in AJAX
$method = $_SERVER["REQUEST_METHOD"];
if ($method === "POST" && isset($_SERVER["HTTP_X_HTTP_METHOD_OVERRIDE"]) && $_SERVER["HTTP_X_HTTP_METHOD_OVERRIDE"] === "PATCH") {
  $method = "PATCH";
}

if ($method !== "PATCH") {
  echo json_encode(["status" => "error", "message" => "Invalid request method. Use PATCH."]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (empty($data["id"]) || empty($data["company_name"]) || empty($data["stage"])) {
  echo json_encode(["status" => "error", "message" => "ID, company name, and stage are required."]);
  exit;
}

$id = $data["id"];
$company_name = $data["company_name"];
$stage = $data["stage"];
$note = $data["note"] ?? "";

try {
  $stmt = $pdo->prepare("UPDATE service_requests SET company_name = ?, stage = ?, note = ? WHERE id = ?");
  $stmt->execute([$company_name, $stage, $note, $id]);

  echo json_encode([
    "status" => "success",
    "message" => "Service request updated successfully"
  ]);
} catch (PDOException $e) {
  echo json_encode([
    "status" => "error",
    "message" => "Error updating service request: " . $e->getMessage()
  ]);
}

<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
  echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in as admin."]);
  exit;
}

// Check the request method
if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
  echo json_encode(["status" => "error", "message" => "Invalid request method. Use DELETE."]);
  exit;
}

// Get ID from URL query string
if (!isset($_GET["id"])) {
  echo json_encode(["status" => "error", "message" => "Missing service request ID in URL."]);
  exit;
}
$id = $_GET["id"];

// Prepare the delete query
try {
  $stmt = $pdo->prepare("DELETE FROM service_requests WHERE id = ?");
  $stmt->execute([$id]);

  if ($stmt->rowCount() > 0) {
    echo json_encode(["status" => "success", "message" => "Service request deleted successfully"]);
  } else {
    echo json_encode(["status" => "error", "message" => "No service request found with the given ID"]);
  }
} catch (PDOException $e) {
  echo json_encode([
    "status" => "error",
    "message" => "Error deleting service request: " . $e->getMessage()
  ]);
}

<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
session_start();
require_once 'db.php';


if ($_SERVER["REQUEST_METHOD"] !== "GET") {
  echo json_encode(["status" => "error", "message" => "Invalid request method. Use GET."]);
  exit;
}

try {
  $stmt = $pdo->query("SELECT * FROM service_requests ORDER BY start_date DESC");
  $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    "status" => "success",
    "requests" => $requests
  ]);
} catch (PDOException $e) {
  echo json_encode([
    "status" => "error",
    "message" => "Error fetching service requests: " . $e->getMessage()
  ]);
}

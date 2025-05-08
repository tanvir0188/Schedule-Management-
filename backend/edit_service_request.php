<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
session_start();
require_once 'db.php';

if (!isset($_SESSION["user_id"])) {
  echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in as admin."]);
  exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  echo json_encode(["status" => "error", "message" => "Invalid request method. Use POST."]);
  exit;
}

// Get ID from URL query string
if (!isset($_GET["id"])) {
  echo json_encode(["status" => "error", "message" => "Missing service request ID in URL."]);
  exit;
}
$id = $_GET["id"];

$data = json_decode(file_get_contents("php://input"), true);

// Check required fields
if (
  empty($data["company_name"]) ||
  empty($data["start_date"]) ||
  empty($data["end_date"]) ||
  empty($data["stage"])
) {
  echo json_encode(["status" => "error", "message" => "Missing required fields."]);
  exit;
}

$company_name = $data["company_name"];
$start_date = $data["start_date"];
$end_date = $data["end_date"];
$stage = $data["stage"];
$note = $data["note"];
$ticket_status = $data["ticket_status"];
$service_person = $data["service_person"];
$current_duration = $data["current_duration"];

try {
  $stmt = $pdo->prepare("
    UPDATE service_requests 
    SET company_name = ?, start_date = ?, end_date = ?, stage = ?, note = ?, ticket_status = ?, service_person = ?, current_duration = ?
    WHERE id = ?
  ");
  $stmt->execute([$company_name, $start_date, $end_date, $stage, $note, $ticket_status, $service_person, $current_duration, $id]);

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

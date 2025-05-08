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

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data["company_name"]) || empty($data["start_date"]) || empty($data["end_date"]) || empty($data["stage"])) {
  echo json_encode(["status" => "error", "message" => "Username and password are required."]);
  exit;
}
$company_name =  $data["company_name"];
$start_date =  $data["start_date"];
$stage = $data["stage"];
$end_date = $data["end_date"];
$note = $data["note"];
$ticket_status = $data["ticket_status"];
$service_person = $data["service_person"];
$current_duration = $data["current_duration"];

try {
  $stmt = $pdo->prepare("INSERT INTO service_requests (company_name, start_date, stage, end_date, note, ticket_status, service_person, current_duration) values(?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$company_name, $start_date, $stage, $end_date, $note, $ticket_status, $service_person, $current_duration]);
  echo json_encode([
    "status" => "success",
    "message" => "New service request created"
  ]);
} catch (PDOException $e) {
  echo json_encode([
    "status" => "error",
    "message" => "Error creating new service request: " . $e->getMessage()
  ]);
}

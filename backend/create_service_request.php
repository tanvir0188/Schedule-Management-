<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  echo json_encode(["status" => "error", "message" => "Invalid request method. Use POST."]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (empty($data["company_name"]) || empty($data["start_date"]) || empty($data["stage"])) {
  echo json_encode(["status" => "error", "message" => "Company name, start date, and stage are required."]);
  exit;
}

$company_name = $data["company_name"];
$start_date = $data["start_date"];
$stage = $data["stage"];
$end_date = $data["end_date"] ?? ""; // Optional
$note = $data["note"] ?? "";
$ticket_status = $data["ticket_status"] ?? "pending";
$service_person = $data["service_person"] ?? "not assigned";

try {
  $stmt = $pdo->prepare("INSERT INTO service_requests (company_name, start_date, stage, end_date, note, ticket_status, service_person) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$company_name, $start_date, $stage, $end_date, $note, $ticket_status, $service_person]);

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

<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

session_start();
require_once 'db.php';

// Make sure user is logged in
if (!isset($_SESSION["user_id"])) {
  echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in."]);
  exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "PATCH") {
  echo json_encode(["status" => "error", "message" => "Invalid request method. Use PATCH."]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data["id"]) || empty($data["ticket_status"])) {
  echo json_encode(["status" => "error", "message" => "ID and ticket status are required."]);
  exit;
}

$id = $data["id"];
$service_person = $data["service_person"] ?? null;
$ticket_status = $data["ticket_status"];
$end_date = null;

// If status is "solved", set the end date to the current date
if ($ticket_status === "solved") {
  $end_date = date('Y-m-d');  // Get today's date
}

try {
  // Update only the modifiable fields
  $updateFields = ["ticket_status" => $ticket_status];
  if ($service_person) {
    $updateFields["service_person"] = $service_person;
  }
  if ($end_date) {
    $updateFields["end_date"] = $end_date;
  }

  // Prepare the SET clause of the SQL
  $setClause = [];
  $params = [];
  foreach ($updateFields as $field => $value) {
    $setClause[] = "$field = ?";
    $params[] = $value;
  }

  $params[] = $id;  // Add ID as the last parameter for WHERE clause

  // Prepare SQL statement to update the service request
  $stmt = $pdo->prepare("UPDATE service_requests SET " . implode(", ", $setClause) . " WHERE id = ?");
  $stmt->execute($params);

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

<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
  echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in."]);
  exit;
}

// Verify the logged-in user is admin
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$user = $stmt->fetch();

if (!$user || $user["username"] !== "admin") {
  echo json_encode(["status" => "error", "message" => "Only admin can access this endpoint."]);
  exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
  echo json_encode(["status" => "error", "message" => "Invalid request method. Use GET."]);
  exit;
}

try {
  // This will include the hashed passwords as stored in the database
  $stmt = $pdo->query("SELECT id, username, password FROM users ORDER BY username ASC");
  $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    "status" => "success",
    "users" => $users
  ]);
} catch (PDOException $e) {
  echo json_encode([
    "status" => "error",
    "message" => "Error fetching users: " . $e->getMessage()
  ]);
}
